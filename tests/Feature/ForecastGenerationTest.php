<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class ForecastGenerationTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    private function operator(): User
    {
        $user = User::factory()->create();
        $user->role = 'operador';
        $user->save();

        return $user;
    }

    public function test_post_persists_forecast_and_audit(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);
        $this->history($farm);

        // SMA = 300*0.50 + 200*0.30 + 100*0.20 = 230.0 → 230 * 1.20 = 276.0 (abril=seco)
        $this->actingAs($user)->post('/forecasts/generate', ['solar_farm_id' => $farm->id, 'target_period' => '2026-04'])
            ->assertRedirect(route('forecasts.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('generation_forecasts', ['solar_farm_id' => $farm->id, 'forecasted_kwh' => 276]);
        $this->assertDatabaseHas('audit_logs', ['user_id' => $user->id, 'action' => 'forecast_generated']);
    }

    public function test_existing_empty_form_generates_next_month(): void
    {
        $this->travelTo(now()->setDate(2026, 1, 31));
        $user = $this->operator();
        $this->history($this->farm($user));
        $this->actingAs($user)->post('/forecasts/generate')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('generation_forecasts', ['target_period' => '2026-02']);
    }

    public function test_guest_and_read_only_user_cannot_generate(): void
    {
        $this->post('/forecasts/generate')->assertRedirect(route('login'));
        $user = User::factory()->create();
        $user->role = 'visualizador';
        $user->save();
        $this->actingAs($user)->post('/forecasts/generate')->assertForbidden();
        $this->assertDatabaseCount('generation_forecasts', 0);
    }

    public function test_invalid_period_and_foreign_farm_are_rejected(): void
    {
        $user = $this->operator();
        $farm = $this->farm();
        $this->actingAs($user)->postJson('/forecasts/generate', ['target_period' => '2026-13'])->assertUnprocessable();
        $this->postJson('/forecasts/generate', ['solar_farm_id' => $farm->id, 'target_period' => '2026-04'])->assertUnprocessable();
        $this->assertDatabaseCount('generation_forecasts', 0);
    }

    public function test_nominal_fallback_persists_forecast_when_no_history(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        // Sin historial suficiente → fallback nominal (capacity=0 → forecast=0)
        $this->actingAs($user)->post('/forecasts/generate', ['solar_farm_id' => $farm->id, 'target_period' => '2026-04'])
            ->assertRedirect(route('forecasts.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('generation_forecasts', [
            'solar_farm_id' => $farm->id,
            'method' => 'SMA-SF (nominal)',
        ]);
    }

    public function test_index_requires_authentication(): void
    {
        $this->get('/forecasts')->assertRedirect(route('login'));
    }

    public function test_index_displays_forecasts_and_compares_with_actual_generation(): void
    {
        $user = $this->operator();
        $farm = $this->farm($user);

        // Crear medición real para 2026-08
        $farm->energyGenerations()->create([
            'period' => '2026-08',
            'record_date' => '2026-08-31',
            'estimated_kwh' => 50000,
            'real_kwh' => 48500,
            'co2_kg' => 19400,
            'created_by' => $user->id,
        ]);

        // Crear forecast para 2026-08 sin actual_kwh inicial
        $forecast = $farm->generationForecasts()->create([
            'target_period' => '2026-08',
            'forecasted_kwh' => 49000,
            'method' => 'SMA-SF (ponderado)',
            'actual_kwh' => null,
            'notes' => 'Test forecast',
        ]);

        $response = $this->actingAs($user)->get('/forecasts');
        $response->assertOk();
        $response->assertViewHas('forecasts');
        $response->assertSee('Proyecciones registradas');
        $response->assertSee('48,500.00 kWh');

        $this->assertDatabaseHas('generation_forecasts', [
            'id' => $forecast->id,
            'actual_kwh' => 48500,
        ]);
    }
}
