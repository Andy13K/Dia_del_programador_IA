<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GenerationAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GenerationAlertService
{
    public function resolve(GenerationAlert $alert, string $notes, int $userId): GenerationAlert
    {
        return DB::transaction(function () use ($alert, $notes, $userId): GenerationAlert {
            $alert = GenerationAlert::query()->lockForUpdate()->findOrFail($alert->getKey());
            if ($alert->status !== 'active') {
                throw ValidationException::withMessages([
                    'resolution_notes' => 'Esta alerta ya fue resuelta.',
                ]);
            }
            $alert->fill([
                'status' => 'resolved',
                'resolution_notes' => $notes,
                'resolved_by' => $userId,
                'resolved_at' => now(),
            ])->saveOrFail();

            return $alert;
        });
    }
}
