@extends('layouts.app', ['title' => 'Panorama nacional'])
@section('content')
<div>
    <div class="kin-dashboard-heading">
        <div><div class="kin-eyebrow">GUATEMALA · ENERGÍA RENOVABLE</div><h1>El sol de hoy.<br class="sm:hidden"> Un futuro más limpio.</h1><p>Una mirada a la generación solar y su impacto en nuestro país.</p></div>
        <div class="kin-heading-actions"><x-button :href="route('reports.index')" variant="outline" icon="arrow-up-right">Ver reportes</x-button><x-button :href="route('map.index')" icon="map">Explorar el mapa</x-button></div>
    </div>
    <div class="kin-summary-grid">
        <article class="kin-panel kin-metric kin-metric-hero">
            <div class="kin-metric-label"><i data-lucide="zap" class="text-amber-500"></i>Generación solar acumulada</div>
            <div class="kin-metric-value">{{ number_format(($stats['total_kwh'] ?? 0) / 1000, 1) }} <small>MWh</small></div>
            <div class="kin-metric-note"><span class="text-amber-600 dark:text-amber-400">{{ number_format($stats['total_kwh'] ?? 0) }} kWh</span> de energía limpia registrada</div>
        </article>
        <article class="kin-panel kin-metric kin-metric-hero is-eco">
            <div class="kin-metric-label"><i data-lucide="leaf" class="text-emerald-600 dark:text-emerald-400"></i>Emisiones de CO₂ evitadas</div>
            <div class="kin-metric-value">{{ number_format(($stats['total_co2_kg'] ?? 0) / 1000, 1) }} <small>toneladas</small></div>
            <div class="kin-metric-note"><span class="text-emerald-700 dark:text-emerald-400">{{ number_format($stats['total_co2_kg'] ?? 0) }} kg de CO₂</span> · Factor de 0.40 kg/kWh</div>
        </article>
    </div>
    <div class="kin-small-metrics">
        @foreach([
            ['Granjas solares',number_format($stats['total_farms'] ?? 0),'Infraestructura registrada','sun-medium'],
            ['Paneles instalados',number_format($stats['total_panels'] ?? 0),'Módulos fotovoltaicos','grid-2x2'],
            ['Capacidad instalada',number_format($stats['total_capacity_kw'] ?? 0,1).' kW','Potencia nominal total','battery-charging'],
            ['Familias beneficiadas',number_format($stats['total_families'] ?? 0),'El impacto que importa','users']
        ] as [$label,$value,$note,$icon])
            <article class="kin-panel kin-metric"><div class="kin-metric-label"><i data-lucide="{{ $icon }}"></i>{{ $label }}</div><div class="kin-metric-value">{{ $value }}</div><div class="kin-metric-note">{{ $note }}</div></article>
        @endforeach
    </div>
    <div class="kin-chart-grid">
        <section class="kin-panel">
            <div class="kin-panel-heading"><div><h3>El pulso de nuestra energía</h3><p>Generación real frente a estimada · kWh</p></div><span class="kin-eyebrow hidden sm:inline">HISTÓRICO</span></div>
            <div class="flex gap-5 px-6 mt-5 text-[10px]" style="color:var(--kin-muted)"><span class="flex items-center gap-2"><span class="w-2 h-2 bg-amber-500 rounded-full"></span>Real</span><span class="flex items-center gap-2"><span class="w-4 border-t border-dashed border-slate-400"></span>Estimada</span></div>
            @if(count($chartLabels ?? []))
                <div class="kin-chart"><canvas id="generationComparisonChart" role="img" aria-label="Comparación mensual de generación solar real y estimada"></canvas></div>
            @else
                <div class="p-10"><x-empty-state title="Tu historia energética empieza aquí" message="Las mediciones registradas aparecerán en este gráfico." icon="chart-no-axes-combined"/></div>
            @endif
        </section>
        <section class="kin-panel">
            <div class="kin-panel-heading"><div><h3>Departamentos que lideran</h3><p>Participación en la generación nacional</p></div><i data-lucide="award" class="w-5 h-5 text-amber-500"></i></div>
            <div class="kin-ranking">
                @forelse($topRanking ?? [] as $index => $dept)
                    <div class="kin-rank-item">
                        <div class="kin-rank-line"><strong><span class="kin-rank-number">{{ str_pad($index+1,2,'0',STR_PAD_LEFT) }}</span>{{ $dept['name'] }}</strong><span>{{ number_format($dept['kwh']/1000,1) }} MWh</span></div>
                        <div class="kin-progress"><span style="width:{{ min(100,max(0,$dept['share'])) }}%"></span></div>
                        <div class="kin-rank-meta"><span>{{ $dept['co2'] }} CO₂ evitado</span><span>{{ $dept['share'] }}%</span></div>
                    </div>
                @empty
                    <p class="text-sm py-10" style="color:var(--kin-muted)">Sin generación registrada todavía.</p>
                @endforelse
                <a class="kin-text-link" href="{{ route('reports.index') }}">Explorar los 22 departamentos <i data-lucide="arrow-right"></i></a>
            </div>
        </section>
    </div>
    <section class="kin-panel mt-6">
        <div class="kin-panel-heading"><div><h3 class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Atención y seguimiento</h3><p>Últimas alertas activas por déficit de generación ≥ 20 %</p></div><a href="{{ route('alerts.index') }}" class="kin-text-link">Ver todas <i data-lucide="arrow-up-right"></i></a></div>
        <div class="p-4 sm:p-6">
            @if(isset($activeAlerts) && $activeAlerts->isNotEmpty())
                <x-table>
                    <thead><tr><th>Granja solar</th><th>Período</th><th>Esperada</th><th>Real</th><th>Déficit</th><th>Seguimiento</th></tr></thead>
                    <tbody>
                    @foreach($activeAlerts as $alert)
                        <tr><td><strong>{{ $alert->solarFarm?->name ?? 'Granja no disponible' }}</strong><span class="block text-[10px] mt-1" style="color:var(--kin-muted)">{{ $alert->solarFarm?->department?->name }}</span></td><td>{{ $alert->period }}</td><td>{{ number_format($alert->estimated_kwh,1) }} kWh</td><td>{{ number_format($alert->real_kwh,1) }} kWh</td><td><x-badge variant="danger">−{{ $alert->deviation_percentage }}%</x-badge></td><td><a class="kin-text-link" href="{{ route('alerts.show',$alert) }}">Ver detalles <i data-lucide="arrow-up-right"></i></a></td></tr>
                    @endforeach
                    </tbody>
                </x-table>
            @else
                <div class="flex items-center gap-4 p-5 rounded-xl bg-emerald-500/5 border border-emerald-500/15"><i data-lucide="circle-check" class="w-7 h-7 text-emerald-600 dark:text-emerald-400"></i><div><p class="text-sm font-semibold">Sin alertas activas</p><p class="text-xs mt-1" style="color:var(--kin-muted)">No hay desviaciones pendientes de seguimiento.</p></div></div>
            @endif
        </div>
    </section>
