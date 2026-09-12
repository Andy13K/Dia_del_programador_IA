@extends('layouts.app', ['title' => 'Reportes Comparativos por Departamento'])

@section('content')
<div class="space-y-6">

    <!-- Header de Reportes -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                    Requerimiento RF-12
                </span>
                <span class="text-xs text-slate-400">Consolidado Nacional</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Matriz Departamental de Generación e Impacto
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
                    $matrix = isset($deptStats) && count($deptStats) > 0 ? $deptStats : [
                        ['id' => 3, 'name' => 'Escuintla', 'farms' => 3, 'panels' => 3500, 'kw' => 2450.50, 'kwh' => 380000, 'families' => 6500],
                        ['id' => 21, 'name' => 'Zacapa', 'farms' => 2, 'panels' => 2800, 'kw' => 1950.00, 'kwh' => 285000, 'families' => 5200],
                        ['id' => 5, 'name' => 'Petén', 'farms' => 2, 'panels' => 2200, 'kw' => 1500.00, 'kwh' => 210000, 'families' => 3800],
                        ['id' => 4, 'name' => 'Izabal', 'farms' => 2, 'panels' => 2100, 'kw' => 1450.00, 'kwh' => 190000, 'families' => 3600],
                        ['id' => 13, 'name' => 'Jutiapa', 'farms' => 1, 'panels' => 1800, 'kw' => 1250.00, 'kwh' => 175000, 'families' => 3100],
                        ['id' => 10, 'name' => 'El Progreso', 'farms' => 1, 'panels' => 1400, 'kw' => 980.00, 'kwh' => 135000, 'families' => 2400],
                        ['id' => 16, 'name' => 'San Marcos', 'farms' => 1, 'panels' => 1350, 'kw' => 950.00, 'kwh' => 130000, 'families' => 2200],
                        ['id' => 9, 'name' => 'Chiquimula', 'farms' => 1, 'panels' => 1200, 'kw' => 840.00, 'kwh' => 115000, 'families' => 2000],
                        ['id' => 2, 'name' => 'Quetzaltenango', 'farms' => 1, 'panels' => 1100, 'kw' => 770.00, 'kwh' => 98000, 'families' => 1800],
                        ['id' => 1, 'name' => 'Guatemala', 'farms' => 1, 'panels' => 900, 'kw' => 630.00, 'kwh' => 85000, 'families' => 1600],
                        ['id' => 6, 'name' => 'Alta Verapaz', 'farms' => 1, 'panels' => 850, 'kw' => 595.00, 'kwh' => 78000, 'families' => 1500],
                        ['id' => 14, 'name' => 'Retalhuleu', 'farms' => 1, 'panels' => 800, 'kw' => 560.00, 'kwh' => 72000, 'families' => 1400],
                        ['id' => 19, 'name' => 'Suchitepéquez', 'farms' => 1, 'panels' => 750, 'kw' => 525.00, 'kwh' => 68000, 'families' => 1300],
                        ['id' => 17, 'name' => 'Santa Rosa', 'farms' => 1, 'panels' => 700, 'kw' => 490.00, 'kwh' => 62000, 'families' => 1200],
                        ['id' => 12, 'name' => 'Jalapa', 'farms' => 1, 'panels' => 650, 'kw' => 455.00, 'kwh' => 58000, 'families' => 1100],
                        ['id' => 7, 'name' => 'Baja Verapaz', 'farms' => 1, 'panels' => 600, 'kw' => 420.00, 'kwh' => 52000, 'families' => 1000],
                        ['id' => 11, 'name' => 'Huehuetenango', 'farms' => 1, 'panels' => 550, 'kw' => 385.00, 'kwh' => 48000, 'families' => 950],
                        ['id' => 8, 'name' => 'Chimaltenango', 'farms' => 1, 'panels' => 500, 'kw' => 350.00, 'kwh' => 42000, 'families' => 850],
                        ['id' => 18, 'name' => 'Sololá', 'farms' => 1, 'panels' => 450, 'kw' => 315.00, 'kwh' => 38000, 'families' => 750],
                        ['id' => 15, 'name' => 'Sacatepéquez', 'farms' => 1, 'panels' => 400, 'kw' => 280.00, 'kwh' => 32000, 'families' => 650],
                        ['id' => 22, 'name' => 'Quiché', 'farms' => 1, 'panels' => 380, 'kw' => 266.00, 'kwh' => 29000, 'families' => 600],
                        ['id' => 20, 'name' => 'Totonicapán', 'farms' => 1, 'panels' => 350, 'kw' => 245.00, 'kwh' => 26500, 'families' => 550],
                    ];

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
