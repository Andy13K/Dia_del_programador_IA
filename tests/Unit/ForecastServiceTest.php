<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ForecastService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class ForecastServiceTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    public function test_all_months_apply_the_correct_seasonal_factor(): void
    {
        $farm = $this->farm();
        $this->history($farm);
        foreach (range(1, 12) as $month) {
            $period = sprintf('2026-%02d', $month);
            $forecast = (new ForecastService)->generateForecast($farm, $period);
            $expected = $month >= 5 && $month <= 10 ? 205.33 : 280.0;
            $this->assertSame($expected, (float) $forecast->forecasted_kwh);
            $this->assertSame('SMA-SF', $forecast->method);
            $this->assertSame($farm->id, $forecast->solar_farm_id);
        }
    }

    public function test_only_latest_three_prior_measurements_of_the_farm_are_used(): void
    {
        $farm = $this->farm();
        $this->history($farm);
        $this->measurement($farm, '2024-12', 9000);
        $this->measurement($farm, '2026-01', 8000);
        $this->measurement($farm, '2026-02', 7000);
        $this->measurement($this->farm(), '2025-03', 6000);
        $service = new ForecastService;
        $forecast = $service->generateForecast($farm, '2026-01');
        $this->assertSame(280.0, (float) $forecast->forecasted_kwh);
        $this->assertSame($forecast->id, $service->generateForecast($farm, '2026-01')->id);
        $this->assertDatabaseCount('generation_forecasts', 1);
    }

    public function test_insufficient_history_does_not_persist_a_forecast(): void
    {
        $farm = $this->farm();
        $this->measurement($farm, '2025-01', 100);
        try {
            (new ForecastService)->generateForecast($farm, '2026-01');
            $this->fail('Se aceptó un historial insuficiente.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('target_period', $exception->errors());
        }
        $this->assertDatabaseCount('generation_forecasts', 0);
    }

    public function test_invalid_month_is_rejected(): void
    {
        $farm = $this->farm();
        $this->expectException(ValidationException::class);
        (new ForecastService)->generateForecast($farm, '2026-13');
    }
}
