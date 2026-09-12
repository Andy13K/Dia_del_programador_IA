<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GenerateForecastRequest;
use App\Models\EnergyGeneration;
use App\Models\GenerationForecast;
use App\Models\SolarFarm;
use App\Services\BackendAccessService;
use App\Services\BackendAuditService;
use App\Services\ForecastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ForecastController extends Controller
{
    public function index(Request $request, BackendAccessService $access, ForecastService $service): View
    {
        $authorizedFarmIds = $access->farms($request->user(), false)->pluck('id');

        // Períodos disponibles para el filtro (del más reciente al más antiguo).
        $availablePeriods = GenerationForecast::query()
            ->whereIn('solar_farm_id', $authorizedFarmIds)
            ->distinct()
            ->orderByDesc('target_period')
            ->pluck('target_period');

        // Período mostrado, por prioridad: ?period=all (sin filtro) > ?period=YYYY-MM >
        // el recién generado (flash de sesión) > el más reciente que exista.
        $requested = $request->query('period');
        $isValidPeriod = is_string($requested) && preg_match('/^[1-9][0-9]{3}-(0[1-9]|1[0-2])$/D', $requested) === 1;
        $selectedPeriod = match (true) {
            $requested === 'all' => null,
            $isValidPeriod => $requested,
            default => $request->session()->get('forecast_period') ?? $availablePeriods->first(),
        };

        $query = GenerationForecast::with(['solarFarm.department'])
            ->whereIn('solar_farm_id', $authorizedFarmIds);
        if ($selectedPeriod !== null) {
            $query->where('target_period', $selectedPeriod);
        }
        $forecasts = $query
            ->orderByDesc('target_period')
            ->orderBy(SolarFarm::query()->select('name')->whereColumn('solar_farms.id', 'generation_forecasts.solar_farm_id'))
            ->paginate(15)
            ->withQueryString();

        // RF-15: comparar la proyección con el resultado real cuando existan datos posteriores.
        foreach ($forecasts as $forecast) {
            if ($forecast->actual_kwh === null) {
                $actual = EnergyGeneration::query()
                    ->where('solar_farm_id', $forecast->solar_farm_id)
                    ->where('period', $forecast->target_period)
                    ->value('real_kwh');

                if ($actual !== null) {
                    $forecast->actual_kwh = (float) $actual;
                    $forecast->saveQuietly();
                }
            }
        }

        $summary = null;
        if ($selectedPeriod !== null) {
            $month = (int) substr($selectedPeriod, 5, 2);
            $summary = [
                'period' => $selectedPeriod,
                'label' => $this->periodLabel($selectedPeriod),
                'factor' => $service->seasonalFactor($month),
                'season' => $month >= 5 && $month <= 10 ? 'lluviosa' : 'seca',
                'count' => $forecasts->total(),
                'total_kwh' => (float) GenerationForecast::query()
                    ->whereIn('solar_farm_id', $authorizedFarmIds)
                    ->where('target_period', $selectedPeriod)
                    ->sum('forecasted_kwh'),
            ];
        }

        $periodOptions = $availablePeriods->mapWithKeys(fn (string $p) => [$p => $this->periodLabel($p)])->all();
        $justGenerated = $request->session()->get('forecast_period');
        $defaultPeriod = $justGenerated ?? now()->startOfMonth()->addMonth()->format('Y-m');

        return view('forecasts.index', compact('forecasts', 'summary', 'selectedPeriod', 'periodOptions', 'justGenerated', 'defaultPeriod'));
    }

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

        $targetPeriod = $request->validated('target_period');

        DB::transaction(function () use ($farms, $request, $service, $audit, $targetPeriod): void {
            foreach ($farms as $farm) {
                Gate::authorize('update', $farm);
                $forecast = $service->generateForecast($farm, $targetPeriod);
                $audit->record($request, 'forecast_generated', $forecast);
            }
        });

        $factor = $service->seasonalFactor((int) substr($targetPeriod, 5, 2));

        return redirect()
            ->route('forecasts.index', ['period' => $targetPeriod])
            ->with('forecast_period', $targetPeriod)
            ->with('success', sprintf(
                'Proyecciones SMA-SF guardadas para %s: %d granja%s, factor estacional %.2f.',
                $this->periodLabel($targetPeriod),
                $farms->count(),
                $farms->count() === 1 ? '' : 's',
                $factor,
            ));
    }

    private function periodLabel(string $period): string
    {
        return Carbon::createFromFormat('Y-m-d', $period.'-01')->locale('es')->translatedFormat('F \d\e Y');
    }
}
