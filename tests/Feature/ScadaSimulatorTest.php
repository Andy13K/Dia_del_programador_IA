<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class ScadaSimulatorTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    private function operator(): User
    {
        $user = User::factory()->create();
        $user->role = 'operador';
        $user->save();

        return $user;
    }

    public function test_guest_cannot_access_simulator(): void
    {
        $response = $this->get('/simulator');
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_simulator(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        $response = $this->actingAs($user)->get('/simulator');
        $response->assertOk()
            ->assertViewIs('simulator.index')
            ->assertViewHas('farms')
            ->assertViewHas('selectedFarm');
    }

    public function test_operator_records_scada_normal_event(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        $response = $this->actingAs($user)->postJson('/simulator/event', [
            'solar_farm_id' => $farm->id,
            'irradiance_wm2' => 1000,
            'active_inverters' => 4,
            'weather' => 'sunny',
            'current_kw' => 500.0,
            'deficit_percentage' => 3.5,
            'notes' => 'Operación nominal en prueba SCADA',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'alert' => null,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'generation' => ['real_kwh', 'estimated_kwh', 'co2_kg'],
                'timestamp',
            ]);

        $this->assertDatabaseHas('energy_generations', [
            'solar_farm_id' => $farm->id,
            'period' => now()->format('Y-m'),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'scada_simulation_event',
        ]);
    }

    public function test_operator_records_incident_triggers_rf14_alert(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        $response = $this->actingAs($user)->postJson('/simulator/event', [
            'solar_farm_id' => $farm->id,
            'irradiance_wm2' => 800,
            'active_inverters' => 2,
            'weather' => 'partly_cloudy',
            'current_kw' => 250.0,
            'deficit_percentage' => 26.5,
            'notes' => 'Falla de 2 inversores inducida',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $resData = $response->json();
        $this->assertNotNull($resData['alert']);
        $this->assertEquals(26.5, $resData['alert']['deviation_percentage']);

        $this->assertDatabaseHas('generation_alerts', [
            'solar_farm_id' => $farm->id,
            'period' => now()->format('Y-m'),
            'status' => 'active',
        ]);
    }

    public function test_operator_can_reset_telemetry(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        // Crear alerta previa
        GenerationAlert::create([
            'solar_farm_id' => $farm->id,
            'period' => now()->format('Y-m'),
            'estimated_kwh' => 1000,
            'real_kwh' => 700,
            'deviation_percentage' => 30.0,
            'status' => 'active',
            'notes' => 'Alerta previa de prueba',
        ]);

        $response = $this->actingAs($user)->postJson('/simulator/reset', [
            'solar_farm_id' => $farm->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'alert_resolved' => true,
            ]);

        $this->assertDatabaseHas('generation_alerts', [
            'solar_farm_id' => $farm->id,
            'period' => now()->format('Y-m'),
            'status' => 'resolved',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'scada_simulation_reset',
        ]);
    }
}
