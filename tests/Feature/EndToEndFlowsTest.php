<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Department;
use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\GenerationForecast;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class EndToEndFlowsTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    private function createRoleUser(string $role): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('SecretPass123!'),
        ]);
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function test_flow_authentication_and_rbac_roles(): void
    {
        $admin = $this->createRoleUser('admin');
        $operator = $this->createRoleUser('operador');
        $viewer = $this->createRoleUser('visualizador');

        // Admin can access user management
        $this->actingAs($admin)->get('/users')->assertOk();

        // Operator cannot access user management
        $this->actingAs($operator)->get('/users')->assertForbidden();

        // Viewer cannot access user management or farm creation
        $this->actingAs($viewer)->get('/users')->assertForbidden();
        $this->actingAs($viewer)->get('/farms/create')->assertForbidden();
    }

    public function test_flow_solar_farms_crud_and_capacity_calculation(): void
    {
        $operator = $this->createRoleUser('operador');
        $department = Department::query()->firstOrCreate(
            ['code' => 'GUA'],
            ['name' => 'Guatemala', 'latitude' => 14.63, 'longitude' => -90.50]
        );

        $panel = SolarPanel::query()->create([
            'brand' => 'Canadian Solar',
            'model' => 'CS6R-440MS',
            'nominal_power_kw' => 0.440,
            'status' => 'active',
        ]);

        // Crear granja (RF-03, RF-04, RF-07)
        $response = $this->actingAs($operator)->post('/farms', [
            'name' => 'Granja Prueba Flujo E2E',
            'department_id' => $department->id,
            'latitude' => 14.5500,
            'longitude' => -90.6000,
            'benefited_families' => 500,
            'status' => 'active',
            'panels' => [
                ['panel_id' => $panel->id, 'quantity' => 1000],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('solar_farms', ['name' => 'Granja Prueba Flujo E2E']);

        $farm = SolarFarm::where('name', 'Granja Prueba Flujo E2E')->firstOrFail();
        $response->assertRedirect(route('farms.show', $farm));

        // Capacidad instalada automática: 1000 paneles * 0.440 kW = 440.0 kW (RF-06)
        $this->assertEquals(440.0, $farm->calculated_capacity_kw);

        // Operador no puede eliminar granja (OWASP A01: solo admin)
        $this->actingAs($operator)->delete("/farms/{$farm->id}")->assertForbidden();

        // Administrador desactiva / elimina suavemente granja (RF-03)
        $admin = $this->createRoleUser('admin');
        $this->actingAs($admin)->delete("/farms/{$farm->id}")
            ->assertRedirect(route('farms.index'));
        $this->assertSoftDeleted('solar_farms', ['id' => $farm->id]);
    }

    public function test_flow_energy_generation_and_co2_and_alert_deficit(): void
    {
        $operator = $this->createRoleUser('operador');
        $farm = $this->farm($operator);

        // Registrar medición con déficit >= 20% (esperada: 50,000, real: 35,000 → déficit 30%)
        $response = $this->actingAs($operator)->post('/generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-08',
            'record_date' => '2026-08-31',
            'estimated_kwh' => 50000,
            'real_kwh' => 35000,
            'notes' => 'Medición de prueba con déficit severo',
        ]);

        $response->assertRedirect(route('generations.index'));

        // CO2 evitado debe ser exactamente real_kwh * 0.40 = 14,000 kg (RF-10)
        $this->assertDatabaseHas('energy_generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-08',
            'co2_kg' => 14000,
        ]);

        // Debe generar automáticamente alerta activa por déficit >= 20% (RF-14)
        $this->assertDatabaseHas('generation_alerts', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-08',
            'deviation_percentage' => 30.00,
            'status' => 'active',
        ]);
    }

    public function test_flow_alerts_resolution(): void
    {
        $operator = $this->createRoleUser('operador');
        $farm = $this->farm($operator);

        $generation = $farm->energyGenerations()->create([
            'period' => '2026-08',
            'record_date' => '2026-08-31',
            'estimated_kwh' => 50000,
            'real_kwh' => 35000,
            'co2_kg' => 14000,
            'created_by' => $operator->id,
        ]);

        $alert = $generation->generationAlert()->create([
            'solar_farm_id' => $farm->id,
            'period' => '2026-08',
            'estimated_kwh' => 50000,
            'real_kwh' => 35000,
            'deviation_percentage' => 30.00,
            'status' => 'active',
        ]);

        // Resolver alerta con justificación
        $this->actingAs($operator)->post("/alerts/{$alert->id}/resolve", [
            'resolution_notes' => 'Inversor reparado y paneles limpiados satisfactoriamente.',
        ])->assertRedirect(route('alerts.index'));

        $this->assertDatabaseHas('generation_alerts', [
            'id' => $alert->id,
            'status' => 'resolved',
        ]);
    }

    public function test_flow_reports_and_csv_export(): void
    {
        $viewer = $this->createRoleUser('visualizador');
        Department::query()->firstOrCreate(
            ['code' => 'ESC'],
            ['name' => 'Escuintla', 'latitude' => 14.30, 'longitude' => -90.78]
        );

        // Cargar vista de reportes departamentales (RF-12)
        $this->actingAs($viewer)->get('/reports')->assertOk()->assertSee('Escuintla');

        // Exportar a CSV (RF-12)
        $response = $this->actingAs($viewer)->get('/reports/export');
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    public function test_flow_map_data_and_departments(): void
    {
        $viewer = $this->createRoleUser('visualizador');
        $department = Department::query()->firstOrCreate(
            ['code' => 'PET'],
            ['name' => 'Petén', 'latitude' => 16.92, 'longitude' => -89.89]
        );

        $this->actingAs($viewer)->get('/map')
            ->assertOk()
            ->assertViewHas('departments')
            ->assertViewHas('farmsJson')
            ->assertSee('Petén');
    }

    public function test_flow_public_api_and_mcp_post(): void
    {
        // Seeder usuario sistema para MCP
        User::query()->firstOrCreate(
            ['email' => 'mcp-agent@kinsolar.internal'],
            ['name' => "Agente MCP (K'in Solar)", 'password' => Hash::make('secret'), 'role' => 'operador']
        );

        $department = Department::query()->firstOrCreate(
            ['code' => 'ZAC'],
            ['name' => 'Zacapa', 'latitude' => 14.97, 'longitude' => -89.53]
        );

        $operator = $this->createRoleUser('operador');
        $farm = SolarFarm::query()->create([
            'department_id' => $department->id,
            'name' => 'Granja API MCP Test',
            'latitude' => 14.97,
            'longitude' => -89.53,
            'benefited_families' => 300,
            'status' => 'active',
            'created_by' => $operator->id,
        ]);

        // Rutas GET públicas
        $this->getJson('/api/v1/statistics')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/departments')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/farms')->assertOk()->assertJsonPath('success', true);

        // POST con MCP API Key
        config(['services.mcp.key' => 'mcp-secret-key-123']);

        $response = $this->postJson('/api/v1/generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-10',
            'record_date' => '2026-10-31',
            'estimated_kwh' => 40000,
            'real_kwh' => 38000,
        ], ['X-MCP-Key' => 'mcp-secret-key-123']);

        $response->assertCreated()->assertJsonPath('success', true);
        $this->assertDatabaseHas('energy_generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-10',
        ]);
    }
}
