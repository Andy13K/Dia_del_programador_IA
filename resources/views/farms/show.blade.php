@extends('layouts.app', ['title' => $farm->name])

@section('content')
<div class="space-y-6">

    <!-- Encabezado con estado y acciones rápidas -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center flex-shrink-0">
                <i data-lucide="sun" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        {{ $farm->department->name ?? 'Guatemala' }}
                    </span>
                    @if($farm->status === 'active')
                        <x-badge variant="success">Activa</x-badge>
                    @elseif($farm->status === 'maintenance')
                        <x-badge variant="warning">Mantenimiento</x-badge>
                    @else
                        <x-badge variant="neutral">Inactiva</x-badge>
                    @endif
                </div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                    {{ $farm->name }}
                </h2>
                <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 mt-1">
                    <span class="flex items-center gap-1 font-mono">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-500"></i>
                        {{ number_format((float)$farm->latitude, 6) }}, {{ number_format((float)$farm->longitude, 6) }}
                    </span>
                    <a href="{{ route('map.index', ['farm' => $farm->id]) }}" class="text-amber-600 dark:text-amber-400 font-bold hover:underline flex items-center gap-1">
                        Ver en Mapa &rarr;
                    </a>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('farms.index') }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                &larr; Volver
            </a>
            <a href="{{ route('map.index', ['farm' => $farm->id]) }}" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 transition flex items-center gap-1.5 shadow-sm active:scale-95">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>Ver en Mapa</span>
            </a>
            @can('manage-generations')
                <x-button href="{{ route('generations.create', ['farm_id' => $farm->id]) }}" variant="success" size="md">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                    <span>Nueva Medición</span>
                </x-button>
            @endcan
            @can('manage-farms')
                <x-button href="{{ route('farms.edit', $farm) }}" variant="primary" size="md">
                    <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i>
                    <span>Editar Granja</span>
                </x-button>
            @endcan
        </div>
    </div>

    <!-- 4 Tarjetas Métricas Rápidas (Simétricas 2 cols en móvil) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <x-kpi-card
            title="Potencia Instalada"
            value="{{ number_format((float)$farm->calculated_capacity_kw, 1) }} kW"
            subtitle="Capacidad calculada"
            icon="zap"
            variant="amber"
        />
        <x-kpi-card
            title="Paneles en Operación"
            value="{{ number_format($farm->solarPanels->sum('pivot.quantity')) }}"
            subtitle="Módulos asignados"
            icon="grid"
            variant="sky"
        />
        <x-kpi-card
            title="Familias Beneficiadas"
            value="{{ number_format($farm->benefited_families) }}"
            subtitle="Hogares guatemaltecos"
            icon="users"
            variant="indigo"
        />
        <x-kpi-card
            title="Generación Acumulada"
            value="{{ number_format($farm->energyGenerations->sum('real_kwh') / 1000, 1) }} MWh"
            subtitle="{{ number_format($farm->energyGenerations->sum('co2_kg') / 1000, 2) }} Ton CO₂ evitadas"
            icon="leaf"
            variant="eco"
        />
    </div>

    <section class="kin-panel overflow-hidden">
        <div class="kin-panel-heading pb-5"><div><h3>Ubicación y territorio</h3><p>{{ $farm->department?->name }} · {{ number_format((float)$farm->latitude,4) }}, {{ number_format((float)$farm->longitude,4) }}</p></div><a class="kin-text-link" href="{{ route('map.index', ['farm' => $farm->id]) }}">Abrir mapa <i data-lucide="arrow-up-right"></i></a></div>
        <div id="farmLocationMap" class="h-64 sm:h-72 relative z-0" aria-label="Ubicación de la granja"></div>
    </section>

    <!-- Pestañas / Tablas de Paneles y Mediciones -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna Izquierda: Paneles Asignados (1 col) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="grid" class="w-4 h-4 text-sky-500"></i>
                    <span>Paneles Fotovoltaicos</span>
                </h3>
                <span class="text-xs text-slate-400">{{ count($farm->solarPanels) }} tipos</span>
            </div>

            <div class="space-y-3">
                @forelse($farm->solarPanels as $panel)
                    @php
                        $qty = (int) $panel->pivot->quantity;
                        $pKw = (float) $panel->nominal_power_kw;
                        $totalKw = $qty * $pKw;
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs font-extrabold text-slate-900 dark:text-white">{{ $panel->brand }}</span>
                                <span class="block text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $panel->model }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-lg bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold text-xs">
                                {{ number_format($pKw * 1000) }} W
                            </span>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs">
                            <span class="text-slate-500">Cantidad: <strong class="text-slate-900 dark:text-white font-bold">{{ number_format($qty) }}</strong></span>
                            <span class="font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($totalKw, 1) }} kW</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs">
                        No hay paneles asignados a esta granja solar.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Columna Derecha: Histórico de Mediciones (2 cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="activity" class="w-4 h-4 text-emerald-500"></i>
                        <span>Historial de Mediciones Mensuales</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Emisiones calculadas automáticamente a 0.40 kg CO₂ por kWh generado.</p>
                </div>
                @can('manage-generations')
                    <a href="{{ route('generations.create', ['farm_id' => $farm->id]) }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 flex items-center gap-1">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Añadir</span>
                    </a>
                @endcan
            </div>

            <x-table>
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Período</th>
                        <th class="px-4 py-3 text-right">Esperada (kWh)</th>
                        <th class="px-4 py-3 text-right">Real (kWh)</th>
                        <th class="px-4 py-3 text-right">CO₂ Evitado (Ton)</th>
                        <th class="px-4 py-3 text-center">Desviación</th>
                        <th class="px-4 py-3 text-center">Alerta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($farm->energyGenerations as $gen)
                        @php
                            $diff = (float)$gen->real_kwh - (float)$gen->estimated_kwh;
                            $pct = (float)$gen->estimated_kwh > 0 ? ($diff / (float)$gen->estimated_kwh) * 100 : 0;
                            $co2Ton = (float)$gen->co2_kg / 1000;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900 dark:text-white">
                                {{ $gen->period }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-slate-500">
                                {{ number_format((float)$gen->estimated_kwh, 1) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-extrabold {{ $pct <= -20 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }}">
                                {{ number_format((float)$gen->real_kwh, 1) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($co2Ton, 2) }} Ton
                            </td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-xs">
                                @if($pct <= -20)
                                    <span class="text-rose-600 dark:text-rose-400">{{ number_format($pct, 1) }}%</span>
                                @elseif($pct < 0)
                                    <span class="text-amber-600 dark:text-amber-400">{{ number_format($pct, 1) }}%</span>
                                @else
                                    <span class="text-emerald-600 dark:text-emerald-400">+{{ number_format($pct, 1) }}%</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($gen->generationAlert)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold text-[10px] border border-rose-200 dark:border-rose-800">
                                        Déficit {{ $gen->generationAlert->status === 'active' ? 'Activa' : 'Resuelta' }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px]">Normal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-xs">
                                No se han registrado mediciones mensuales para esta granja solar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>

    </div>

</div>
@endsection
@push('scripts')
<script>
    const farmCoordinates = [Number(@json($farm->latitude)), Number(@json($farm->longitude))];
    const farmMap = L.map('farmLocationMap', {scrollWheelZoom:false}).setView(farmCoordinates,10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'&copy; OpenStreetMap',maxZoom:19}).addTo(farmMap);
    L.circleMarker(farmCoordinates, {radius:10,color:'#b45309',weight:3,fillColor:'#fbbf24',fillOpacity:1}).addTo(farmMap);
</script>
@endpush
