<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnergyGenerationController;
use App\Http\Controllers\GenerationAlertController;
use App\Http\Controllers\ScadaSimulatorController;
use App\Http\Controllers\SolarFarmController;
use App\Http\Controllers\SolarPanelController;
use App\Http\Controllers\TelemetrySimulationController;
use App\Models\Department;
use App\Models\EnergyGeneration;
use App\Models\GenerationAlert;
use App\Models\SolarFarm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web del Sistema Solar Guatemala
|--------------------------------------------------------------------------
|
| Nombres y middleware congelados en docs/06-CONTRATOS-HORA-1.md (sección 2).
| Todas las rutas de gestión protegidas bajo 'auth' y Gates/Policies correspondientes.
|
*/

// Handler del Dashboard Ejecutivo Nacional (RF-11)
$dashboardHandler = function () {
    // OWASP A10: fallar cerrado. Antes esto atrapaba cualquier error de BD y lo
    // reemplazaba con cifras de demostración fijas (incluso con la BD sana pero
    // en cero), mostrando datos falsos como si fueran reales. Si algo falla acá,
    // debe verse como el error 500 que es, no como un dashboard "exitoso".
    $farms = SolarFarm::with(['solarPanels', 'department'])->get();

    $stats = [
        'total_farms' => $farms->count(),
        'total_panels' => (int) DB::table('farm_panel')->sum('quantity'),
        'total_capacity_kw' => (float) $farms->sum(fn ($f) => $f->calculated_capacity_kw),
        'total_kwh' => (float) EnergyGeneration::sum('real_kwh'),
        'total_families' => (int) $farms->sum('benefited_families'),
        'total_co2_kg' => (float) EnergyGeneration::sum('co2_kg'),
    ];

    $activeAlerts = GenerationAlert::with(['solarFarm.department', 'energyGeneration'])
        ->where('status', 'active')
        ->latest()
        ->take(5)
        ->get();

    // Top 5 Ranking Departamental por Generación Real
    $deptGenerations = DB::table('departments')
        ->join('solar_farms', 'departments.id', '=', 'solar_farms.department_id')
        ->join('energy_generations', 'solar_farms.id', '=', 'energy_generations.solar_farm_id')
        ->select('departments.name', DB::raw('SUM(energy_generations.real_kwh) as total_kwh'))
        ->groupBy('departments.id', 'departments.name')
        ->orderByDesc('total_kwh')
        ->take(5)
        ->get();

    $topRanking = $deptGenerations->map(function ($row) use ($stats) {
        $kwh = (float) $row->total_kwh;
        $co2Ton = ($kwh * 0.40) / 1000;
        $share = $stats['total_kwh'] > 0 ? round(($kwh / $stats['total_kwh']) * 100, 1) : 0;

        return [
            'name' => $row->name,
            'kwh' => $kwh,
            'co2' => number_format($co2Ton, 1).' Ton',
            'share' => $share,
        ];
    })->all();

    // Gráfica de últimos 6 períodos
    $monthlyGens = DB::table('energy_generations')
        ->select('period', DB::raw('SUM(estimated_kwh) as exp_kwh'), DB::raw('SUM(real_kwh) as act_kwh'))
        ->groupBy('period')
        ->orderBy('period')
        ->take(6)
        ->get();

    $chartLabels = $monthlyGens->pluck('period')->all();
    $chartExpected = $monthlyGens->pluck('exp_kwh')->map(fn ($v) => (float) $v)->all();
    $chartReal = $monthlyGens->pluck('act_kwh')->map(fn ($v) => (float) $v)->all();

    return view('dashboard', compact('farms', 'stats', 'activeAlerts', 'topRanking', 'chartLabels', 'chartExpected', 'chartReal'));
};

// Rutas Principales: Inicio y Dashboard
Route::get('/', $dashboardHandler)->name('home');
Route::get('/dashboard', $dashboardHandler)->middleware('auth')->name('dashboard');
Route::post('/telemetry/simulate', [TelemetrySimulationController::class, 'simulate'])
    ->middleware(['auth', 'can:manage-generations'])
    ->name('telemetry.simulate');

// Laboratorio y Centro de Control SCADA IoT en Tiempo Real
Route::get('/simulator', [ScadaSimulatorController::class, 'index'])->middleware('auth')->name('simulator.index');
Route::post('/simulator/event', [ScadaSimulatorController::class, 'recordEvent'])->middleware(['auth', 'can:manage-generations'])->name('simulator.event');
Route::post('/simulator/reset', [ScadaSimulatorController::class, 'resetTelemetry'])->middleware(['auth', 'can:manage-generations'])->name('simulator.reset');

// Autenticación (OWASP A01/A06/A07): throttle:5,1 limita fuerza bruta en el intento de login.
Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Granjas Solares (RF-03 a RF-07)
Route::get('/farms', [SolarFarmController::class, 'index'])->middleware('auth')->name('farms.index');
Route::get('/farms/create', [SolarFarmController::class, 'create'])->middleware(['auth', 'can:manage-farms'])->name('farms.create');
Route::post('/farms', [SolarFarmController::class, 'store'])->middleware(['auth', 'can:manage-farms'])->name('farms.store');
Route::get('/farms/{farm}', [SolarFarmController::class, 'show'])->middleware('auth')->name('farms.show');
Route::get('/farms/{farm}/edit', [SolarFarmController::class, 'edit'])->middleware(['auth', 'can:manage-farms'])->name('farms.edit');
Route::put('/farms/{farm}', [SolarFarmController::class, 'update'])->middleware(['auth', 'can:manage-farms'])->name('farms.update');
Route::delete('/farms/{farm}', [SolarFarmController::class, 'destroy'])->middleware(['auth', 'can:manage-farms'])->name('farms.destroy');

