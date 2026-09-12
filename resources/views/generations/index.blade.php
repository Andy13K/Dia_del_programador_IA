@extends('layouts.app', ['title' => 'Mediciones de Generación'])

@section('content')
<div class="space-y-6">

    <!-- Encabezado y Acciones -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold text-xs">
                    Requerimientos RF-08 a RF-10
                </span>
                <span class="text-xs text-slate-400">Factor Certificado: {{ number_format((float) config('solar.co2_kg_per_kwh'), 2) }} kg CO₂ / kWh<x-co2-info /></span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Registro de Generación Eléctrica Mensual
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Seguimiento de producción real versus estimada y balance de descarbonización nacional.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @can('manage-generations')
                <x-button href="{{ route('generations.create') }}" variant="success" size="md">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                    <span>Registrar Medición</span>
                </x-button>
            @endcan
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('generations.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Granja Solar</label>
                <select name="solar_farm_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500">
                    <option value="">Todas las granjas solares</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" {{ ($filters['solar_farm_id'] ?? '') == $farm->id ? 'selected' : '' }}>
                            {{ $farm->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Período (YYYY-MM)</label>
                <input type="text" name="period" value="{{ $filters['period'] ?? '' }}" placeholder="Ej. 2026-08" 
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 font-mono">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-xs font-bold rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 transition">
                    Filtrar
                </button>
                <a href="{{ route('generations.index') }}" class="px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Mediciones -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                <tr>
                    <th class="px-6 py-4">Granja Solar</th>
                    <th class="px-6 py-4">Período</th>
                    <th class="px-6 py-4">Fecha Registro</th>
                    <th class="px-6 py-4 text-right">Esperada (kWh)</th>
                    <th class="px-6 py-4 text-right">Real (kWh)</th>
                    <th class="px-6 py-4 text-right">CO₂ Evitado<x-co2-info /></th>
                    <th class="px-6 py-4 text-center">Desviación</th>
                    <th class="px-6 py-4 text-center">Estado Alerta</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @forelse($generations as $gen)
                    @php
                        $diff = (float)$gen->real_kwh - (float)$gen->estimated_kwh;
                        $pct = (float)$gen->estimated_kwh > 0 ? ($diff / (float)$gen->estimated_kwh) * 100 : 0;
                        $co2Ton = (float)$gen->co2_kg / 1000;
                    @endphp
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                            <a href="{{ route('farms.show', $gen->solar_farm_id) }}" class="hover:text-amber-600 dark:hover:text-amber-400">
                                {{ $gen->solarFarm->name ?? 'Granja #'.$gen->solar_farm_id }}
                            </a>
                        </td>
                        <td class="px-6 py-4 font-mono font-bold text-slate-700 dark:text-slate-300">
                            {{ $gen->period }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $gen->record_date }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-slate-500">
                            {{ number_format((float)$gen->estimated_kwh, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-extrabold {{ $pct <= -20 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">
                            {{ number_format((float)$gen->real_kwh, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                            {{ number_format($co2Ton, 2) }} Ton
                            <span class="block text-[10px] text-slate-400 font-normal">({{ number_format((float)$gen->co2_kg, 1) }} kg)</span>
                        </td>
                        <td class="px-6 py-4 text-center font-mono font-bold">
                            @if($pct <= -20)
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 text-xs">
                                    {{ number_format($pct, 1) }}%
                                </span>
                            @elseif($pct < 0)
                                <span class="text-amber-600 dark:text-amber-400">{{ number_format($pct, 1) }}%</span>
                            @else
                                <span class="text-emerald-600 dark:text-emerald-400">+{{ number_format($pct, 1) }}%</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($gen->generationAlert)
                                <a href="{{ route('alerts.index') }}">
                                    <x-badge variant="danger">
                                        <i data-lucide="alert-triangle" class="w-3 h-3 mr-0.5"></i>
                                        <span>Déficit {{ $gen->generationAlert->status === 'active' ? 'Activo' : 'Resuelto' }}</span>
                                    </x-badge>
                                </a>
                            @else
                                <span class="text-slate-400 text-[11px]">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('generations.show', $gen) }}" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white transition inline-block" title="Ver detalle">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i data-lucide="activity" class="w-10 h-10 text-slate-300"></i>
                                <p class="text-sm font-medium">No se encontraron mediciones con los filtros indicados.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        @if($generations->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $generations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
