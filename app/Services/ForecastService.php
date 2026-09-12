<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GenerationForecast;
use App\Models\SolarFarm;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ForecastService
{
    /** @var array<int, float> Pesos SMA ponderado: más reciente → más antiguo */
    private const SMA_WEIGHTS = [0.50, 0.30, 0.20];

    /** Horas sol pico mensuales promedio para Guatemala */
    private const PEAK_SUN_HOURS = 140;

    /** @var array<string, float> Factores estacionales bimodales de Guatemala */
    private const SEASONAL_FACTORS = [
        'dry'  => 1.20, // noviembre a abril
        'rainy' => 0.88, // mayo a octubre
    ];

    public function generateForecast(SolarFarm $farm, string $targetPeriod): GenerationForecast
    {
        if (! preg_match('/^[1-9][0-9]{3}-(0[1-9]|1[0-2])$/D', $targetPeriod)) {
            throw ValidationException::withMessages(['target_period' => 'El período debe tener formato YYYY-MM válido.']);
        }

        return DB::transaction(function () use ($farm, $targetPeriod): GenerationForecast {
            $farm = SolarFarm::query()->lockForUpdate()->findOrFail($farm->getKey());

            $history = $farm->energyGenerations()
                ->where('period', '<', $targetPeriod)
                ->orderByDesc('period')
                ->limit(3)
                ->get();

            if ($history->count() >= 3) {
                $base = 0.0;
                foreach ($history as $index => $generation) {
                    $base += (float) $generation->real_kwh * self::SMA_WEIGHTS[$index];
                }
                $method = 'SMA-SF (ponderado)';
            } else {
                $farm->load('solarPanels');
                $capacityKw = (float) $farm->calculated_capacity_kw;
                $base = $capacityKw * self::PEAK_SUN_HOURS;
                $method = 'SMA-SF (nominal)';
            }

            $month = (int) substr($targetPeriod, 5, 2);
            $factor = $this->seasonalFactor($month);
            $value = round($base * $factor, 2);

            if (! is_finite($value) || $value < 0 || $value > 9999999999.99) {
                throw ValidationException::withMessages(['target_period' => 'La proyección excede el rango permitido.']);
            }

            return $farm->generationForecasts()->updateOrCreate(['target_period' => $targetPeriod], [
                'forecasted_kwh' => $value,
                'method' => $method,
                'notes' => sprintf(
                    'Método: %s; factor estacional %s (mes %d, %.2f).',
                    $method,
                    $factor === self::SEASONAL_FACTORS['dry'] ? 'seco' : 'lluvioso',
                    $month,
                    $factor,
                ),
            ]);
        });
    }

    /**
     * Devuelve el factor estacional para el mes dado según el régimen bimodal de Guatemala.
     */
    public function seasonalFactor(int $month): float
    {
        return ($month >= 5 && $month <= 10)
            ? self::SEASONAL_FACTORS['rainy']
            : self::SEASONAL_FACTORS['dry'];
    }
}
