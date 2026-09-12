<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ScadaSimulatorController extends Controller
{
    public function __construct(
        private AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $farms = SolarFarm::with(['department', 'solarPanels'])->get();
        $selectedFarm = $request->has('farm_id')
            ? $farms->firstWhere('id', (int) $request->input('farm_id')) ?? $farms->first()
            : $farms->first();

        $activeAlertsCount = GenerationAlert::where('status', 'active')->count();

        return view('simulator.index', compact('farms', 'selectedFarm', 'activeAlertsCount'));
    }

    public function recordEvent(Request $request): JsonResponse
    {
        Gate::authorize('manage-generations');

        $validated = $request->validate([
            'solar_farm_id' => ['required', 'integer', 'exists:solar_farms,id'],
            'irradiance_wm2' => ['required', 'numeric', 'min:0', 'max:1500'],
            'active_inverters' => ['required', 'integer', 'min:0', 'max:4'],
            'weather' => ['required', 'string', 'in:sunny,partly_cloudy,stormy,night'],
            'current_kw' => ['required', 'numeric', 'min:0'],
            'deficit_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $farm = SolarFarm::with(['department', 'solarPanels'])->findOrFail($validated['solar_farm_id']);
        $period = now()->format('Y-m');
        $capacityKw = max(50.0, (float) $farm->calculated_capacity_kw);

        // Generación estimada mensual basada en régimen solar de Guatemala
        $month = (int) now()->format('m');
        $seasonalFactor = ($month >= 5 && $month <= 10) ? 0.88 : 1.20;
        $estimatedKwh = round($capacityKw * 5.3 * 30 * 0.80 * $seasonalFactor, 2);

        // Generación real calculada según déficit inyectado
        $realKwh = round($estimatedKwh * (1 - ($validated['deficit_percentage'] / 100)), 2);
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

        $alertData = null;
        if ($validated['deficit_percentage'] >= 20.0) {
            $alert = GenerationAlert::updateOrCreate(
                ['energy_generation_id' => $generation->id],
                [
                    'solar_farm_id' => $farm->id,
                    'period' => $period,
                    'estimated_kwh' => $estimatedKwh,
                    'real_kwh' => $realKwh,
                    'deviation_percentage' => round((float) $validated['deficit_percentage'], 1),
                    'status' => 'active',
                    'notes' => sprintf(
                        'Incidente SCADA en vivo: Déficit del %.1f%% en %s. Inversores activos: %d/4. Clima: %s. %s',
                        $validated['deficit_percentage'],
                        $farm->name,
                        $validated['active_inverters'],
                        $validated['weather'],
                        $validated['notes'] ?? 'Anomalía registrada desde el Laboratorio SCADA IoT.'
                    ),
                ]
            );

            $alertData = [
                'id' => $alert->id,
                'farm_name' => $farm->name,
                'department_name' => $farm->department?->name ?? 'Nacional',
                'deviation_percentage' => round((float) $validated['deficit_percentage'], 1),
                'show_url' => route('alerts.show', $alert),
            ];
        }

        // Trazabilidad y auditoría OWASP A09
        $this->audit->log('scada_simulation_event', EnergyGeneration::class, $generation->id, [
            'solar_farm' => $farm->name,
            'weather' => $validated['weather'],
            'active_inverters' => $validated['active_inverters'],
            'irradiance_wm2' => $validated['irradiance_wm2'],
            'current_kw' => $validated['current_kw'],
            'deficit_percentage' => $validated['deficit_percentage'],
            'alert_triggered' => $alertData !== null,
        ]);

        return response()->json([
            'success' => true,
            'message' => sprintf('Telemetría SCADA persistida para %s (%s).', $farm->name, $period),
            'alert' => $alertData,
            'generation' => [
                'real_kwh' => number_format($realKwh, 1),
                'estimated_kwh' => number_format($estimatedKwh, 1),
                'co2_kg' => number_format($co2Kg, 1),
            ],
            'timestamp' => now()->setTimezone('America/Guatemala')->format('H:i:s'),
        ]);
    }

    public function resetTelemetry(Request $request): JsonResponse
    {
        Gate::authorize('manage-generations');

        $validated = $request->validate([
            'solar_farm_id' => ['required', 'integer', 'exists:solar_farms,id'],
        ]);

        $farm = SolarFarm::findOrFail($validated['solar_farm_id']);
        $period = now()->format('Y-m');

        // Restaurar alerta activa si fue generada en este período
        $alert = GenerationAlert::where('solar_farm_id', $farm->id)
            ->where('period', $period)
            ->where('status', 'active')
            ->first();

        if ($alert) {
            $alert->status = 'resolved';
            $alert->resolved_at = now();
            $alert->resolved_by = (int) $request->user()->getAuthIdentifier();
            $alert->resolution_notes = 'Restablecimiento nominal desde el Laboratorio de Control SCADA IoT.';
            $alert->save();
        }

        $this->audit->log('scada_simulation_reset', SolarFarm::class, $farm->id, [
            'solar_farm' => $farm->name,
            'period' => $period,
        ]);

        return response()->json([
            'success' => true,
            'message' => sprintf('Telemetría restablecida a condiciones nominales para %s.', $farm->name),
            'alert_resolved' => $alert !== null,
        ]);
    }
}