// Catálogo de Paneles (RF-02)
Route::get('/panels', [SolarPanelController::class, 'index'])->middleware('auth')->name('panels.index');
Route::get('/panels/create', [SolarPanelController::class, 'create'])->middleware(['auth', 'can:manage-panels'])->name('panels.create');
Route::post('/panels', [SolarPanelController::class, 'store'])->middleware(['auth', 'can:manage-panels'])->name('panels.store');
Route::get('/panels/{panel}/edit', [SolarPanelController::class, 'edit'])->middleware(['auth', 'can:manage-panels'])->name('panels.edit');
Route::put('/panels/{panel}', [SolarPanelController::class, 'update'])->middleware(['auth', 'can:manage-panels'])->name('panels.update');
Route::delete('/panels/{panel}', [SolarPanelController::class, 'destroy'])->middleware(['auth', 'can:manage-panels'])->name('panels.destroy');

// Mediciones de Generación (RF-08 a RF-10)
Route::get('/generations', [EnergyGenerationController::class, 'index'])->middleware('auth')->name('generations.index');
Route::get('/generations/create', [EnergyGenerationController::class, 'create'])->middleware(['auth', 'can:manage-generations'])->name('generations.create');
Route::post('/generations', [EnergyGenerationController::class, 'store'])->middleware(['auth', 'can:manage-generations'])->name('generations.store');
Route::get('/generations/{generation}', [EnergyGenerationController::class, 'show'])->middleware('auth')->name('generations.show');

// Mapa Interactivo de Guatemala (RF-13)
Route::get('/map', function () {
    // OWASP A10: fallar cerrado (ver nota en el handler del dashboard más arriba).
    $departments = Department::orderBy('name')->get();
    $farms = SolarFarm::with(['department', 'solarPanels', 'energyGenerations', 'generationAlerts' => fn ($q) => $q->where('status', 'active')])->get();
    $farmsJson = $farms->map(function ($f) {
        $recentGen = $f->energyGenerations->sortByDesc('period')->first();

        return [
            'id' => $f->id,
            'name' => $f->name,
            'dept_id' => $f->department_id,
            'dept_name' => $f->department->name ?? 'Guatemala',
            'lat' => (float) $f->latitude,
            'lng' => (float) $f->longitude,
            'capacity_kw' => (float) $f->calculated_capacity_kw,
            'families' => (int) $f->benefited_families,
            'status' => $f->status,
            'has_alert' => $f->generationAlerts->isNotEmpty(),
            'monthly_kwh' => (float) ($recentGen?->real_kwh ?? 0),
        ];
    });

    return view('map.index', compact('departments', 'farmsJson'));
})->middleware('auth')->name('map.index');

// Alertas de Generación (RF-14)
Route::get('/alerts', [GenerationAlertController::class, 'index'])->middleware('auth')->name('alerts.index');
Route::get('/alerts/notifications', [GenerationAlertController::class, 'notifications'])->middleware('auth')->name('alerts.notifications');
Route::get('/alerts/{alert}', [GenerationAlertController::class, 'show'])->middleware('auth')->name('alerts.show');
Route::post('/alerts/{alert}/resolve', [GenerationAlertController::class, 'resolve'])->middleware(['auth', 'can:manage-alerts'])->name('alerts.resolve');

// Gestión de Usuarios y Roles (RBAC / OWASP A01)
Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->middleware(['auth', 'can:manage-users'])->name('users.index');
Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->middleware(['auth', 'can:manage-users'])->name('users.create');
Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->middleware(['auth', 'can:manage-users'])->name('users.store');
Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->middleware(['auth', 'can:manage-users'])->name('users.edit');
Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->middleware(['auth', 'can:manage-users'])->name('users.update');
Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->middleware(['auth', 'can:manage-users'])->name('users.destroy');

// Reportes Departamentales y Exportación Profesional (RF-12)
Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->middleware('auth')->name('reports.index');
Route::get('/reports/export/excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->middleware('auth')->name('reports.export.excel');
Route::get('/reports/export/csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->middleware('auth')->name('reports.export.csv');
Route::get('/reports/export', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->middleware('auth')->name('reports.export');
Route::get('/reports/print', [\App\Http\Controllers\ReportController::class, 'printPdf'])->middleware('auth')->name('reports.print');
Route::get('/reports/department/{department}', fn (string $department) => redirect()->route('reports.index'))->middleware('auth')->name('reports.department');

// Proyecciones Predictivas SMA-SF (RF-15)
Route::get('/forecasts', [\App\Http\Controllers\ForecastController::class, 'index'])->middleware('auth')->name('forecasts.index');
Route::post('/forecasts/generate', [\App\Http\Controllers\ForecastController::class, 'generate'])->middleware(['auth', 'can:manage-forecasts'])->name('forecasts.generate');

// Documentación de API REST (RF-16)
Route::get('/api-docs', fn () => view('api-docs.index'))->name('api.docs');
