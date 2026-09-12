<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\SolarPanel;
use App\Services\ForecastService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class ForecastServiceTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    /**
     * Con history()=[100, 200, 300] orden DESC=[300, 200, 100]:
     *   SMA = 300*0.50 + 200*0.30 + 100*0.20 = 150 + 60 + 20 = 230.0
     *   Seco (1.20):  230 * 1.20 = 276.0
     *   Lluvioso (0.88): 230 * 0.88 = 202.4
     */
    public function test_all_months_apply_the_correct_seasonal_factor(): void
    {
        $farm = $this->farm();
        $this->history($farm);

        foreach (range(1, 12) as $month) {
            $period = sprintf('2026-%02d', $month);
            $forecast = (new ForecastService)->generateForecast($farm, $period);
            $expected = ($month >= 5 && $month <= 10) ? 202.4 : 276.0;
            $this->assertSame($expected, (float) $forecast->forecasted_kwh, "Mes {$month} debería aplicar factor correcto.");
            $this->assertSame('SMA-SF (ponderado)', $forecast->method);
            $this->assertSame($farm->id, $forecast->solar_farm_id);
        }
    }

    public function test_only_latest_three_prior_measurements_of_the_farm_are_used(): void
    {
        $farm = $this->farm();
        $this->history($farm);

        // Mediciones adicionales que NO deben afectar el cálculo para 2026-01
        $this->measurement($farm, '2024-12', 9000);
        $this->measurement($farm, '2026-01', 8000);
        $this->measurement($farm, '2026-02', 7000);
        $this->measurement($this->farm(), '2025-03', 6000); // otra granja

        $service = new ForecastService;
        $forecast = $service->generateForecast($farm, '2026-01');

        // Las 3 más recientes antes de 2026-01: 2025-03=300, 2025-02=200, 2025-01=100
        // SMA = 300*0.50 + 200*0.30 + 100*0.20 = 230.0 * 1.20 = 276.0
        $this->assertSame(276.0, (float) $forecast->forecasted_kwh);

        // updateOrCreate → idempotente
        $this->assertSame($forecast->id, $service->generateForecast($farm, '2026-01')->id);
        $this->assertDatabaseCount('generation_forecasts', 1);
    }

    /**
     * Con < 3 mediciones, usa fallback nominal: capacity_kw * 140 * factor.
     * Panel: 0.500 kW × quantity 10 = 5.0 kW → 5.0 * 140 = 700.0
     * Mes 1 (seco): 700.0 * 1.20 = 840.0
     */
    public function test_uses_nominal_fallback_when_insufficient_history(): void
    {
        $farm = $this->farm();

        // Asignar panel para que tenga capacidad nominal
        $panel = SolarPanel::query()->create([
            'brand' => 'TestBrand',
            'model' => 'TestModel',
            'nominal_power_kw' => 0.500,
            'status' => 'active',
        ]);
        $farm->solarPanels()->attach($panel->id, ['quantity' => 10]);

        // Solo 1 medición → insuficiente para SMA
        $this->measurement($farm, '2025-01', 100);

        $forecast = (new ForecastService)->generateForecast($farm, '2026-01');

        // 0.500 * 10 = 5.0 kW → 5.0 * 140 = 700 → 700 * 1.20 = 840.0
        $this->assertSame(840.0, (float) $forecast->forecasted_kwh);
        $this->assertSame('SMA-SF (nominal)', $forecast->method);
        $this->assertDatabaseCount('generation_forecasts', 1);
    }

    public function test_nominal_fallback_with_no_panels_returns_zero(): void
    {
        $farm = $this->farm();

        $forecast = (new ForecastService)->generateForecast($farm, '2026-07');

        // Sin paneles → capacity = 0 → 0 * 140 * 0.88 = 0.0
        $this->assertSame(0.0, (float) $forecast->forecasted_kwh);
        $this->assertSame('SMA-SF (nominal)', $forecast->method);
    }

    public function test_invalid_month_is_rejected(): void
    {
        $farm = $this->farm();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new ForecastService)->generateForecast($farm, '2026-13');
    }

    public function test_dry_season_months_have_higher_factor_than_rainy(): void
    {
        $service = new ForecastService;

        // Época seca: nov-abr
        foreach ([1, 2, 3, 4, 11, 12] as $month) {
            $this->assertSame(1.20, $service->seasonalFactor($month), "Mes {$month} debe ser seco.");
        }

        // Época lluviosa: may-oct
        foreach ([5, 6, 7, 8, 9, 10] as $month) {
            $this->assertSame(0.88, $service->seasonalFactor($month), "Mes {$month} debe ser lluvioso.");
        }
    }
}
