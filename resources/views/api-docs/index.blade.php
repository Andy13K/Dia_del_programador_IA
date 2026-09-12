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
            Esta API expone datos en tiempo real de infraestructura solar, mediciones energéticas y cálculo de reducción de emisiones de CO₂ en la República de Guatemala. Todas las respuestas se entregan bajo estándar JSON con código de estado HTTP y encabezados de seguridad. Es de solo lectura y de acceso público, sin autenticación.
        </p>

        <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs font-mono">
            <div>
                <span class="text-slate-400">URL Base Pública:</span>
                <strong class="text-amber-600 dark:text-amber-400 ml-2">https://kin-solar-guatemala.duckdns.org/api/v1</strong>
            </div>
            <span class="text-[10px] bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold px-2 py-0.5 rounded">CORS Habilitado</span>
        </div>

        <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-950/30 rounded-xl border border-amber-200 dark:border-amber-900/50 text-xs text-amber-800 dark:text-amber-300">
            <strong>Nota sobre tipos:</strong> los campos numéricos que provienen de columnas <code>decimal</code>
            (kWh, CO₂, porcentajes, coordenadas) se serializan como <strong>cadenas de texto</strong>
            (ej. <code>"113055.36"</code>), no como números JSON, por precisión decimal exacta de Laravel/MySQL.
            Los ejemplos de esta página reflejan exactamente el tipo real devuelto por el servidor.
        </div>
    </div>

    <!-- LISTADO DE ENDPOINTS -->
    <div class="space-y-6">

        <!-- Endpoint 1: Departamentos -->
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
                    Lista los 22 departamentos de Guatemala con su código y coordenadas de referencia,
                    junto con la cantidad de granjas solares registradas en cada uno.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Guatemala",
      "code": "GUA",
      "latitude": "14.6349000",
      "longitude": "-90.5069000",
      "created_at": "2026-09-12T00:42:50.000000Z",
      "updated_at": "2026-09-12T00:42:50.000000Z",
      "solar_farms_count": 1
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        <!-- Endpoint 2: Detalle de un departamento -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-3 font-mono text-sm">
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-black text-xs border border-sky-500/30">GET</span>
                    <span class="font-bold text-slate-800 dark:text-white">/api/v1/departments/{id}</span>
                </div>
                <span class="text-xs text-slate-500">Detalle con granjas y paneles</span>
            </div>
            <div class="p-5">
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-3">
                    Devuelve un departamento con sus granjas solares y los paneles asociados a cada una.
                    Responde <code>404</code> con <code>{"success": false, "message": "Departamento no encontrado"}</code>
                    si el ID no existe.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "data": {
    "id": 3,
    "name": "Escuintla",
    "code": "ESC",
    "latitude": "14.3009000",
    "longitude": "-90.7850000",
    "solar_farms": [
      {
        "id": 3,
        "name": "Granja Solar Escuintla Norte",
        "status": "active",
        "solar_panels": [ "..." ]
      }
    ]
  }
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
                    Retorna todas las granjas con su ubicación, departamento asociado, capacidad
                    calculada (suma de potencia × cantidad de sus paneles) y familias beneficiadas.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "data": [
    {
      "id": 3,
      "name": "Granja Solar Escuintla Norte",
      "department": "Escuintla",
      "latitude": 14.335,
      "longitude": -90.77,
      "calculated_capacity_kw": 1010,
      "benefited_families": 1200,
      "status": "active"
    }
  ]
}</pre>
                </div>
            </div>
        </div>

        <!-- Endpoint 4: Generación de energía -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-3 font-mono text-sm">
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-black text-xs border border-sky-500/30">GET</span>
                    <span class="font-bold text-slate-800 dark:text-white">/api/v1/generations</span>
                </div>
                <span class="text-xs text-slate-500">Mediciones por período, paginadas</span>
            </div>
            <div class="p-5">
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-3">
                    Lista las mediciones de generación (estimada vs. real, y CO₂ evitado), ordenadas
                    del período más reciente al más antiguo, paginadas de 20 en 20.
                    Acepta filtros opcionales por query string:
                    <code>?solar_farm_id={id}</code> y <code>?period=AAAA-MM</code>.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "data": {
    "current_page": 1,
    "per_page": 20,
    "total": 6,
    "data": [
      {
        "id": 6,
        "solar_farm_id": 1,
        "period": "2026-08",
        "record_date": "2026-08-31T00:00:00.000000Z",
        "estimated_kwh": "62894.04",
        "real_kwh": "61636.16",
        "co2_kg": "24654.46",
        "solar_farm": { "id": 1, "name": "Granja Solar Villa Nueva", "..." : "..." }
      }
    ]
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Endpoint 5: Estadísticas Generales -->
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
                    Devuelve el consolidado nacional: total de granjas y paneles, capacidad en kW,
                    energía acumulada en kWh, familias beneficiadas, CO₂ evitado en kg y toneladas,
                    y el conteo de alertas activas.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "data": {
    "total_farms": 10,
    "total_panels": 12630,
    "total_capacity_kw": 6710.3,
    "total_kwh": 5048227.46,
    "total_co2_kg": 2019290.98,
    "total_co2_tons": 2019.29098,
    "total_families": 9150,
    "active_alerts": 3,
    "emission_factor_kg_per_kwh": 0.4
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Endpoint 6: Alertas de Desviación -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                <div class="flex items-center gap-3 font-mono text-sm">
                    <span class="px-2.5 py-1 rounded-md bg-sky-500/15 text-sky-600 dark:text-sky-400 font-black text-xs border border-sky-500/30">GET</span>
                    <span class="font-bold text-slate-800 dark:text-white">/api/v1/alerts</span>
                </div>
                <span class="text-xs text-slate-500">Alertas automáticas por déficit ≥20%</span>
            </div>
            <div class="p-5">
                <p class="text-xs text-slate-600 dark:text-slate-300 mb-3">
                    Lista todas las alertas de desviación (generación real ≤ 80% de la esperada),
                    con la granja y la medición de generación que las originó, ordenadas de más
                    reciente a más antigua.
                </p>
                <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>{
  "success": true,
  "data": [
    {
      "id": 1,
      "solar_farm_id": 3,
      "energy_generation_id": 16,
      "period": "2026-06",
      "estimated_kwh": "113055.36",
      "real_kwh": "84791.52",
      "deviation_percentage": "25.00",
      "status": "active",
      "resolution_notes": null,
      "resolved_by": null,
      "resolved_at": null,
      "solar_farm": { "id": 3, "name": "Granja Solar Escuintla Norte", "..." : "..." },
      "energy_generation": { "id": 16, "period": "2026-06", "..." : "..." }
    }
  ]
}</pre>
                </div>
            </div>
        </div>

    </div>

    <!-- Ejemplo de consumo -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-2">Ejemplo de consumo (curl)</h3>
        <div class="bg-slate-950 text-slate-200 rounded-xl p-4 font-mono text-xs overflow-x-auto">
<pre>curl https://kin-solar-guatemala.duckdns.org/api/v1/statistics
curl https://kin-solar-guatemala.duckdns.org/api/v1/farms
curl https://kin-solar-guatemala.duckdns.org/api/v1/departments/1</pre>
        </div>
    </div>

</div>
@endsection
