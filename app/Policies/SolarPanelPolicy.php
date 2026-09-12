<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SolarPanel;
use App\Models\User;

class SolarPanelPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SolarPanel $solarPanel): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    public function update(User $user, SolarPanel $solarPanel): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    public function delete(User $user, SolarPanel $solarPanel): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    public function restore(User $user, SolarPanel $solarPanel): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    /**
     * Borrado permanente: se restringe solo a admin por ser irreversible.
     */
    public function forceDelete(User $user, SolarPanel $solarPanel): bool
    {
        return $user->hasRole('admin');
    }
}
