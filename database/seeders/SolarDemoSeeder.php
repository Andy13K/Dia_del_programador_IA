<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SolarDemoSeeder extends Seeder
{
    /**
     * Catálogo de paneles de referencia (marcas reales, potencias entre 0.400 y 0.650 kW).
     *
     * @var list<array{brand: string, model: string, nominal_power_kw: float}>
     */
    private const PANELS = [
        ['brand' => 'Canadian Solar', 'model' => 'CS6R-440MS', 'nominal_power_kw' => 0.440],
        ['brand' => 'Canadian Solar', 'model' => 'CS7N-610MS', 'nominal_power_kw' => 0.610],
        ['brand' => 'JinkoSolar', 'model' => 'Tiger Neo JKM440N-54HL4-B', 'nominal_power_kw' => 0.440],
        ['brand' => 'JinkoSolar', 'model' => 'Tiger Neo JKM650N-78HL4-BDV', 'nominal_power_kw' => 0.650],
        ['brand' => 'LONGi Solar', 'model' => 'Hi-MO 6 LR5-54HTH-440M', 'nominal_power_kw' => 0.440],
        ['brand' => 'LONGi Solar', 'model' => 'Hi-MO X6 LR5-54HTH-460M', 'nominal_power_kw' => 0.460],
    ];

    /**
     * 10 granjas en departamentos clave, con coordenadas reales de su municipio
     * (ligeramente distintas a la cabecera departamental para diferenciarlas en el mapa).
     *
     * @var list<array{name: string, department_code: string, latitude: float, longitude: float, benefited_families: int, status: string}>
     */
    private const FARMS = [
        ['name' => 'Granja Solar Villa Nueva', 'department_code' => 'GUA', 'latitude' => 14.5272, 'longitude' => -90.5807, 'benefited_families' => 850, 'status' => 'active'],
        ['name' => 'Granja Solar Coatepeque', 'department_code' => 'QUE', 'latitude' => 14.7000, 'longitude' => -91.8600, 'benefited_families' => 620, 'status' => 'active'],
        ['name' => 'Granja Solar Escuintla Norte', 'department_code' => 'ESC', 'latitude' => 14.3350, 'longitude' => -90.7700, 'benefited_families' => 1200, 'status' => 'active'],
        ['name' => 'Granja Solar Puerto Barrios', 'department_code' => 'IZB', 'latitude' => 15.7350, 'longitude' => -88.6050, 'benefited_families' => 940, 'status' => 'active'],
        ['name' => 'Granja Solar Poptún', 'department_code' => 'PET', 'latitude' => 16.3244, 'longitude' => -89.4200, 'benefited_families' => 480, 'status' => 'active'],
        ['name' => 'Granja Solar Estanzuela', 'department_code' => 'ZAC', 'latitude' => 14.9550, 'longitude' => -89.5450, 'benefited_families' => 390, 'status' => 'active'],
        ['name' => 'Granja Solar Cobán', 'department_code' => 'AVE', 'latitude' => 15.4550, 'longitude' => -90.3850, 'benefited_families' => 1560, 'status' => 'active'],
        ['name' => 'Granja Solar Chiquimula Sur', 'department_code' => 'CHQ', 'latitude' => 14.7850, 'longitude' => -89.5550, 'benefited_families' => 700, 'status' => 'active'],
        ['name' => 'Granja Solar Jutiapa Oriente', 'department_code' => 'JUT', 'latitude' => 14.2900, 'longitude' => -89.9100, 'benefited_families' => 310, 'status' => 'active'],
        ['name' => 'Granja Solar Mazatenango', 'department_code' => 'SUC', 'latitude' => 14.5250, 'longitude' => -91.5150, 'benefited_families' => 2100, 'status' => 'maintenance'],
    ];

    /**
     * Índice de panel (según PANELS) => cantidad instalada, por granja (según FARMS).
     * Cantidades entre 200 y 1,500 paneles por tipo.
     *
     * @var array<int, list<array{0: int, 1: int}>>
     */
    private const FARM_PANEL_ASSIGNMENTS = [
        0 => [[0, 850], [2, 400]],
        1 => [[4, 700], [1, 300]],
        2 => [[3, 1200], [5, 500]],
        3 => [[1, 950], [4, 450]],
        4 => [[0, 500], [2, 350]],
        5 => [[5, 380], [0, 300]],
        6 => [[3, 1500], [1, 700]],
        7 => [[2, 600], [4, 400]],
        8 => [[0, 250], [5, 300]],
        9 => [[3, 1100], [2, 900]],
    ];

    /** Períodos históricos a sembrar (últimos 6 meses de la demo). */
    private const PERIODS = ['2026-03', '2026-04', '2026-05', '2026-06', '2026-07', '2026-08'];

    /**
     * Factor de estacionalidad solar guatemalteca por mes (docs/06-CONTRATOS-HORA-1.md §6):
     * época seca (nov-abr) alta irradiancia, época lluviosa (may-oct) nubosidad frecuente.
     *
     * @var array<string, float>
     */
    private const SEASONAL_FACTOR = [
        '2026-03' => 1.20,
        '2026-04' => 1.18,
        '2026-05' => 0.90,
        '2026-06' => 0.88,
        '2026-07' => 0.86,
        '2026-08' => 0.87,
    ];

    /**
     * Combinaciones [índice de granja, período] con déficit del 25% (real <= 0.80 * esperado)
     * para que el jurado vea alertas activas desde el primer vistazo al dashboard (RF-14).
     *
     * @var list<array{0: int, 1: string}>
     */
    private const DEFICIT_POINTS = [
        [2, '2026-06'],
        [6, '2026-07'],
        [8, '2026-08'],
    ];

    private const CO2_KG_PER_KWH = 0.40;

    private const PEAK_SUN_HOURS = 5.3;

    private const PERFORMANCE_RATIO = 0.80;

    public function run(): void
    {
        $panels = $this->seedPanels();
        [$adminId, $operadorId] = $this->seedOperators();

        foreach (self::FARMS as $index => $farmData) {
            $department = Department::query()->where('code', $farmData['department_code'])->firstOrFail();

            $farm = SolarFarm::query()->updateOrCreate(
                ['name' => $farmData['name']],
                [
                    'department_id' => $department->id,
                    'latitude' => $farmData['latitude'],
                    'longitude' => $farmData['longitude'],
                    'benefited_families' => $farmData['benefited_families'],
                    'status' => $farmData['status'],
                    'created_by' => $index % 2 === 0 ? $adminId : $operadorId,
                ],
            );

            $capacityKw = 0.0;
            foreach (self::FARM_PANEL_ASSIGNMENTS[$index] as [$panelIndex, $quantity]) {
                $panel = $panels[$panelIndex];
                $farm->solarPanels()->syncWithoutDetaching([$panel->id => ['quantity' => $quantity]]);
                $capacityKw += $quantity * (float) $panel->nominal_power_kw;
            }

            $this->seedGenerationsForFarm($farm, $index, $capacityKw);
        }
    }

    /**
     * @return list<SolarPanel>
     */
    private function seedPanels(): array
    {
        return array_map(
            fn (array $panelData): SolarPanel => SolarPanel::query()->updateOrCreate(
                ['brand' => $panelData['brand'], 'model' => $panelData['model']],
                ['nominal_power_kw' => $panelData['nominal_power_kw'], 'status' => 'active'],
            ),
            self::PANELS,
        );
    }

    /**
     * @return array{0: int, 1: int} [admin_id, operador_id]
     */
    private function seedOperators(): array
    {
        $adminId = User::query()->where('email', 'admin@solarguatemala.gob.gt')->value('id');
        $operadorId = User::query()->where('email', 'operador@solarguatemala.gob.gt')->value('id');

        return [$adminId, $operadorId];
    }

    private function seedGenerationsForFarm(SolarFarm $farm, int $farmIndex, float $capacityKw): void
    {
        foreach (self::PERIODS as $monthIndex => $period) {
            $seasonalFactor = self::SEASONAL_FACTOR[$period];
            $daysInMonth = Carbon::parse("{$period}-01")->daysInMonth;

            $estimatedKwh = round($capacityKw * self::PEAK_SUN_HOURS * $daysInMonth * self::PERFORMANCE_RATIO * $seasonalFactor, 2);

            $isDeficit = in_array([$farmIndex, $period], self::DEFICIT_POINTS, true);
            $performanceFactor = $isDeficit ? 0.75 : 0.95 + (($farmIndex + $monthIndex) % 4) * 0.03;

            $realKwh = round($estimatedKwh * $performanceFactor, 2);
            $co2Kg = round($realKwh * self::CO2_KG_PER_KWH, 2);

            $generation = EnergyGeneration::query()->updateOrCreate(
                ['solar_farm_id' => $farm->id, 'period' => $period],
                [
                    'record_date' => Carbon::parse("{$period}-01")->endOfMonth()->toDateString(),
                    'estimated_kwh' => $estimatedKwh,
                    'real_kwh' => $realKwh,
                    'co2_kg' => $co2Kg,
                    'created_by' => $farm->created_by,
                ],
            );

            if ($isDeficit) {
                $deviationPercentage = round((1 - ($realKwh / $estimatedKwh)) * 100, 2);

                GenerationAlert::query()->updateOrCreate(
                    ['solar_farm_id' => $farm->id, 'period' => $period],
                    [
                        'energy_generation_id' => $generation->id,
                        'estimated_kwh' => $estimatedKwh,
                        'real_kwh' => $realKwh,
                        'deviation_percentage' => $deviationPercentage,
                        'status' => 'active',
                    ],
                );
            }
        }
    }
}
