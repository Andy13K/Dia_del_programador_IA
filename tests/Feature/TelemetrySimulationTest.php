<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class TelemetrySimulationTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    private function operator(): User
    {
        $user = User::factory()->create();
        $user->role = 'operador';
        $user->save();

        return $user;
    }

    public function test_guest_cannot_simulate_telemetry(): void
    {
        $response = $this->postJson('/telemetry/simulate', [
            'solar_farm_id' => 1,
            'scenario' => 'optimal',
        ]);

        $response->assertUnauthorized();
    }

    public function test_visualizer_cannot_simulate_telemetry(): void
    {
        $user = User::factory()->create();
        $user->role = 'visualizador';
        $user->save();

        $farm = $this->farm($user);

        $response = $this->actingAs($user)->postJson('/telemetry/simulate', [
            'solar_farm_id' => $farm->id,
            'scenario' => 'optimal',
        ]);

        $response->assertForbidden();
    }

    public function test_operator_simulates_optimal_telemetry(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        $response = $this->actingAs($user)->postJson('/telemetry/simulate', [
            'solar_farm_id' => $farm->id,
            'scenario' => 'optimal',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'reading' => [
                    'farm_name' => $farm->name,
                    'alert_triggered' => false,
                ],
                'new_alert' => null,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'reading' => ['farm_name', 'real_kwh', 'estimated_kwh', 'co2_kg', 'timestamp'],
                'stats' => ['total_kwh_mwh', 'total_co2_tons', 'active_alerts_count'],
                'chart' => ['labels', 'real', 'expected'],
                'ranking',
            ]);

        $this->assertDatabaseHas('energy_generations', [
            'solar_farm_id' => $farm->id,
            'period' => now()->format('Y-m'),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'telemetry_simulated',
        ]);
    }

    public function test_operator_simulates_critical_alert_triggers_rf14(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        $response = $this->actingAs($user)->postJson('/telemetry/simulate', [
            'solar_farm_id' => $farm->id,
            'scenario' => 'critical_alert',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'reading' => [
                    'farm_name' => $farm->name,
                    'alert_triggered' => true,
                ],
            ]);

        $responseData = $response->json();
        $this->assertNotNull($responseData['new_alert']);
        $this->assertGreaterThanOrEqual(20.0, (float) $responseData['new_alert']['deviation_percentage']);

        $this->assertDatabaseHas('generation_alerts', [
            'solar_farm_id' => $farm->id,
            'period' => now()->format('Y-m'),
            'status' => 'active',
        ]);
    }
}
