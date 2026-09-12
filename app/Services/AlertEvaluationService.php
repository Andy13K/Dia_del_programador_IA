<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use Illuminate\Support\Facades\DB;

class AlertEvaluationService
{
    public function evaluateGeneration(EnergyGeneration $generation): ?GenerationAlert
    {
        return DB::transaction(function () use ($generation): ?GenerationAlert {
            // El bloqueo de la medición evita alertas duplicadas en evaluaciones concurrentes.
            $generation = EnergyGeneration::query()->lockForUpdate()->findOrFail($generation->getKey());
            $estimated = (float) $generation->estimated_kwh;
            $real = (float) $generation->real_kwh;

            // Sin generación esperada no existe un déficit porcentual definido.
            // Los kWh se almacenan con dos decimales: comparar centésimas evita
            // que el error binario de 0.80 excluya valores justo en el umbral.
            if ($estimated <= 0 || (int) round($real * 100) * 5 > (int) round($estimated * 100) * 4) {
                return null;
            }

            return $generation->generationAlert()->firstOrCreate([], [
                'solar_farm_id' => $generation->solar_farm_id,
                'period' => $generation->period,
                'estimated_kwh' => $estimated,
                'real_kwh' => $real,
                'deviation_percentage' => round((($estimated - $real) / $estimated) * 100, 2),
                'status' => 'active',
            ]);
        });
    }
}
