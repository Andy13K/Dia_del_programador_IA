<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IndexEnergyGenerationRequest;
use App\Http\Requests\StoreEnergyGenerationRequest;
use App\Models\EnergyGeneration;
use App\Services\BackendAccessService;
use App\Services\BackendAuditService;
use App\Services\EnergyGenerationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class EnergyGenerationController extends Controller
{
    public function __construct(
        private BackendAccessService $access,
        private EnergyGenerationService $generations,
        private BackendAuditService $audit,
    ) {}

    public function index(IndexEnergyGenerationRequest $request): View
    {
        Gate::authorize('viewAny', EnergyGeneration::class);
        $filters = $request->validated();
        $farms = $this->access->farms($request->user())->orderBy('name')->get();
        $query = EnergyGeneration::query()->whereIn('solar_farm_id', $farms->modelKeys())
            ->with(['solarFarm', 'generationAlert']);
        if (! empty($filters['solar_farm_id'])) {
            $query->where('solar_farm_id', $filters['solar_farm_id']);
        }
        if (! empty($filters['period'])) {
            $query->where('period', $filters['period']);
        }
        $generations = $query->orderByDesc('period')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('generations.index', compact('generations', 'farms', 'filters'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', EnergyGeneration::class);
        $farms = $this->access->farms($request->user(), true)->orderBy('name')->get();

        return view('generations.create', compact('farms'));
    }

    public function store(StoreEnergyGenerationRequest $request): RedirectResponse
    {
        Gate::authorize('create', EnergyGeneration::class);
        $farm = $this->access->farm($request->user(), $request->integer('solar_farm_id'), true);
        Gate::authorize('update', $farm);
        $generation = DB::transaction(function () use ($request, $farm): EnergyGeneration {
            $generation = $this->generations->store(
                $farm, $request->validated(), (int) $request->user()->getAuthIdentifier(),
            );
            $this->audit->record($request, 'created', $generation);
            if ($generation->generationAlert !== null) {
                $this->audit->record($request, 'created', $generation->generationAlert);
            }

            return $generation;
        });

        if ($generation->generationAlert !== null) {
            return redirect()->route('generations.index')->with('warning',
                'Medición registrada. Se generó una alerta por déficit de '.$generation->generationAlert->deviation_percentage.'%.');
        }

        return redirect()->route('generations.index')->with('success', 'Medición registrada correctamente.');
    }

    public function show(Request $request, EnergyGeneration $generation): View
    {
        Gate::authorize('view', $generation);
        $this->access->farm($request->user(), (int) $generation->solar_farm_id);
        $generation->load(['solarFarm', 'generationAlert']);

        return view('generations.show', compact('generation'));
    }
}
