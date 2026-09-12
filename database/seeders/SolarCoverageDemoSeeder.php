<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SolarCoverageDemoSeeder extends Seeder
{
    public function run(): void
    {
        $created = DB::transaction(function (): array {
            // Serializa ejecuciones simultáneas sin cambiar el esquema de la base.
            $departments = Department::query()->orderBy('code')->lockForUpdate()->get();

            if ($departments->count() !== 22) {
                throw new RuntimeException('Se requiere el catálogo completo de los 22 departamentos antes de cargar la cobertura demo.');
            }

            $emptyDepartments = $departments->filter(
                // Una eliminación manual también se respeta al volver a ejecutar el seeder.
                fn (Department $department): bool => ! SolarFarm::withTrashed()
                    ->where('department_id', $department->id)->exists(),
            );

            if ($emptyDepartments->isEmpty()) {
                return [];
            }

            $owner = User::query()->where('role', 'admin')->orderBy('id')->first();
            $panel = SolarPanel::query()->where('status', 'active')
                ->where('nominal_power_kw', '>', 0)->orderBy('id')->lockForUpdate()->first();

            if ($owner === null || $panel === null) {
                throw new RuntimeException('Se requiere un administrador y un panel activo existentes; este seeder no crea usuarios ni modifica el catálogo de paneles.');
            }

            $created = [];

            foreach ($emptyDepartments as $index => $department) {
                $latitude = (float) $department->latitude;
                $longitude = (float) $department->longitude;

                if (! is_finite($latitude) || ! is_finite($longitude)
                    || $latitude < 13.5 || $latitude > 18 || $longitude < -92.5 || $longitude > -88) {
                    throw new RuntimeException('Coordenadas de referencia inválidas para '.$department->name.'. No se guardará ninguna granja.');
                }

                // Ubicaciones de referencia del catálogo, no instalaciones reales verificadas.
                $quantity = 600 + ($index % 7) * 200;
                $farm = SolarFarm::query()->create([
                    'department_id' => $department->id,
                    'name' => 'Granja Solar Demo '.$department->name,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'benefited_families' => 0,
                    'status' => 'active',
                    'created_by' => $owner->id,
                ]);
                $farm->solarPanels()->attach($panel->id, ['quantity' => $quantity]);

                $created[] = [
                    $farm->name,
                    $department->name,
                    $quantity,
                    number_format($quantity * (float) $panel->nominal_power_kw, 3, '.', ''),
                ];
            }

            return $created;
        });

        $this->command?->info('Granjas demo agregadas: '.count($created).'. Los registros existentes se conservaron.');

        if ($created !== []) {
            $this->command?->table(['Granja', 'Departamento', 'Paneles', 'Capacidad kW'], $created);
        }
    }
}
