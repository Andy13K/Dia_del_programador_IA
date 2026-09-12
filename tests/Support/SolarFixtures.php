<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Models\Department;
use App\Models\EnergyGeneration;
use App\Models\SolarFarm;
use App\Models\User;

trait SolarFixtures
{
    protected function farm(?User $owner = null): SolarFarm
    {
        $department = Department::query()->firstOrCreate(['code' => '180'], ['name' => 'Izabal', 'latitude' => 15.7, 'longitude' => -88.6]);

        return SolarFarm::query()->create(['department_id' => $department->id, 'name' => 'Granja de prueba', 'latitude' => 15.7, 'longitude' => -88.6, 'benefited_families' => 10, 'status' => 'active', 'created_by' => $owner?->id]);
    }

    protected function measurement(SolarFarm $farm, string $period, float $real, float $estimated = 100): EnergyGeneration
    {
        return $farm->energyGenerations()->create(['period' => $period, 'record_date' => $period.'-01', 'real_kwh' => $real, 'estimated_kwh' => $estimated, 'co2_kg' => $real * 0.4]);
    }

    protected function history(SolarFarm $farm): void
    {
        $this->measurement($farm, '2025-01', 100);
        $this->measurement($farm, '2025-02', 200);
        $this->measurement($farm, '2025-03', 300);
    }
}
