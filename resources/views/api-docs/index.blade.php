@extends('layouts.app', ['title' => 'Documentación API REST v1'])

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">

    <!-- Header API -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 md:p-8 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <span class="px-2.5 py-1 rounded-md bg-violet-500/10 text-violet-600 dark:text-violet-400 font-black text-xs border border-violet-500/20 uppercase">
                RF-16 • RESTful
            </span>
            <span class="text-xs text-slate-400 font-semibold">Versión 1.0</span>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">
            Documentación Oficial de Endpoints de la API
        </h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
            Esta API expone datos en tiempo real de infraestructura solar, mediciones energéticas y cálculo de reducción de emisiones de CO₂ en la República de Guatemala. Todas las respuestas se entregan bajo estándar JSON con código de estado HTTP y encabezados de seguridad.
        </p>

        <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs font-mono">
            <div>
                <span class="text-slate-400">URL Base Pública:</span>
                <strong class="text-amber-600 dark:text-amber-400 ml-2">http://3.238.198.77/api/v1</strong>
            </div>
            <span class="text-[10px] bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold px-2 py-0.5 rounded">CORS Habilitado</span>
        </div>
    </div>

    <!-- LISTADO DE ENDPOINTS -->
    <div class="space-y-6">

        <!-- Endpoint 1: Estadísticas Generales -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-3 font-mono text-sm">
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-black text-xs border border-sky-500/30">GET</span>
                    <span class="font-bold text-slate-800 dark:text-white">/api/v1/statistics</span>
                </div>
                <span class="text-xs text-slate-500">Métricas macro nacionales</span>
            </div>
            <div class="p-5">
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-3">
                    Devuelve el consolidado nacional de granjas, paneles, capacidad en kW, energía acumulada en kWh, familias beneficiadas y reducción certificada de CO₂ en kilogramos y toneladas.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "timestamp": "2026-09-11T18:50:00Z",
  "data": {
    "total_farms": 12,
    "total_panels": 14850,
    "total_capacity_kw": 8450.50,
    "accumulated_generation_kwh": 1420500.00,
    "benefited_families": 24500,
    "co2_reduction_kg": 568200.00,
    "co2_reduction_metric_tons": 568.20,
    "emission_factor_kg_per_kwh": 0.40,
    "active_alerts_count": 2
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Endpoint 2: Departamentos -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-3 font-mono text-sm">
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-black text-xs border border-sky-500/30">GET</span>
                    <span class="font-bold text-slate-800 dark:text-white">/api/v1/departments</span>
                </div>
                <span class="text-xs text-slate-500">Catálogo de los 22 departamentos</span>
            </div>
            <div class="p-5">
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-3">
                    Lista los 22 departamentos de Guatemala con su código oficial, coordenadas GPS de referencia y agregados de capacidad solar.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "total": 22,
  "data": [
    {
      "id": 1,
      "name": "Guatemala",
      "code": "GUA",
      "latitude": 14.6349,
      "longitude": -90.5069,
      "farms_count": 2,
      "total_capacity_kw": 950.00
    },
    {
      "id": 3,
      "name": "Escuintla",
      "code": "ESC",
      "latitude": 14.3009,
      "longitude": -90.7850,
      "farms_count": 3,
      "total_capacity_kw": 2450.50
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        <!-- Endpoint 3: Granjas Solares -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-3 font-mono text-sm">
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-black text-xs border border-sky-500/30">GET</span>
                    <span class="font-bold text-slate-800 dark:text-white">/api/v1/farms</span>
                </div>
                <span class="text-xs text-slate-500">Listado de activos con GPS</span>
            </div>
            <div class="p-5">
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-3">
                    Retorna todas las granjas con su ubicación, departamento asociado, capacidad nominal calculada y familias beneficiadas.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Parque Solar Escuintla Verde",
      "department": { "id": 3, "name": "Escuintla" },
      "coordinates": { "latitude": 14.3009, "longitude": -90.7850 },
      "calculated_capacity_kw": 1850.50,
      "benefited_families": 4200,
      "status": "active"
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        <!-- Endpoint 4: Alertas de Desviación -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-3 font-mono text-sm">
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-black text-xs border border-sky-500/30">GET</span>
                    <span class="font-bold text-slate-800 dark:text-white">/api/v1/alerts</span>
                </div>
                <span class="text-xs text-slate-500">Alertas automáticas por déficit ≥20%</span>
            </div>
            <div class="p-5">
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "active_alerts_count": 1,
  "data": [
    {
      "id": 1,
      "farm_id": 2,
      "farm_name": "Granja Solar Guayacán (Petén)",
      "period": "2026-08",
      "estimated_kwh": 145000.00,
      "real_kwh": 110000.00,
      "deviation_percentage": -24.14,
      "status": "active"
    }
  ]
}</pre>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
