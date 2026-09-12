<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web
|--------------------------------------------------------------------------
|
| Nombres y middleware congelados en docs/06-CONTRATOS-HORA-1.md (sección 2).
| Los controladores reales los implementa el Agente B; mientras tanto cada
| ruta responde con un texto simple para que route() no lance
| RouteNotFoundException y las vistas se puedan enlazar sin errores.
|
*/

Route::get('/', fn () => 'OK: home')->name('home');
Route::get('/dashboard', fn () => 'OK: dashboard')->middleware('auth')->name('dashboard');

// Placeholder temporal: el middleware "auth" redirige aquí si no hay sesión.
// El andamiaje real de autenticación (Breeze/Fortify, ver OWASP A07) queda pendiente
// para un incremento posterior — no forma parte del alcance de este PR.
Route::get('/login', fn () => 'OK: login (placeholder, pendiente andamiaje de autenticación)')->name('login');

// Granjas
Route::get('/farms', fn () => 'OK: farms.index')->middleware('auth')->name('farms.index');
Route::get('/farms/create', fn () => 'OK: farms.create')->middleware(['auth', 'can:manage-farms'])->name('farms.create');
Route::post('/farms', fn () => 'OK: farms.store')->middleware(['auth', 'can:manage-farms'])->name('farms.store');
Route::get('/farms/{farm}', fn (string $farm) => "OK: farms.show {$farm}")->middleware('auth')->name('farms.show');
Route::get('/farms/{farm}/edit', fn (string $farm) => "OK: farms.edit {$farm}")->middleware(['auth', 'can:manage-farms'])->name('farms.edit');
Route::put('/farms/{farm}', fn (string $farm) => "OK: farms.update {$farm}")->middleware(['auth', 'can:manage-farms'])->name('farms.update');
Route::delete('/farms/{farm}', fn (string $farm) => "OK: farms.destroy {$farm}")->middleware(['auth', 'can:manage-farms'])->name('farms.destroy');

// Paneles
Route::get('/panels', fn () => 'OK: panels.index')->middleware('auth')->name('panels.index');
Route::get('/panels/create', fn () => 'OK: panels.create')->middleware(['auth', 'can:manage-panels'])->name('panels.create');
Route::post('/panels', fn () => 'OK: panels.store')->middleware(['auth', 'can:manage-panels'])->name('panels.store');
Route::get('/panels/{panel}/edit', fn (string $panel) => "OK: panels.edit {$panel}")->middleware(['auth', 'can:manage-panels'])->name('panels.edit');
Route::put('/panels/{panel}', fn (string $panel) => "OK: panels.update {$panel}")->middleware(['auth', 'can:manage-panels'])->name('panels.update');
Route::delete('/panels/{panel}', fn (string $panel) => "OK: panels.destroy {$panel}")->middleware(['auth', 'can:manage-panels'])->name('panels.destroy');

// Generación
Route::get('/generations', fn () => 'OK: generations.index')->middleware('auth')->name('generations.index');
Route::get('/generations/create', fn () => 'OK: generations.create')->middleware(['auth', 'can:manage-generations'])->name('generations.create');
Route::post('/generations', fn () => 'OK: generations.store')->middleware(['auth', 'can:manage-generations'])->name('generations.store');
Route::get('/generations/{generation}', fn (string $generation) => "OK: generations.show {$generation}")->middleware('auth')->name('generations.show');

// Mapa Interactivo
Route::get('/map', fn () => 'OK: map.index')->middleware('auth')->name('map.index');

// Alertas
Route::get('/alerts', fn () => 'OK: alerts.index')->middleware('auth')->name('alerts.index');
Route::get('/alerts/{alert}', fn (string $alert) => "OK: alerts.show {$alert}")->middleware('auth')->name('alerts.show');
Route::post('/alerts/{alert}/resolve', fn (string $alert) => "OK: alerts.resolve {$alert}")->middleware(['auth', 'can:manage-alerts'])->name('alerts.resolve');

// Reportes
Route::get('/reports', fn () => 'OK: reports.index')->middleware('auth')->name('reports.index');
Route::get('/reports/department/{department}', fn (string $department) => "OK: reports.department {$department}")->middleware('auth')->name('reports.department');
Route::get('/reports/export', fn () => 'OK: reports.export')->middleware('auth')->name('reports.export');

// Proyecciones
Route::get('/forecasts', fn () => 'OK: forecasts.index')->middleware('auth')->name('forecasts.index');
Route::post('/forecasts/generate', fn () => 'OK: forecasts.generate')->middleware(['auth', 'can:manage-forecasts'])->name('forecasts.generate');

// Documentación API
Route::get('/api-docs', fn () => 'OK: api-docs.index')->name('api.docs');
