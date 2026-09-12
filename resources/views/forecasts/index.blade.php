@extends('layouts.app', ['title' => 'Proyecciones estacionales'])
@section('content')
<div class="space-y-6">
    <div class="kin-dashboard-heading"><div><div class="kin-eyebrow">INTELIGENCIA SOLAR · SMA-SF</div><h1>Anticipa la energía del mañana.</h1><p>Una proyección explicable, basada en tu historial y en la estación del año.</p></div></div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <section class="kin-panel p-6 lg:col-span-2">
            <div class="flex items-center gap-3 mb-5"><span class="p-3 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"><i data-lucide="sparkles" class="w-5 h-5"></i></span><div><h2 class="text-base font-semibold">Un modelo adaptado a Guatemala</h2><p class="text-xs mt-1" style="color:var(--kin-muted)">Promedio móvil ponderado con factor estacional</p></div></div>
            <p class="text-sm leading-relaxed" style="color:var(--kin-muted)">Las tres mediciones anteriores al período objetivo aportan el punto de partida. La más reciente tiene mayor peso. El factor de la estación ajusta la estimación final.</p>
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-5"><i data-lucide="sun" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i><div class="text-xs mt-4" style="color:var(--kin-muted)">Época seca · nov–abr</div><div class="text-3xl font-semibold tracking-tight mt-2">1.20<span class="text-sm ml-1" style="color:var(--kin-muted)">×</span></div></div>
                <div class="rounded-xl border border-indigo-500/20 bg-indigo-500/5 p-5"><i data-lucide="cloud-rain" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i><div class="text-xs mt-4" style="color:var(--kin-muted)">Época lluviosa · may–oct</div><div class="text-3xl font-semibold tracking-tight mt-2">0.88<span class="text-sm ml-1" style="color:var(--kin-muted)">×</span></div></div>
            </div>
        </section>
        <section class="kin-panel p-6">
            <h2 class="text-base font-semibold">Generar proyección</h2><p class="text-xs leading-relaxed mt-2" style="color:var(--kin-muted)">Se aplica a las granjas que puedes gestionar. Cada una necesita tres mediciones previas.</p>
            @can('manage-forecasts')
                <form method="POST" action="{{ route('forecasts.generate') }}" class="mt-6">
                    @csrf
                    <x-input name="target_period" label="Período objetivo" type="month" :value="$defaultPeriod" :error="$errors->first('target_period')" hint="La proyección se calcula para el mes que elijas aquí y aparece resaltada en la tabla de abajo." required/>
                    @error('solar_farm_id')<p role="alert" class="text-xs text-rose-600 mt-3">{{ $message }}</p>@enderror
                    <x-button type="submit" class="w-full mt-5" icon="sparkles">Calcular proyecciones</x-button>
                </form>
            @else
                <p class="text-sm mt-6" style="color:var(--kin-muted)">La generación está disponible para administradores y operadores.</p>
            @endcan
        </section>
    </div>
    <section class="kin-panel p-6">
        <div class="flex justify-between items-center gap-3"><div><h2 class="text-base font-semibold">El ciclo solar, mes a mes</h2><p class="text-xs mt-2" style="color:var(--kin-muted)">Factores del modelo, no mediciones ni resultados de una granja.</p></div><span class="kin-eyebrow hidden sm:inline">ESTACIONALIDAD</span></div>
        <div class="relative h-64 mt-6"><canvas id="forecastChart" role="img" aria-label="Factor 1.20 de noviembre a abril y 0.88 de mayo a octubre"></canvas></div>
        <div class="mt-5 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-xs leading-relaxed" style="color:var(--kin-muted)">Las proyecciones son estimaciones, no garantías de producción. Su precisión se evalúa al compararlas con mediciones reales del mismo período.</div>
    </section>
    <section class="kin-panel p-5 sm:p-6" id="proyecciones-registradas">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-4">
            <div>
                <h2 class="text-base font-semibold">Proyecciones registradas</h2>
                @if($summary)
                    <p class="text-xs mt-1" style="color:var(--kin-muted)">
                        <strong class="text-slate-900 dark:text-white">{{ ucfirst($summary['label']) }}</strong>
                        · {{ $summary['count'] }} {{ $summary['count'] === 1 ? 'granja' : 'granjas' }}
                        · época {{ $summary['season'] }} (factor {{ number_format($summary['factor'], 2) }}×)
                        · total proyectado <strong class="text-amber-600 dark:text-amber-400">{{ number_format($summary['total_kwh'], 2) }} kWh</strong>
                    </p>
                @else
                    <p class="text-xs mt-1" style="color:var(--kin-muted)">Comparación directa entre el modelo predictivo SMA-SF y las mediciones reales del período (§8). Todos los períodos, del más reciente al más antiguo.</p>
                @endif
                <div class="flex flex-wrap items-center gap-3 text-[11px] mt-2" style="color:var(--kin-muted)">
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Precisión óptima (&le;5%)</span>
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Aceptable (&le;15%)</span>
                    <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Desvío (&gt;15%)</span>
                </div>
            </div>
            @if(count($periodOptions))
                <form method="GET" action="{{ route('forecasts.index') }}#proyecciones-registradas" class="flex items-end gap-2">
                    <div class="min-w-[200px]">
                        <label for="period" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Ver período</label>
                        <select name="period" id="period" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm border-slate-300 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="all" @selected($selectedPeriod === null)>Todos los períodos</option>
                            @foreach($periodOptions as $value => $label)
                                <option value="{{ $value }}" @selected($selectedPeriod === $value)>{{ ucfirst($label) }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            @endif
        </div>

        @if($forecasts->count())
            <x-table>
                <thead>
                    <tr>
                        <th>Granja</th>
                        <th>Período</th>
                        <th class="text-right">Proyección</th>
                        <th class="text-right">Generación real</th>
                        <th class="text-right">Desviación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forecasts as $forecast)
                        @php
                            $isNew = $justGenerated !== null && $forecast->target_period === $justGenerated;
                            $hasActual = $forecast->actual_kwh !== null && (float)$forecast->forecasted_kwh > 0;
                            $badgeClass = '';
                            $devFormatted = '—';
                            if ($hasActual) {
                                $dev = (((float)$forecast->actual_kwh - (float)$forecast->forecasted_kwh) / (float)$forecast->forecasted_kwh) * 100;
                                $absDev = abs($dev);
                                $sign = $dev > 0 ? '+' : '';
                                $devFormatted = $sign . number_format($dev, 1) . '%';
                                if ($absDev <= 5.0) {
                                    $badgeClass = 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
                                } elseif ($absDev <= 15.0) {
                                    $badgeClass = 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
                                } else {
                                    $badgeClass = 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
                                }
                            }
                        @endphp
                        <tr @class(['bg-amber-500/10' => $isNew])>
                            <td class="font-medium text-slate-900 dark:text-slate-100">
                                {{ $forecast->solarFarm?->name }}
                                @if($isNew)<span class="ml-2 text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-300">nueva</span>@endif
                            </td>
                            <td><span class="font-mono text-xs">{{ $forecast->target_period }}</span></td>
                            <td class="text-right font-mono">{{ number_format((float)$forecast->forecasted_kwh, 2) }} kWh</td>
                            <td class="text-right font-mono">
                                @if($forecast->actual_kwh !== null)
                                    <span class="font-semibold text-slate-900 dark:text-slate-100">{{ number_format((float)$forecast->actual_kwh, 2) }} kWh</span>
                                @else
                                    <span class="text-xs italic text-slate-400 dark:text-slate-500">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-right font-mono">
                                @if($hasActual)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ $devFormatted }}
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 font-mono text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
            <div class="mt-4">
                {{ $forecasts->links() }}
            </div>
        @else
            <p class="text-sm py-6 text-center" style="color:var(--kin-muted)">Todavía no hay proyecciones registradas{{ $selectedPeriod ? ' para este período' : '' }}.</p>
        @endif
    </section>
</div>
@endsection
@push('scripts')
<script>
    new Chart(document.getElementById('forecastChart'), {
        type:'bar',
        data:{labels:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],datasets:[{label:'Factor estacional',data:[1.2,1.2,1.2,1.2,.88,.88,.88,.88,.88,.88,1.2,1.2],backgroundColor:['#f59e0b','#f59e0b','#f59e0b','#f59e0b','#818cf8','#818cf8','#818cf8','#818cf8','#818cf8','#818cf8','#f59e0b','#f59e0b'],borderRadius:5,maxBarThickness:34}]},
        options:{responsive:true,maintainAspectRatio:false,animation:matchMedia('(prefers-reduced-motion: reduce)').matches?false:{duration:500},plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:'#8493a7',font:{size:10}}},y:{beginAtZero:true,max:1.4,grid:{color:'#94a3b815'},ticks:{color:'#8493a7',font:{size:10}}}}}
    });
</script>
@endpush
