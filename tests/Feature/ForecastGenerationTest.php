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
        $this->actingAs($user)->post('/forecasts/generate', ['solar_farm_id' => $farm->id, 'target_period' => '2026-04'])
            ->assertRedirect(route('forecasts.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('generation_forecasts', ['solar_farm_id' => $farm->id, 'forecasted_kwh' => 280]);
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

    public function test_bulk_failure_rolls_back_forecasts_and_audit(): void
    {
        $user = $this->operator();
        $this->history($this->farm($user));
        $this->farm($user);
        $this->actingAs($user)->postJson('/forecasts/generate', ['target_period' => '2026-04'])->assertUnprocessable();
        $this->assertDatabaseCount('generation_forecasts', 0);
        $this->assertDatabaseCount('audit_logs', 0);
    }
}
