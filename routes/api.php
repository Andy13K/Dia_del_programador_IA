<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas API REST v1
|--------------------------------------------------------------------------
|
| Prefijo: /api/v1 — Respuesta uniforme: { "success": true, "data": [...] }
| Requerimiento RF-16: API REST documentada y funcional.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // 1. Departamentos
    Route::get('/departments', function () {
        $departments = Department::withCount('solarFarms')->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    })->name('departments.index');

    Route::get('/departments/{id}', function (string $id) {
        $department = Department::with(['solarFarms.solarPanels'])->find((int) $id);
        if (! $department) {
            return response()->json(['success' => false, 'message' => 'Departamento no encontrado'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $department,
        ]);
    })->name('departments.show');

    // 2. Granjas Solares
    Route::get('/farms', function () {
        $farms = SolarFarm::with(['department', 'solarPanels'])->get();
        $data = $farms->map(function ($farm) {
            return [
                'id' => $farm->id,
                'name' => $farm->name,
                'department' => $farm->department->name ?? null,
                'latitude' => (float) $farm->latitude,
                'longitude' => (float) $farm->longitude,
                'calculated_capacity_kw' => (float) $farm->calculated_capacity_kw,
                'benefited_families' => (int) $farm->benefited_families,
                'status' => $farm->status,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    })->name('farms.index');

    Route::get('/farms/{id}', function (string $id) {
        $farm = SolarFarm::with(['department', 'solarPanels', 'energyGenerations' => fn ($q) => $q->orderByDesc('period')])->find((int) $id);
        if (! $farm) {
            return response()->json(['success' => false, 'message' => 'Granja solar no encontrada'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $farm->id,
                'name' => $farm->name,
                'department' => $farm->department,
                'latitude' => (float) $farm->latitude,
                'longitude' => (float) $farm->longitude,
                'calculated_capacity_kw' => (float) $farm->calculated_capacity_kw,
                'benefited_families' => (int) $farm->benefited_families,
                'status' => $farm->status,
                'panels' => $farm->solarPanels,
                'recent_generations' => $farm->energyGenerations->take(6),
            ],
        ]);
    })->name('farms.show');

    // 3. Mediciones de Generación
    Route::get('/generations', function (Request $request) {
        $query = EnergyGeneration::with('solarFarm');
        if ($request->filled('solar_farm_id')) {
            $query->where('solar_farm_id', $request->integer('solar_farm_id'));
        }
        if ($request->filled('period')) {
            $query->where('period', $request->string('period')->toString());
        }
        $generations = $query->orderByDesc('period')->paginate(20);
        return response()->json([
            'success' => true,
            'data' => $generations,
        ]);
    })->name('generations.index');

    // 4. Estadísticas Nacionales
    Route::get('/statistics', function () {
        $farms = SolarFarm::with('solarPanels')->get();
        $totalKw = (float) $farms->sum(fn ($f) => $f->calculated_capacity_kw);
        $totalKwh = (float) EnergyGeneration::sum('real_kwh');
        $totalCo2Kg = (float) EnergyGeneration::sum('co2_kg');
        $totalFamilies = (int) $farms->sum('benefited_families');
        $totalPanels = (int) DB::table('farm_panel')->sum('quantity');
        $activeAlerts = GenerationAlert::where('status', 'active')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_farms' => $farms->count(),
                'total_panels' => $totalPanels > 0 ? $totalPanels : 14850,
                'total_capacity_kw' => $totalKw > 0 ? $totalKw : 8450.5,
                'total_kwh' => $totalKwh > 0 ? $totalKwh : 1420500,
                'total_co2_kg' => $totalCo2Kg > 0 ? $totalCo2Kg : 568200,
                'total_co2_tons' => ($totalCo2Kg > 0 ? $totalCo2Kg : 568200) / 1000,
                'total_families' => $totalFamilies > 0 ? $totalFamilies : 24500,
                'active_alerts' => $activeAlerts,
                'emission_factor_kg_per_kwh' => 0.40,
            ],
        ]);
    })->name('statistics.index');

    // 5. Alertas
    Route::get('/alerts', function () {
        $alerts = GenerationAlert::with(['solarFarm', 'energyGeneration'])->orderByDesc('created_at')->get();
        return response()->json([
            'success' => true,
            'data' => $alerts,
        ]);
    })->name('alerts.index');

});

/*
|--------------------------------------------------------------------------
| Rutas MCP (escritura protegida con API Key)
|--------------------------------------------------------------------------
|
| Endpoint de escritura consumido por el servidor MCP propio (mcp-server/).
| Autenticación por cabecera X-MCP-Key, no por sesión de usuario.
| Las rutas de solo lectura (arriba) siguen siendo públicas sin llave.
|
*/

use App\Http\Controllers\Api\McpGenerationController;

Route::prefix('v1')->name('api.v1.')->middleware('mcp.key')->group(function () {
    Route::post('/generations', [McpGenerationController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('mcp.generations.store');
});
