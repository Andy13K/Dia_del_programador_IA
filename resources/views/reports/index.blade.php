@extends('layouts.app', ['title' => "Centro de Reportes y Exportación — K'in Solar"])

@section('content')
<div class="space-y-6">

    <!-- HEADER INSTITUCIONAL -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold text-xs">
                    REPORTES & AUDITORÍA ENERGÉTICA
                </span>
                <span class="text-xs text-slate-400 font-mono">Factor CNEE {{ number_format((float) config('solar.co2_kg_per_kwh'), 2) }} kg CO₂/kWh<x-co2-info /></span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">
                {{ $reportData['title'] }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $reportData['subtitle'] }}
            </p>
        </div>

        <!-- BOTONES DE EXPORTACIÓN DIRECTA -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('reports.export.excel', request()->all()) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-1.5 transition shadow-sm" title="Descargar reporte formateado en Excel con encabezado y logo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Descargar Excel (.xls)</span>
            </a>

            <a href="{{ route('reports.export.csv', request()->all()) }}" class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold flex items-center gap-1.5 transition" title="Exportar archivo CSV plano con BOM UTF-8">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>CSV UTF-8</span>
            </a>

            <a href="{{ route('reports.print', array_merge(request()->all(), ['auto_print' => 1])) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-extrabold flex items-center gap-1.5 transition shadow-sm" title="Abrir plantilla ejecutiva y guardar como PDF">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Imprimir / Guardar PDF</span>
            </a>
        </div>
    </div>

    <!-- SELECTOR DE TIPOS DE REPORTES (TABS) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $currentType = $reportData['type'];
            $queryParams = request()->except('type', 'page');
        @endphp

        <!-- Tipo 1: Departamental -->
        <a href="{{ route('reports.index', array_merge($queryParams, ['type' => 'departamental'])) }}" class="p-4 rounded-2xl border transition flex items-center space-x-3 {{ $currentType === 'departamental' ? 'bg-amber-500/10 border-amber-500/40 text-amber-600 dark:text-amber-400' : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-slate-300' }}">
            <i data-lucide="landmark" class="w-6 h-6 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <span class="text-xs font-bold block text-slate-900 dark:text-white">Departamental</span>
                <span class="text-[10px] text-slate-400 block leading-tight">22 departamentos</span>
            </div>
        </a>

        <!-- Tipo 2: Granjas -->
        <a href="{{ route('reports.index', array_merge($queryParams, ['type' => 'granjas'])) }}" class="p-4 rounded-2xl border transition flex items-center space-x-3 {{ $currentType === 'granjas' ? 'bg-amber-500/10 border-amber-500/40 text-amber-600 dark:text-amber-400' : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-slate-300' }}">
            <i data-lucide="zap" class="w-6 h-6 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <span class="text-xs font-bold block text-slate-900 dark:text-white">Por Granjas</span>
                <span class="text-[10px] text-slate-400 block leading-tight">Rendimiento solar</span>
            </div>
        </a>

        <!-- Tipo 3: Ambiental -->
        <a href="{{ route('reports.index', array_merge($queryParams, ['type' => 'ambiental'])) }}" class="p-4 rounded-2xl border transition flex items-center space-x-3 {{ $currentType === 'ambiental' ? 'bg-amber-500/10 border-amber-500/40 text-amber-600 dark:text-amber-400' : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-slate-300' }}">
            <i data-lucide="leaf" class="w-6 h-6 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <span class="text-xs font-bold block text-slate-900 dark:text-white">Mitigación CO₂</span>
                <span class="text-[10px] text-slate-400 block leading-tight">Balance ecológico</span>
            </div>
        </a>

        <!-- Tipo 4: Alertas -->
        <a href="{{ route('reports.index', array_merge($queryParams, ['type' => 'alertas'])) }}" class="p-4 rounded-2xl border transition flex items-center space-x-3 {{ $currentType === 'alertas' ? 'bg-amber-500/10 border-amber-500/40 text-amber-600 dark:text-amber-400' : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:border-slate-300' }}">
            <i data-lucide="siren" class="w-6 h-6 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <span class="text-xs font-bold block text-slate-900 dark:text-white">Alertas & Fallas</span>
                <span class="text-[10px] text-slate-400 block leading-tight">Déficit ≥ 20%</span>
            </div>
        </a>
    </div>

    <!-- BARRA DE FILTROS AVANZADOS -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
            <input type="hidden" name="type" value="{{ $reportData['type'] }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <!-- Fecha Inicio -->
                <div>
                    <label for="start_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Fecha Inicial (Desde):
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date', $reportData['start_date']) }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Fecha Fin -->
                <div>
                    <label for="end_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Fecha Final (Hasta):
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date', $reportData['end_date']) }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Departamento Opcional -->
                <div>
                    <label for="department_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Filtrar por Departamento:
                    </label>
                    <select id="department_id" name="department_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        <option value="">Todos los departamentos (22)</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (string)request('department_id', $reportData['department_id']) === (string)$dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Botones Filtrar y Limpiar -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-amber-500 dark:hover:bg-amber-400 dark:text-slate-950 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        <span>Aplicar Filtros</span>
                    </button>
                    <a href="{{ route('reports.index', ['type' => $reportData['type']]) }}" class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold transition" title="Limpiar rango de fechas y departamento">
                        Limpiar
                    </a>
                </div>
            </div>

            <!-- PREAJUSTES RÁPIDOS DE FECHA -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center gap-2 text-[11px]">
                <span class="text-slate-400 font-medium">Preajustes rápidos:</span>
                <button type="button" onclick="setDateRange('2026-08-01', '2026-08-31')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/10 text-slate-700 dark:text-slate-300 font-mono transition">
                    Agosto 2026
                </button>
                <button type="button" onclick="setDateRange('2026-09-01', '2026-09-30')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/10 text-slate-700 dark:text-slate-300 font-mono transition">
                    Septiembre 2026 (Actual)
                </button>
                <button type="button" onclick="setDateRange('2026-01-01', '2026-12-31')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/10 text-slate-700 dark:text-slate-300 font-mono transition">
                    Año 2026 Completo
                </button>
                <button type="button" onclick="setDateRange('', '')" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/10 text-slate-700 dark:text-slate-300 font-mono transition">
                    Histórico Completo
                </button>
            </div>
        </form>
    </div>

    <!-- TARJETAS KPIS RESUMEN -->
    @if(!empty($reportData['summary_kpis']))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($reportData['summary_kpis'] as $kpi)
                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block font-mono">
                        {{ $kpi['label'] }}@if(stripos($kpi['label'], 'CO2') !== false || stripos($kpi['label'], 'CO₂') !== false)<x-co2-info />@endif
                    </span>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ $kpi['value'] }}
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">
                        {{ $kpi['desc'] }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- TABLA PRINCIPAL DE DATOS -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                    Registros Detallados ({{ count($reportData['rows']) }})
                </h3>
                <span class="text-xs text-slate-400">
                    Ámbito: {{ $reportData['department_name'] }} • Período: {{ $reportData['start_date'] ?: 'Histórico inicial' }} al {{ $reportData['end_date'] ?: 'Actual' }}
                </span>
            </div>

            <div class="flex items-center gap-2 text-xs font-mono text-slate-400">
                <span>Total filas: <strong>{{ count($reportData['rows']) }}</strong></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <x-table>
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5 text-center w-12">#</th>
                        @foreach($reportData['columns'] as $col)
                            <th class="px-5 py-3.5 {{ $col['align'] === 'right' ? 'text-right' : ($col['align'] === 'center' ? 'text-center' : 'text-left') }}">
                                {{ $col['label'] }}@if(str_starts_with($col['key'], 'co2'))<x-co2-info />@endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($reportData['rows'] as $idx => $row)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 text-center font-mono text-slate-400 text-[11px]">
                                {{ $idx + 1 }}
                            </td>
                            @foreach($reportData['columns'] as $col)
                                @php
                                    $val = $row[$col['key']] ?? '';
                                    $align = $col['align'] ?? 'left';
                                    $format = $col['format'] ?? 'string';
                                    $alignClass = $align === 'right' ? 'text-right' : ($align === 'center' ? 'text-center' : 'text-left');
                                @endphp
                                <td class="px-5 py-3.5 {{ $alignClass }} {{ $align === 'right' ? 'font-mono' : '' }}">
                                    @if($format === 'decimal' && is_numeric($val))
                                        <span class="font-semibold text-slate-900 dark:text-white">{{ number_format((float)$val, 2) }}</span>
                                    @elseif($format === 'integer' && is_numeric($val))
                                        <span class="text-slate-800 dark:text-slate-200">{{ number_format((int)$val) }}</span>
                                    @elseif($format === 'percentage' && is_numeric($val))
                                        <span class="font-bold {{ (float)$val < 80 ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400' }}">
                                            {{ number_format((float)$val, 1) }}%
                                        </span>
                                    @elseif($col['key'] === 'status')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ in_array(strtolower((string)$val), ['activa', 'operativo', 'resuelta']) ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-red-500/10 text-red-600 dark:text-red-400' }}">
                                            {{ $val }}
                                        </span>
                                    @else
                                        <span class="text-slate-800 dark:text-slate-200">{{ $val }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($reportData['columns']) + 1 }}" class="px-6 py-12 text-center text-slate-400 text-xs">
                                No se encontraron registros con los filtros seleccionados. Intenta ampliar el rango de fechas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(!empty($reportData['totals']) && count($reportData['rows']) > 0)
                    <tfoot class="bg-slate-100 dark:bg-slate-800/90 font-bold text-slate-900 dark:text-white border-t-2 border-slate-300 dark:border-slate-700 text-xs">
                        <tr>
                            <td class="px-5 py-4 text-center">-</td>
                            @foreach($reportData['columns'] as $col)
                                @php
                                    $val = $reportData['totals'][$col['key']] ?? '';
                                    $align = $col['align'] ?? 'left';
                                    $format = $col['format'] ?? 'string';
                                    $alignClass = $align === 'right' ? 'text-right' : ($align === 'center' ? 'text-center' : 'text-left');
                                @endphp
                                <td class="px-5 py-4 {{ $alignClass }} {{ $align === 'right' ? 'font-mono' : '' }}">
                                    @if($format === 'decimal' && is_numeric($val))
                                        {{ number_format((float)$val, 2) }}
                                    @elseif($format === 'integer' && is_numeric($val))
                                        {{ number_format((int)$val) }}
                                    @elseif($format === 'percentage' && is_numeric($val))
                                        {{ number_format((float)$val, 1) }}%
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </x-table>
        </div>
    </div>
</div>

<script>
    function setDateRange(start, end) {
        document.getElementById('start_date').value = start;
        document.getElementById('end_date').value = end;
    }
</script>
@endsection
