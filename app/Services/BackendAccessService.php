<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SolarFarm;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class BackendAccessService
{
    public function farms(User $user, bool $forWriting = false): Builder
    {
        $query = SolarFarm::query();
        // La lectura nacional es un permiso explícito; escribir exige propiedad o administración.
        if (! Gate::forUser($user)->allows('manage-users')
            && ($forWriting || ! Gate::forUser($user)->allows('view-reports'))) {
            $query->where('created_by', $user->getKey());
        }

        return $query;
    }

    public function farm(User $user, int $id, bool $forWriting = false): SolarFarm
    {
        return $this->farms($user, $forWriting)->findOrFail($id);
    }
}
