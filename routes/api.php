<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas API REST v1
|--------------------------------------------------------------------------
|
| Nombres y prefijo congelados en docs/06-CONTRATOS-HORA-1.md (sección 2).
| Respuesta uniforme: { "success": true, "data": [...] }.
| Los controladores reales los implementa el Agente B; mientras tanto cada
| ruta responde con un JSON de referencia para que route() no lance
| RouteNotFoundException.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/departments', fn () => response()->json(['success' => true, 'data' => []]))->name('departments.index');
    Route::get('/departments/{id}', fn (string $id) => response()->json(['success' => true, 'data' => ['id' => $id]]))->name('departments.show');

    Route::get('/farms', fn () => response()->json(['success' => true, 'data' => []]))->name('farms.index');
    Route::get('/farms/{id}', fn (string $id) => response()->json(['success' => true, 'data' => ['id' => $id]]))->name('farms.show');

    Route::get('/generations', fn (Request $request) => response()->json(['success' => true, 'data' => []]))->name('generations.index');

    Route::get('/statistics', fn () => response()->json(['success' => true, 'data' => []]))->name('statistics.index');

    Route::get('/alerts', fn () => response()->json(['success' => true, 'data' => []]))->name('alerts.index');
});
