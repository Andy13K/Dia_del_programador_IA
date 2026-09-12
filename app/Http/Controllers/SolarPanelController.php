<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolarPanelRequest;
use App\Http\Requests\UpdateSolarPanelRequest;
use App\Models\SolarPanel;
use App\Services\BackendAuditService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SolarPanelController extends Controller
{
    public function __construct(private BackendAuditService $audit) {}

    public function index(): View
    {
        Gate::authorize('viewAny', SolarPanel::class);

        return view('panels.index', ['panels' => SolarPanel::query()->orderBy('brand')->orderBy('id')->paginate(15)]);
    }

    public function create(): View
    {
        Gate::authorize('create', SolarPanel::class);

        return view('panels.create');
    }

    public function store(StoreSolarPanelRequest $request): RedirectResponse
    {
        Gate::authorize('create', SolarPanel::class);
        DB::transaction(function () use ($request): void {
            $panel = SolarPanel::query()->create($request->validated());
            $this->audit->record($request, 'created', $panel);
        });

        return redirect()->route('panels.index')->with('success', 'Panel solar registrado correctamente.');
    }

    public function edit(SolarPanel $panel): View
    {
        Gate::authorize('update', $panel);

        return view('panels.edit', compact('panel'));
    }

    public function update(UpdateSolarPanelRequest $request, SolarPanel $panel): RedirectResponse
    {
        Gate::authorize('update', $panel);
        DB::transaction(function () use ($request, $panel): void {
            $panel->fill($request->validated())->saveOrFail();
            $this->audit->record($request, 'updated', $panel);
        });

        return redirect()->route('panels.index')->with('success', 'Panel solar actualizado correctamente.');
    }

    public function destroy(Request $request, SolarPanel $panel): RedirectResponse
    {
        Gate::authorize('delete', $panel);
        DB::transaction(function () use ($request, $panel): void {
            $panel->deleteOrFail();
            $this->audit->record($request, 'deleted', $panel);
        });

        return redirect()->route('panels.index')->with('success', 'Panel solar eliminado correctamente.');
    }
}