</div>
@endsection
@push('scripts')
<script>
    const chartCanvas = document.getElementById('generationComparisonChart');
    if (chartCanvas && window.Chart) {
        const ctx = chartCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0,0,0,280);
        gradient.addColorStop(0,'rgba(245,158,11,.22)');
        gradient.addColorStop(1,'rgba(245,158,11,0)');
        const energyChart = new Chart(ctx, {
            type:'line',
            data:{labels:@json($chartLabels ?? []),datasets:[
                {label:'Real (kWh)',data:@json($chartReal ?? []),borderColor:'#f59e0b',backgroundColor:gradient,fill:true,tension:.35,borderWidth:2.5,pointRadius:3,pointHoverRadius:6,pointBackgroundColor:'#f59e0b'},
                {label:'Estimada (kWh)',data:@json($chartExpected ?? []),borderColor:'#94a3b8',borderDash:[5,5],tension:.35,borderWidth:1.5,pointRadius:0}
            ]},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: matchMedia('(prefers-reduced-motion: reduce)').matches ? false : {duration:650},
                interaction: {intersect:false, mode:'index'},
                plugins: {legend:{display:false}, tooltip:{backgroundColor:'#172033',padding:12,cornerRadius:9}},
                scales: {
                    x: {grid:{display:false},border:{display:false},ticks:{color:'#8493a7',font:{size:10}}},
                    y: {beginAtZero:true,border:{display:false},grid:{color:'rgba(148,163,184,.10)'},ticks:{color:'#8493a7',font:{size:10},callback:value=>new Intl.NumberFormat('es-GT',{notation:'compact'}).format(value)}}
                }
            }
        });
    }
</script>
@endpush
