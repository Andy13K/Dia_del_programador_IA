<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GenerateForecastRequest;
use App\Services\BackendAccessService;
use App\Services\BackendAuditService;
use App\Services\ForecastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ForecastController extends Controller
{
    public function generate(GenerateForecastRequest $request, ForecastService $service, BackendAccessService $access, BackendAuditService $audit): RedirectResponse
    {
        Gate::authorize('manage-forecasts');
        $query = $access->farms($request->user(), true);
        if ($request->has('solar_farm_id')) {
            $query->whereKey($request->integer('solar_farm_id'));
        }
        $farms = $query->orderBy('id')->get();
        if ($farms->isEmpty()) {
            throw ValidationException::withMessages(['solar_farm_id' => 'No hay granjas autorizadas para generar proyecciones.']);
        }
        DB::transaction(function () use ($farms, $request, $service, $audit): void {
            foreach ($farms as $farm) {
                Gate::authorize('update', $farm);
                $forecast = $service->generateForecast($farm, $request->validated('target_period'));
                $audit->record($request, 'forecast_generated', $forecast);
            }
        });

        return redirect()->route('forecasts.index')->with('success', 'Proyecciones SMA-SF guardadas: '.$farms->count().'.');
    }
}
