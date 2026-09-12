@extends('layouts.app', ['title' => 'Reportes Comparativos por Departamento'])

@section('content')
<div class="space-y-6">

    <!-- Header de Reportes -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                    IMPACTO TERRITORIAL
                </span>
                <span class="text-xs text-slate-400">Consolidado Nacional</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                La energía, departamento a departamento
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Desglose oficial por departamento: infraestructura, potencia instalada, energía generada y balance de emisiones.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2 transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Imprimir / PDF</span>
            </button>
            <a href="{{ route('reports.export') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold flex items-center gap-2 transition shadow-sm">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Exportar CSV</span>
            </a>
        </div>
    </div>

    <!-- Tabla Comparativa Completa de los 22 Departamentos -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                <tr>
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Departamento</th>
                    <th class="px-6 py-4 text-center">Granjas</th>
                    <th class="px-6 py-4 text-right">Paneles</th>
                    <th class="px-6 py-4 text-right">Capacidad (kW)</th>
                    <th class="px-6 py-4 text-right">Generación (kWh)</th>
                    <th class="px-6 py-4 text-right">Familias</th>
                    <th class="px-6 py-4 text-right">CO₂ Evitado (Ton)</th>
                    <th class="px-6 py-4 text-right">CO₂ Evitado (kg)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @php
                    $matrix = $deptStats ?? [];

                    $totalFarms = 0;
                    $totalPanels = 0;
                    $totalKw = 0;
                    $totalKwh = 0;
                    $totalFam = 0;
                    $totalCo2Kg = 0;
                    $totalCo2Ton = 0;
                @endphp

                @foreach($matrix as $i => $d)
                    @php
                        $co2_kg = $d['co2_kg'] ?? ($d['kwh'] * 0.40);
                        $co2_ton = $d['co2_ton'] ?? ($co2_kg / 1000);
                        $totalFarms += $d['farms'];
                        $totalPanels += $d['panels'];
                        $totalKw += $d['kw'];
                        $totalKwh += $d['kwh'];
                        $totalFam += $d['families'];
                        $totalCo2Kg += $co2_kg;
                        $totalCo2Ton += $co2_ton;
                    @endphp
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-6 py-3.5 text-slate-400 font-mono text-[11px]">{{ $i + 1 }}</td>
                        <td class="px-6 py-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>{{ $d['name'] }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-center font-semibold text-slate-700 dark:text-slate-300">
                            {{ $d['farms'] }}
                        </td>
                        <td class="px-6 py-3.5 text-right font-mono text-slate-600 dark:text-slate-400">
                            {{ number_format($d['panels']) }}
                        </td>
                        <td class="px-6 py-3.5 text-right font-bold text-amber-600 dark:text-amber-400 font-mono">
                            {{ number_format((float)$d['kw'], 2) }} kW
                        </td>
                        <td class="px-6 py-3.5 text-right font-extrabold text-slate-900 dark:text-white font-mono">
                            {{ number_format((float)$d['kwh']) }}
                        </td>
                        <td class="px-6 py-3.5 text-right font-medium text-slate-600 dark:text-slate-300 font-mono">
                            {{ number_format((int)$d['families']) }}
                        </td>
                        <td class="px-6 py-3.5 text-right font-extrabold text-emerald-600 dark:text-emerald-400 font-mono">
                            {{ number_format((float)$co2_ton, 2) }} Ton
                        </td>
                        <td class="px-6 py-3.5 text-right font-mono text-slate-500">
                            {{ number_format((float)$co2_kg, 2) }} kg
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-100 dark:bg-slate-800 font-bold text-slate-900 dark:text-white border-t-2 border-slate-300 dark:border-slate-700 text-xs">
                <tr>
                    <td class="px-6 py-4" colspan="2">TOTAL NACIONAL (22 Departamentos)</td>
                    <td class="px-6 py-4 text-center">{{ $totalFarms }}</td>
                    <td class="px-6 py-4 text-right font-mono">{{ number_format($totalPanels) }}</td>
                    <td class="px-6 py-4 text-right font-mono text-amber-600 dark:text-amber-400">{{ number_format((float)$totalKw, 2) }} kW</td>
                    <td class="px-6 py-4 text-right font-mono">{{ number_format((float)$totalKwh) }} kWh</td>
                    <td class="px-6 py-4 text-right font-mono">{{ number_format((int)$totalFam) }}</td>
                    <td class="px-6 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400">{{ number_format((float)$totalCo2Ton, 2) }} Ton</td>
                    <td class="px-6 py-4 text-right font-mono text-slate-500">{{ number_format((float)$totalCo2Kg, 2) }} kg</td>
                </tr>
            </tfoot>
        </x-table>
    </div>
</div>
@endsection
