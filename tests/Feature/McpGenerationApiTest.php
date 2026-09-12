<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class McpGenerationApiTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    private function seedMcpUser(): User
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'mcp-agent@kinsolar.internal'],
            [
                'name' => "Agente MCP (K'in Solar)",
                'password' => Hash::make(Str::random(40)),
            ],
        );
        $user->role = 'operador';
        $user->save();

        return $user;
    }

    public function test_returns_401_without_api_key(): void
    {
        $this->postJson('/api/v1/generations', [])
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_returns_401_with_wrong_api_key(): void
    {
        config(['services.mcp.key' => 'correct-key']);

        $this->postJson('/api/v1/generations', [], ['X-MCP-Key' => 'wrong-key'])
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_returns_422_with_valid_key_but_invalid_data(): void
    {
        config(['services.mcp.key' => 'test-key']);

        $this->postJson('/api/v1/generations', [], ['X-MCP-Key' => 'test-key'])
            ->assertUnprocessable();
    }

    public function test_returns_500_when_system_user_not_seeded(): void
    {
        config(['services.mcp.key' => 'test-key']);
        $farm = $this->farm();

        $this->postJson('/api/v1/generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-12',
            'record_date' => '2026-12-31',
            'estimated_kwh' => 50000,
            'real_kwh' => 48000,
        ], ['X-MCP-Key' => 'test-key'])
            ->assertStatus(500)
            ->assertJsonPath('success', false);
    }

    public function test_creates_generation_with_valid_key_and_data(): void
    {
        config(['services.mcp.key' => 'test-key']);
        $this->seedMcpUser();
        $farm = $this->farm();

        $response = $this->postJson('/api/v1/generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-12',
            'record_date' => '2026-12-31',
            'estimated_kwh' => 50000,
            'real_kwh' => 48000,
        ], ['X-MCP-Key' => 'test-key']);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.solar_farm_id', $farm->id)
            ->assertJsonPath('data.period', '2026-12')
            ->assertJsonPath('data.alert_created', false);

        $responseData = $response->json('data');
        $this->assertEquals(48000, $responseData['real_kwh']);
        $this->assertEquals(19200, $responseData['co2_kg']);

        $this->assertDatabaseHas('energy_generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-12',
            'real_kwh' => 48000,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'created',
            'model_type' => 'App\\Models\\EnergyGeneration',
        ]);
    }

    public function test_creates_alert_when_deficit_exceeds_20_percent(): void
    {
        config(['services.mcp.key' => 'test-key']);
        $this->seedMcpUser();
        $farm = $this->farm();

        $response = $this->postJson('/api/v1/generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-11',
            'record_date' => '2026-11-30',
            'estimated_kwh' => 50000,
            'real_kwh' => 35000,
        ], ['X-MCP-Key' => 'test-key']);

        $response->assertCreated()
            ->assertJsonPath('data.alert_created', true);

        $this->assertDatabaseHas('generation_alerts', [
            'solar_farm_id' => $farm->id,
            'status' => 'active',
        ]);
    }

    public function test_rejects_duplicate_period_for_same_farm(): void
    {
        config(['services.mcp.key' => 'test-key']);
        $this->seedMcpUser();
        $farm = $this->farm();
        $this->measurement($farm, '2026-12', 48000);

        $this->postJson('/api/v1/generations', [
            'solar_farm_id' => $farm->id,
            'period' => '2026-12',
            'record_date' => '2026-12-31',
            'estimated_kwh' => 50000,
            'real_kwh' => 48000,
        ], ['X-MCP-Key' => 'test-key'])
            ->assertUnprocessable();
    }

    public function test_public_api_routes_remain_accessible_without_key(): void
    {
        $farm = $this->farm();
        $this->measurement($farm, '2026-01', 100);

        $this->getJson('/api/v1/departments')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/farms')->assertOk()->assertJsonPath('success', true);
        $this->getJson('/api/v1/statistics')->assertOk()->assertJsonPath('success', true);
    }
}
