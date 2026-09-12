<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IndexSolarFarmRequest;
use App\Http\Requests\StoreSolarFarmRequest;
use App\Http\Requests\UpdateSolarFarmRequest;
use App\Models\Department;
use App\Models\SolarFarm;
use App\Models\SolarPanel;
use App\Services\BackendAccessService;
use App\Services\BackendAuditService;
use App\Services\SolarFarmService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SolarFarmController extends Controller
{
    public function __construct(
        private BackendAccessService $access,
        private SolarFarmService $farms,
        private BackendAuditService $audit,
    ) {}

    public function index(IndexSolarFarmRequest $request): View
    {
        Gate::authorize('viewAny', SolarFarm::class);
        $filters = $request->validated();
        $query = $this->access->farms($request->user())->with(['department', 'solarPanels']);
        if (! empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['search']) && $filters['search'] !== '') {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }
        $farms = $query->orderBy('name')->orderBy('id')->paginate(15)->withQueryString();
        $farms->getCollection()->each(fn (SolarFarm $farm) => $farm->append('calculated_capacity_kw'));
        $departments = Department::query()->orderBy('name')->get();

        return view('farms.index', compact('farms', 'departments', 'filters'));
    }

    public function create(): View
    {
        Gate::authorize('create', SolarFarm::class);

        return view('farms.create', $this->formOptions());
    }

    public function store(StoreSolarFarmRequest $request): RedirectResponse
    {
        Gate::authorize('create', SolarFarm::class);
        $farm = DB::transaction(function () use ($request): SolarFarm {
            $farm = $this->farms->save(new SolarFarm, [
                ...$request->validated(), 'created_by' => $request->user()->getAuthIdentifier(),
            ]);
            $this->audit->record($request, 'created', $farm);

            return $farm;
        });

        return redirect()->route('farms.show', $farm)->with('success', 'Granja solar registrada correctamente.');
    }

    public function show(Request $request, SolarFarm $farm): View
    {
        Gate::authorize('view', $farm);
        $farm = $this->access->farm($request->user(), (int) $farm->getKey());
        $farm->load(['department', 'solarPanels', 'energyGenerations' => fn ($query) => $query->orderByDesc('period'),
            'generationAlerts', 'generationForecasts']);
        $farm->append('calculated_capacity_kw');

        return view('farms.show', compact('farm'));
    }

    public function edit(Request $request, SolarFarm $farm): View
    {
        Gate::authorize('update', $farm);
        $farm = $this->access->farm($request->user(), (int) $farm->getKey(), true);
        $farm->load('solarPanels');

        return view('farms.edit', ['farm' => $farm, ...$this->formOptions()]);
    }

    public function update(UpdateSolarFarmRequest $request, SolarFarm $farm): RedirectResponse
    {
        Gate::authorize('update', $farm);
        $farm = $this->access->farm($request->user(), (int) $farm->getKey(), true);
        $farm = DB::transaction(function () use ($request, $farm): SolarFarm {
            $farm = $this->farms->save($farm, $request->validated());
            $this->audit->record($request, 'updated', $farm);

            return $farm;
        });

        return redirect()->route('farms.show', $farm)->with('success', 'Granja solar actualizada correctamente.');
    }

    public function destroy(Request $request, SolarFarm $farm): RedirectResponse
    {
        Gate::authorize('delete', $farm);
        $farm = $this->access->farm($request->user(), (int) $farm->getKey(), true);
        DB::transaction(function () use ($request, $farm): void {
            $farm->deleteOrFail();
            $this->audit->record($request, 'deleted', $farm);
        });

        return redirect()->route('farms.index')->with('success', 'Granja solar eliminada correctamente.');
    }

    private function formOptions(): array
    {
        return [
            'departments' => Department::query()->orderBy('name')->get(),
            'panels' => SolarPanel::query()->orderBy('brand')->orderBy('model')->get(),
        ];
    }
}
