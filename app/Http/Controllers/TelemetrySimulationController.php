<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TelemetrySimulationController extends Controller
{
    public function __construct(
        private AuditService $audit,
    ) {}

    public function simulate(Request $request): JsonResponse
    {
        Gate::authorize('manage-generations');

        $validated = $request->validate([
            'solar_farm_id' => ['required', 'integer', 'exists:solar_farms,id'],
            'scenario' => ['required', 'string', 'in:optimal,degraded,critical_alert,severe_storm'],
        ]);

        $farm = SolarFarm::with(['department', 'solarPanels'])->findOrFail($validated['solar_farm_id']);

        $capacityKw = max(50.0, (float) $farm->calculated_capacity_kw);
        $period = now()->format('Y-m');

        // Cálculo base: Capacidad * 5.3 h sol * 30 días * 0.80 PR * Factor estacional (0.88 en época lluviosa)
        $month = (int) now()->format('m');
        $seasonalFactor = ($month >= 5 && $month <= 10) ? 0.88 : 1.20;
        $estimatedKwh = round($capacityKw * 5.3 * 30 * 0.80 * $seasonalFactor, 2);

        // Escenarios de rendimiento en tiempo real
        $scenarioData = match ($validated['scenario']) {
            'optimal' => [
                'ratio' => 0.99,
                'label' => 'Operación Óptima (100% de paneles activos)',
                'notes' => 'Inversores operando a máxima eficiencia fotovoltaica. Sin novedades.',
            ],
            'degraded' => [
                'ratio' => 0.90,
                'label' => 'Degradación Leve (10% paneles con sombra/polvo)',
                'notes' => 'Pérdida por suciedad y alta temperatura en módulos. Dentro del margen admisible.',
            ],
            'critical_alert' => [
                'ratio' => 0.74,
                'label' => 'Falla Crítica de Inversores / Strings Caídos (Déficit 26%)',
                'notes' => 'Telemetría SCADA detectó desconexión de ramales fotovoltaicos e inversor primario fuera de línea.',
            ],
            'severe_storm' => [
                'ratio' => 0.52,
                'label' => 'Tormenta Severa / Nubosidad Densa (Déficit 48%)',
                'notes' => 'Radiación global horizontal reducida drásticamente por frente meteorológico adverso.',
            ],
        };

        $realKwh = round($estimatedKwh * $scenarioData['ratio'], 2);
        $co2Kg = round($realKwh * 0.40, 2);

        // Persistir medición en base de datos
        $generation = EnergyGeneration::updateOrCreate(
            ['solar_farm_id' => $farm->id, 'period' => $period],
            [
                'record_date' => now()->toDateString(),
                'estimated_kwh' => $estimatedKwh,
                'real_kwh' => $realKwh,
                'co2_kg' => $co2Kg,
                'created_by' => (int) $request->user()->getAuthIdentifier(),
            ]
        );

        // Evaluar alerta automática (RF-14: déficit >= 20%)
        $deficit = round((($estimatedKwh - $realKwh) / $estimatedKwh) * 100, 1);
        $alertData = null;

        if ($realKwh <= ($estimatedKwh * 0.80)) {
            $alert = GenerationAlert::updateOrCreate(
                ['energy_generation_id' => $generation->id],
                [
                    'solar_farm_id' => $farm->id,
                    'period' => $period,
                    'estimated_kwh' => $estimatedKwh,
                    'real_kwh' => $realKwh,
                    'deviation_percentage' => $deficit,
                    'status' => 'active',
                    'notes' => sprintf(
                        'Alerta SCADA automática: déficit del %.1f%% en %s. %s',
                        $deficit,
                        $farm->name,
                        $scenarioData['notes']
                    ),
                ]
            );

            $alertData = [
                'id' => $alert->id,
                'farm_name' => $farm->name,
                'department_name' => $farm->department?->name ?? 'Nacional',
                'period' => $period,
                'estimated_kwh' => number_format($estimatedKwh, 1),
                'real_kwh' => number_format($realKwh, 1),
                'deviation_percentage' => $deficit,
                'notes' => $alert->notes,
                'show_url' => route('alerts.show', $alert),
            ];
        }

        // Trazabilidad y auditoría OWASP A09
        $this->audit->log('telemetry_simulated', EnergyGeneration::class, $generation->id, [
            'solar_farm' => $farm->name,
            'scenario' => $validated['scenario'],
            'period' => $period,
            'real_kwh' => $realKwh,
            'estimated_kwh' => $estimatedKwh,
            'deficit_percentage' => $deficit,
            'alert_triggered' => $alertData !== null,
        ]);

        // Recalcular métricas macro nacionales para actualización en vivo
        $totalKwh = (float) EnergyGeneration::sum('real_kwh');
        $totalCo2Kg = (float) EnergyGeneration::sum('co2_kg');
        $activeAlertsCount = GenerationAlert::where('status', 'active')->count();

        // Gráfica de últimos 6 períodos
        $monthlyGens = DB::table('energy_generations')
            ->select('period', DB::raw('SUM(estimated_kwh) as exp_kwh'), DB::raw('SUM(real_kwh) as act_kwh'))
            ->groupBy('period')
            ->orderBy('period')
            ->take(6)
            ->get();

        $chartLabels = $monthlyGens->pluck('period')->all();
        $chartExpected = $monthlyGens->pluck('exp_kwh')->map(fn ($v) => (float) $v)->all();
        $chartReal = $monthlyGens->pluck('act_kwh')->map(fn ($v) => (float) $v)->all();

        // Top ranking departamental recalculado
        $deptGenerations = DB::table('departments')
            ->join('solar_farms', 'departments.id', '=', 'solar_farms.department_id')
            ->join('energy_generations', 'solar_farms.id', '=', 'energy_generations.solar_farm_id')
            ->select('departments.name', DB::raw('SUM(energy_generations.real_kwh) as total_kwh'))
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('total_kwh')
            ->take(5)
            ->get();

        $topRanking = $deptGenerations->map(function ($row) use ($totalKwh) {
            $kwh = (float) $row->total_kwh;
            $co2Ton = ($kwh * 0.40) / 1000;
            $share = $totalKwh > 0 ? round(($kwh / $totalKwh) * 100, 1) : 0;

            return [
                'name' => $row->name,
                'kwh_mwh' => number_format($kwh / 1000, 1) . ' MWh',
                'co2' => number_format($co2Ton, 1) . ' Ton CO₂ evitado',
                'share' => $share,
            ];
        })->all();

        return response()->json([
            'success' => true,
            'message' => sprintf(
                'Telemetría IoT inyectada exitosamente a %s para el período %s.',
                $farm->name,
                $period
            ),
            'reading' => [
                'farm_name' => $farm->name,
                'department_name' => $farm->department?->name ?? 'Nacional',
                'capacity_kw' => number_format($capacityKw, 1),
                'period' => $period,
                'scenario_label' => $scenarioData['label'],
                'real_kwh' => number_format($realKwh, 1),
                'estimated_kwh' => number_format($estimatedKwh, 1),
                'deficit_percentage' => $deficit,
                'co2_kg' => number_format($co2Kg, 1),
                'timestamp' => now()->setTimezone('America/Guatemala')->format('d/m/Y H:i:s'),
                'alert_triggered' => $alertData !== null,
            ],
            'stats' => [
                'total_kwh_mwh' => number_format($totalKwh / 1000, 1),
                'total_kwh_formatted' => number_format($totalKwh),
                'total_co2_tons' => number_format($totalCo2Kg / 1000, 1),
                'total_co2_kg_formatted' => number_format($totalCo2Kg),
                'active_alerts_count' => $activeAlertsCount,
            ],
            'chart' => [
                'labels' => $chartLabels,
                'real' => $chartReal,
                'expected' => $chartExpected,
            ],
            'ranking' => $topRanking,
            'new_alert' => $alertData,
        ]);
    }
}
