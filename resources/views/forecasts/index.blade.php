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
                    <x-input name="target_period" label="Período objetivo" type="month" :value="now()->startOfMonth()->addMonth()->format('Y-m')" :error="$errors->first('target_period')" required/>
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
    @if(isset($forecasts) && $forecasts->count())
        <section class="kin-panel p-5"><h2 class="text-base font-semibold mb-4">Proyecciones registradas</h2><x-table><thead><tr><th>Granja</th><th>Período</th><th>Proyección</th><th>Generación real</th></tr></thead><tbody>@foreach($forecasts as $forecast)<tr><td>{{ $forecast->solarFarm?->name }}</td><td>{{ $forecast->target_period }}</td><td>{{ number_format($forecast->forecasted_kwh,2) }} kWh</td><td>{{ $forecast->actual_kwh !== null ? number_format($forecast->actual_kwh,2).' kWh' : 'Pendiente' }}</td></tr>@endforeach</tbody></x-table>{{ $forecasts->links() }}</section>
    @endif
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
