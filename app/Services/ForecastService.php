<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GenerationForecast;
use App\Models\SolarFarm;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ForecastService
{
    public function generateForecast(SolarFarm $farm, string $targetPeriod): GenerationForecast
    {
        if (! preg_match('/^[1-9][0-9]{3}-(0[1-9]|1[0-2])$/D', $targetPeriod)) {
            throw ValidationException::withMessages(['target_period' => 'El período debe tener formato YYYY-MM válido.']);
        }

        return DB::transaction(function () use ($farm, $targetPeriod): GenerationForecast {
            // Serializa regeneraciones de la misma granja sin cambiar el esquema compartido.
            $farm = SolarFarm::query()->lockForUpdate()->findOrFail($farm->getKey());
            $history = $farm->energyGenerations()->where('period', '<', $targetPeriod)
                ->orderByDesc('period')->limit(3)->get();
            if ($history->count() < 3) {
                throw ValidationException::withMessages(['target_period' => 'Se requieren tres mediciones anteriores al período objetivo para cada granja.']);
            }
            $weighted = 0.0;
            foreach ($history as $index => $generation) {
                $weighted += (float) $generation->real_kwh * (3 - $index);
            }
            $month = (int) substr($targetPeriod, 5, 2);
            $factor = $month >= 5 && $month <= 10 ? 0.88 : 1.20;
            $value = round(($weighted / 6) * $factor, 2);
            if (! is_finite($value) || $value < 0 || $value > 9999999999.99) {
                throw ValidationException::withMessages(['target_period' => 'La proyección excede el rango permitido.']);
            }

            return $farm->generationForecasts()->updateOrCreate(['target_period' => $targetPeriod], [
                'forecasted_kwh' => $value,
                'method' => 'SMA-SF',
                'notes' => 'Pesos 3, 2 y 1 desde la medición más reciente; factor estacional '.$factor.'.',
            ]);
        });
    }
}
