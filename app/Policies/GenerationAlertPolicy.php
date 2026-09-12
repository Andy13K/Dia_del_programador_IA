<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GenerationAlert;
use App\Models\User;

class GenerationAlertPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GenerationAlert $generationAlert): bool
    {
        return true;
    }

    /**
     * Las alertas las genera el sistema automáticamente (RF-14); solo se autoriza
     * su resolución manual, no su creación/edición directa por el usuario.
     */
    public function resolve(User $user, GenerationAlert $generationAlert): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }
}
