<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EnergyGeneration;
use App\Models\User;

class EnergyGenerationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, EnergyGeneration $energyGeneration): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    public function update(User $user, EnergyGeneration $energyGeneration): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    /**
     * Eliminar un registro histórico de generación es más sensible que crearlo:
     * se restringe solo a admin.
     */
    public function delete(User $user, EnergyGeneration $energyGeneration): bool
    {
        return $user->hasRole('admin');
    }
}
