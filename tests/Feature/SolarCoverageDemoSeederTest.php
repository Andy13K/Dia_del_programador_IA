<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Department;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\SolarCoverageDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class SolarCoverageDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DepartmentSeeder::class);
        $owner = User::factory()->create();
        $owner->role = 'admin';
        $owner->save();
        SolarPanel::query()->create(['brand' => 'Demo', 'model' => 'Existing', 'nominal_power_kw' => 0.440, 'status' => 'active']);
    }

    public function test_it_fills_only_empty_departments_and_preserves_existing_data(): void
    {
        $covered = ['AVE', 'BVE', 'CHM', 'CHQ', 'ESC', 'GUA', 'IZB', 'JUT', 'PET', 'QUE', 'SUC', 'ZAC'];
        foreach (Department::query()->whereIn('code', $covered)->get() as $department) {
            $farm = SolarFarm::query()->create([
                'name' => 'Granja existente '.$department->code, 'department_id' => $department->id,
                'latitude' => $department->latitude, 'longitude' => $department->longitude,
                'status' => 'maintenance', 'benefited_families' => 123, 'created_by' => User::query()->value('id'),
            ]);
            $farm->solarPanels()->attach(SolarPanel::query()->value('id'), ['quantity' => 51]);
            $farm->energyGenerations()->create(['period' => '2026-08', 'record_date' => '2026-08-31', 'estimated_kwh' => 1000, 'real_kwh' => 900, 'co2_kg' => 360]);
        }

        $originalFarms = DB::table('solar_farms')->orderBy('id')->get()->toJson();
        $originalGenerations = DB::table('energy_generations')->get()->toJson();
        $originalUsers = DB::table('users')->get()->toJson();
        $originalPanels = DB::table('solar_panels')->get()->toJson();
        $originalAssignments = DB::table('farm_panel')->orderBy('id')->get()->toJson();

        $this->seed(SolarCoverageDemoSeeder::class);

        $this->assertDatabaseCount('solar_farms', 22);
        $this->assertSame(22, SolarFarm::query()->distinct()->count('department_id'));
        $this->assertSame($originalFarms, DB::table('solar_farms')->orderBy('id')->limit(12)->get()->toJson());
        $this->assertSame($originalGenerations, DB::table('energy_generations')->get()->toJson());
        $this->assertSame($originalUsers, DB::table('users')->get()->toJson());
        $this->assertSame($originalPanels, DB::table('solar_panels')->get()->toJson());
        $this->assertSame($originalAssignments, DB::table('farm_panel')->orderBy('id')->limit(12)->get()->toJson());

        foreach (SolarFarm::query()->where('name', 'like', 'Granja Solar Demo %')->with('solarPanels')->get() as $farm) {
            $this->assertGreaterThan(0, $farm->calculated_capacity_kw);
            $this->assertSame(0, $farm->benefited_families);
        }
    }

    public function test_reruns_do_not_duplicate_overwrite_or_restore_deleted_farms(): void
    {
        $this->seed(SolarCoverageDemoSeeder::class);
        $farm = SolarFarm::query()->firstOrFail();
        $farm->update(['name' => 'Nombre corregido por el operador', 'status' => 'maintenance']);
        $farm->solarPanels()->updateExistingPivot(SolarPanel::query()->value('id'), ['quantity' => 17]);
        SolarFarm::query()->where('id', '!=', $farm->id)->firstOrFail()->delete();
        $farms = DB::table('solar_farms')->orderBy('id')->get()->toJson();
        $assignments = DB::table('farm_panel')->orderBy('id')->get()->toJson();

        $this->seed(SolarCoverageDemoSeeder::class);

        $this->assertSame($farms, DB::table('solar_farms')->orderBy('id')->get()->toJson());
        $this->assertSame($assignments, DB::table('farm_panel')->orderBy('id')->get()->toJson());
        $this->assertSame(21, SolarFarm::query()->count());
        $this->assertSame(22, SolarFarm::withTrashed()->count());
    }

    public function test_a_failure_rolls_back_all_new_farms_and_panel_assignments(): void
    {
        Department::query()->where('code', 'ZAC')->update(['latitude' => 0]);

        try {
            $this->seed(SolarCoverageDemoSeeder::class);
            $this->fail('Debe rechazar coordenadas inválidas.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Coordenadas de referencia inválidas', $exception->getMessage());
        }

        $this->assertDatabaseCount('solar_farms', 0);
        $this->assertDatabaseCount('farm_panel', 0);
    }

    public function test_it_refuses_to_create_farms_without_an_existing_administrator(): void
    {
        User::query()->update(['role' => 'visualizador']);

        try {
            $this->seed(SolarCoverageDemoSeeder::class);
            $this->fail('Debe exigir un administrador existente.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('administrador y un panel activo existentes', $exception->getMessage());
        }

        $this->assertDatabaseCount('solar_farms', 0);
        $this->assertSame('visualizador', User::query()->value('role'));
    }
}
