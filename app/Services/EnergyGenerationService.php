<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EnergyGeneration;
use App\Models\SolarFarm;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnergyGenerationService
{
    public function __construct(
        private CarbonOffsetService $carbon,
        private AlertEvaluationService $alerts,
    ) {}

    public function store(SolarFarm $farm, array $data, int $userId): EnergyGeneration
    {
        return DB::transaction(function () use ($farm, $data, $userId): EnergyGeneration {
            // Serializa el alta mensual por granja, incluyendo solicitudes simultáneas.
            $farm = SolarFarm::query()->lockForUpdate()->findOrFail($farm->getKey());
            if ($farm->energyGenerations()->where('period', $data['period'])->exists()) {
                throw ValidationException::withMessages([
                    'period' => 'Ya existe una medición para esta granja y período.',
                ]);
            }

            $generation = $farm->energyGenerations()->create([
                'period' => $data['period'],
                'record_date' => $data['record_date'],
                'estimated_kwh' => $data['estimated_kwh'],
                'real_kwh' => $data['real_kwh'],
                'co2_kg' => $this->carbon->calculateCO2((float) $data['real_kwh']),
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);
            $generation->setRelation('generationAlert', $this->alerts->evaluateGeneration($generation));

            return $generation;
        });
    }
}
