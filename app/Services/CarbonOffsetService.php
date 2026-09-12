<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class CarbonOffsetService
{
    public function calculateCO2(float $realKwh): float
    {
        $this->validateAmount($realKwh);

        return $realKwh * 0.40;
    }

    public function toMetricTons(float $co2Kg): float
    {
        $this->validateAmount($co2Kg);

        return $co2Kg / 1000;
    }

    private function validateAmount(float $amount): void
    {
        if (! is_finite($amount) || $amount < 0) {
            throw new InvalidArgumentException('La cantidad debe ser finita y no negativa.');
        }
    }
}
