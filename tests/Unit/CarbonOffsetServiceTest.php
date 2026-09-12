<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\CarbonOffsetService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CarbonOffsetServiceTest extends TestCase
{
    public function test_exact_official_factor_and_metric_tons(): void
    {
        $service = new CarbonOffsetService;
        $this->assertSame(0.0, $service->calculateCO2(0));
        $this->assertSame(0.4, $service->calculateCO2(1));
        $this->assertSame(400.0, $service->calculateCO2(1000));
        $this->assertSame(0.4, $service->toMetricTons(400));
    }

    public function test_negative_generation_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new CarbonOffsetService)->calculateCO2(-1);
    }
}
