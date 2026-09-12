<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SolarFarm;
use App\Models\SolarPanel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SolarFarmService
{
    public function save(SolarFarm $farm, array $data): SolarFarm
    {
        return DB::transaction(function () use ($farm, $data): SolarFarm {
            if ($farm->exists) {
                $farm = SolarFarm::query()->lockForUpdate()->findOrFail($farm->getKey());
            }
            $farm->fill(Arr::except($data, ['panels']))->saveOrFail();

            // Omitir panels conserva la asignación; enviar [] la elimina explícitamente.
            if (array_key_exists('panels', $data)) {
                $assignments = [];
                foreach ($data['panels'] as $item) {
                    $panel = SolarPanel::query()->lockForUpdate()->findOrFail($item['panel_id']);
                    Gate::authorize('view', $panel);
                    $assignments[$panel->getKey()] = ['quantity' => $item['quantity']];
                }
                $farm->solarPanels()->sync($assignments);
            }

            return $farm;
        });
    }
}
