<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class CarbonOffsetService
{
    /**
     * Factor fijado por las bases de la competencia (§14): 0.40 kg de CO₂ evitado por kWh.
     * Única definición del número; config/solar.php lo expone a las vistas.
     */
    public const CO2_KG_PER_KWH = 0.40;

    public function calculateCO2(float $realKwh): float
    {
        $this->validateAmount($realKwh);

        return $realKwh * self::CO2_KG_PER_KWH;
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
