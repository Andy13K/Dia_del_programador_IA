<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\AlertEvaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\SolarFixtures;
use Tests\TestCase;

class AlertEvaluationServiceTest extends TestCase
{
    use RefreshDatabase, SolarFixtures;

    public function test_alert_threshold_is_inclusive_and_idempotent(): void
    {
        $farm = $this->farm();
        foreach ([80.0, 79.0, 0.0] as $index => $real) {
            $generation = $this->measurement($farm, '2025-0'.($index + 1), $real);
            $service = new AlertEvaluationService;
            $alert = $service->evaluateGeneration($generation);
            $this->assertNotNull($alert);
            $this->assertSame('active', $alert->status);
            $this->assertSame(round(100 - $real, 2), (float) $alert->deviation_percentage);
            $this->assertSame($alert->id, $service->evaluateGeneration($generation)->id);
        }
        $this->assertDatabaseCount('generation_alerts', 3);
    }

    public function test_optimal_and_zero_estimate_do_not_alert(): void
    {
        $farm = $this->farm();
        foreach ([[80.01, 100], [100, 100], [120, 100], [0, 0]] as $index => [$real, $estimated]) {
            $this->assertNull((new AlertEvaluationService)->evaluateGeneration($this->measurement($farm, '2025-0'.($index + 1), $real, $estimated)));
        }
        $this->assertDatabaseCount('generation_alerts', 0);
    }

    public function test_decimal_boundary_is_not_lost_to_floating_point(): void
    {
        $alert = (new AlertEvaluationService)->evaluateGeneration($this->measurement($this->farm(), '2025-01', 0.28, 0.35));
        $this->assertSame('20.00', $alert->deviation_percentage);
    }
}
