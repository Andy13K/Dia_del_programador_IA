<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SolarFarm;
use App\Models\User;

class SolarFarmPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SolarFarm $solarFarm): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    public function update(User $user, SolarFarm $solarFarm): bool
    {
        return $user->hasAnyRole(['admin', 'operador']);
    }

    public function delete(User $user, SolarFarm $solarFarm): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, SolarFarm $solarFarm): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, SolarFarm $solarFarm): bool
    {
        return $user->hasRole('admin');
    }
}
