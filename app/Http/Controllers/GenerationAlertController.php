<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IndexGenerationAlertRequest;
use App\Http\Requests\ResolveGenerationAlertRequest;
use App\Models\GenerationAlert;
use App\Services\BackendAccessService;
use App\Services\BackendAuditService;
use App\Services\GenerationAlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GenerationAlertController extends Controller
{
    public function __construct(
        private BackendAccessService $access,
        private GenerationAlertService $alerts,
        private BackendAuditService $audit,
    ) {}

    public function index(IndexGenerationAlertRequest $request): View
    {
        Gate::authorize('viewAny', GenerationAlert::class);
        $filters = $request->validated();
        $farms = $this->access->farms($request->user())->orderBy('name')->get();
        $query = GenerationAlert::query()->whereIn('solar_farm_id', $farms->modelKeys());
        if (! empty($filters['solar_farm_id'])) {
            $query->where('solar_farm_id', $filters['solar_farm_id']);
        }
        $stats = [
            'active' => (clone $query)->where('status', 'active')->count(),
            'resolved' => (clone $query)->where('status', 'resolved')->count(),
            'total' => (clone $query)->count(),
        ];
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $alerts = $query->with(['solarFarm', 'energyGeneration', 'resolvedBy'])
            ->orderByDesc('created_at')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('alerts.index', compact('alerts', 'farms', 'stats', 'filters'));
    }

    public function show(Request $request, GenerationAlert $alert): View
    {
        Gate::authorize('view', $alert);
        $this->access->farm($request->user(), (int) $alert->solar_farm_id);
        $alert->load(['solarFarm', 'energyGeneration', 'resolvedBy']);

        return view('alerts.show', compact('alert'));
    }

    public function resolve(ResolveGenerationAlertRequest $request, GenerationAlert $alert): RedirectResponse
    {
        Gate::authorize('update', $alert);
        $this->access->farm($request->user(), (int) $alert->solar_farm_id, true);
        DB::transaction(function () use ($request, $alert): void {
            $alert = $this->alerts->resolve(
                $alert, $request->validated('resolution_notes'), (int) $request->user()->getAuthIdentifier(),
            );
            $this->audit->record($request, 'resolved', $alert);
        });

        return redirect()->route('alerts.index')->with('success', 'Alerta resuelta correctamente.');
    }

    public function notifications(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', GenerationAlert::class);
        $farms = $this->access->farms($request->user());

        $alerts = GenerationAlert::query()
            ->whereIn('solar_farm_id', $farms->modelKeys())
            ->where('status', 'active')
            ->with(['solarFarm.department'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(15)
            ->get();

        $items = $alerts->map(function ($alert) {
            return [
                'id' => $alert->id,
                'farm_name' => $alert->solarFarm?->name ?? 'Granja Solar',
                'department_name' => $alert->solarFarm?->department?->name ?? 'Guatemala',
                'period' => $alert->period,
                'deviation_percentage' => (float) $alert->deviation_percentage,
                'estimated_kwh' => number_format((float) $alert->estimated_kwh, 1),
                'real_kwh' => number_format((float) $alert->real_kwh, 1),
                'notes' => $alert->notes ?? 'Déficit de generación fotovoltaica ≥ 20%.',
                'created_at_human' => $alert->created_at ? $alert->created_at->diffForHumans() : 'Reciente',
                'show_url' => route('alerts.show', $alert),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $items->count(),
            'alerts' => $items,
        ]);
    }
}
