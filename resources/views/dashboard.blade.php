@extends('layouts.app', ['title' => 'Panorama nacional'])
@section('content')
<div>
    <div class="kin-dashboard-heading">
        <div><div class="kin-eyebrow">GUATEMALA · ENERGÍA RENOVABLE</div><h1>El sol de hoy.<br class="sm:hidden"> Un futuro más limpio.</h1><p>Una mirada a la generación solar y su impacto en nuestro país.</p></div>
        <div class="kin-heading-actions"><x-button :href="route('reports.index')" variant="outline" icon="arrow-up-right">Ver reportes</x-button><x-button :href="route('map.index')" icon="map">Explorar el mapa</x-button></div>
    </div>

    @can('manage-generations')
    {{-- Panel de Simulación de Telemetría SCADA en Tiempo Real (RF-14 & IoT) --}}
    <div class="kin-panel mb-6 p-5 sm:p-6 border border-amber-500/30 bg-gradient-to-r from-amber-500/5 via-transparent to-emerald-500/5 relative overflow-hidden transition-all duration-300 shadow-sm" id="scadaSimulatorPanel">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span class="kin-eyebrow text-amber-600 dark:text-amber-400 font-bold">TELEMETRÍA SCADA · IoT EN TIEMPO REAL (RF-14)</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white mt-1">Inyector de Mediciones Fotovoltaicas en Vivo</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Seleccione una granja y un escenario operativo para inyectar telemetría instantánea. Los indicadores, la gráfica histórica y las alertas se actualizarán en vivo sin recargar la página.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="text-[11px] px-3 py-1 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20 font-medium">
                    ⚡ Período en curso: {{ now()->format('Y-m') }}
                </span>
            </div>
        </div>

        <form id="telemetrySimulationForm" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            @csrf
            <div class="lg:col-span-5">
                <label for="simSolarFarmId" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Granja solar destino:
                </label>
                <select id="simSolarFarmId" name="solar_farm_id" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}">
                            {{ $farm->name }} ({{ $farm->department?->name ?? 'N/D' }}) · {{ number_format($farm->calculated_capacity_kw, 1) }} kW
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-4">
                <label for="simScenario" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Escenario operativo:
                </label>
                <select id="simScenario" name="scenario" required class="w-full text-xs sm:text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2.5 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    <option value="optimal">🟢 Óptimo (100% potencia nominal · Cielo despejado)</option>
                    <option value="degraded">🟡 Leve degradación (-10% · Nubosidad parcial)</option>
                    <option value="critical_alert">🔴 Falla de Inversores (-26% déficit · Dispara Alerta RF-14)</option>
                    <option value="severe_storm">⚠️ Tormenta Severa (-48% déficit · Dispara Alerta RF-14)</option>
                </select>
            </div>

            <div class="lg:col-span-3">
                <button type="submit" id="btnSimulateSubmit" class="w-full inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-200 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-0.5 active:scale-[0.98] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none shadow-sm cursor-pointer min-h-11 px-4 py-2 text-sm gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold focus:ring-amber-500 shadow-amber-500/20">
                    <i data-lucide="radio" class="w-4 h-4" id="simBtnIcon"></i>
                    <span id="simBtnText">Inyectar telemetría</span>
                </button>
            </div>
        </form>

        {{-- Feedback reactivo en tiempo real --}}
        <div id="simFeedbackBox" class="hidden mt-4 p-4 rounded-xl text-xs flex items-start gap-3 transition-all duration-300">
            <div class="flex-1" id="simFeedbackContent"></div>
        </div>
    </div>
    @else
    @auth
    <div class="kin-panel mb-6 p-4 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
        <div class="flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-amber-500"></i>
            <span>Simulación SCADA en tiempo real disponible para usuarios con rol de <strong>Operador</strong> o <strong>Administrador</strong>.</span>
        </div>
        <span class="kin-eyebrow">MODO VISUALIZADOR</span>
    </div>
    @endauth
    @endcan

    <div class="kin-summary-grid">
        <article class="kin-panel kin-metric kin-metric-hero transition-all duration-500" id="kpiCardGeneration">
            <div class="kin-metric-label"><i data-lucide="zap" class="text-amber-500"></i>Generación solar acumulada</div>
            <div class="kin-metric-value"><span id="kpiTotalKwhMwh">{{ number_format(($stats['total_kwh'] ?? 0) / 1000, 1) }}</span> <small>MWh</small></div>
            <div class="kin-metric-note"><span id="kpiTotalKwhFormatted" class="text-amber-600 dark:text-amber-400">{{ number_format($stats['total_kwh'] ?? 0) }} kWh</span> de energía limpia registrada</div>
        </article>
        <article class="kin-panel kin-metric kin-metric-hero is-eco transition-all duration-500" id="kpiCardEco">
            <div class="kin-metric-label"><i data-lucide="leaf" class="text-emerald-600 dark:text-emerald-400"></i>Emisiones de CO₂ evitadas</div>
            <div class="kin-metric-value"><span id="kpiTotalCo2Tons">{{ number_format(($stats['total_co2_kg'] ?? 0) / 1000, 1) }}</span> <small>toneladas</small></div>
            <div class="kin-metric-note"><span id="kpiTotalCo2Formatted" class="text-emerald-700 dark:text-emerald-400">{{ number_format($stats['total_co2_kg'] ?? 0) }} kg de CO₂</span> · Factor de 0.40 kg/kWh</div>
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
            <div class="kin-ranking" id="topRankingContainer">
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
        <div class="p-4 sm:p-6" id="activeAlertsContainer">
            @if(isset($activeAlerts) && $activeAlerts->isNotEmpty())
                <x-table id="activeAlertsTable">
                    <thead><tr><th>Granja solar</th><th>Período</th><th>Esperada</th><th>Real</th><th>Déficit</th><th>Seguimiento</th></tr></thead>
                    <tbody id="activeAlertsTbody">
                    @foreach($activeAlerts as $alert)
                        <tr><td><strong>{{ $alert->solarFarm?->name ?? 'Granja no disponible' }}</strong><span class="block text-[10px] mt-1" style="color:var(--kin-muted)">{{ $alert->solarFarm?->department?->name }}</span></td><td>{{ $alert->period }}</td><td>{{ number_format($alert->estimated_kwh,1) }} kWh</td><td>{{ number_format($alert->real_kwh,1) }} kWh</td><td><x-badge variant="danger">−{{ $alert->deviation_percentage }}%</x-badge></td><td><a class="kin-text-link" href="{{ route('alerts.show',$alert) }}">Ver detalles <i data-lucide="arrow-up-right"></i></a></td></tr>
                    @endforeach
                    </tbody>
                </x-table>
            @else
                <div id="noAlertsState" class="flex items-center gap-4 p-5 rounded-xl bg-emerald-500/5 border border-emerald-500/15"><i data-lucide="circle-check" class="w-7 h-7 text-emerald-600 dark:text-emerald-400"></i><div><p class="text-sm font-semibold">Sin alertas activas</p><p class="text-xs mt-1" style="color:var(--kin-muted)">No hay desviaciones pendientes de seguimiento.</p></div></div>
            @endif
        </div>
    </section>
</div>
@endsection
@push('scripts')
<script>
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
        });
    }

    const chartCanvas = document.getElementById('generationComparisonChart');
    if (chartCanvas && window.Chart) {
        const ctx = chartCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0,0,0,280);
        gradient.addColorStop(0,'rgba(245,158,11,.22)');
        gradient.addColorStop(1,'rgba(245,158,11,0)');
        window.energyChartInstance = new Chart(ctx, {
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

    // Manejador Asíncrono de Simulación de Telemetría SCADA (En Tiempo Real sin Recargar)
    const simForm = document.getElementById('telemetrySimulationForm');
    if (simForm) {
        simForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const farmSelect = document.getElementById('simSolarFarmId');
            const scenarioSelect = document.getElementById('simScenario');
            const btnSubmit = document.getElementById('btnSimulateSubmit');
            const btnText = document.getElementById('simBtnText');
            const feedbackBox = document.getElementById('simFeedbackBox');
            const feedbackContent = document.getElementById('simFeedbackContent');

            if (!farmSelect || !scenarioSelect || !btnSubmit) return;

            const farmId = farmSelect.value;
            const scenario = scenarioSelect.value;

            // Estado de carga
            btnSubmit.disabled = true;
            if (btnText) btnText.textContent = 'Inyectando telemetría...';

            try {
                const response = await fetch("{{ route('telemetry.simulate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        solar_farm_id: farmId,
                        scenario: scenario
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Error al comunicar con la pasarela SCADA IoT.');
                }

                const res = await response.json();

                // 1. Actualizar Tarjetas Hero KPI (Generación MWh y CO2 Toneladas) con efecto visual de pulso
                const kpiGen = document.getElementById('kpiTotalKwhMwh');
                const kpiGenFormatted = document.getElementById('kpiTotalKwhFormatted');
                const kpiEco = document.getElementById('kpiTotalCo2Tons');
                const kpiEcoFormatted = document.getElementById('kpiTotalCo2Formatted');
                const cardGen = document.getElementById('kpiCardGeneration');
                const cardEco = document.getElementById('kpiCardEco');

                if (res.stats) {
                    if (kpiGen) kpiGen.textContent = res.stats.total_kwh_mwh;
                    if (kpiGenFormatted) kpiGenFormatted.textContent = `${res.stats.total_kwh_formatted} kWh`;
                    if (kpiEco) kpiEco.textContent = res.stats.total_co2_tons;
                    if (kpiEcoFormatted) kpiEcoFormatted.textContent = `${res.stats.total_co2_kg_formatted} kg de CO₂`;

                    // Animación visual de confirmación en tiempo real
                    if (cardGen) {
                        cardGen.classList.add('ring-2', 'ring-amber-500', 'scale-[1.02]');
                        setTimeout(() => cardGen.classList.remove('ring-2', 'ring-amber-500', 'scale-[1.02]'), 1500);
                    }
                    if (cardEco) {
                        cardEco.classList.add('ring-2', 'ring-emerald-500', 'scale-[1.02]');
                        setTimeout(() => cardEco.classList.remove('ring-2', 'ring-emerald-500', 'scale-[1.02]'), 1500);
                    }
                }

                // 2. Actualizar Gráfica Chart.js en tiempo real
                if (window.energyChartInstance && res.chart) {
                    window.energyChartInstance.data.labels = res.chart.labels;
                    window.energyChartInstance.data.datasets[0].data = res.chart.real;
                    window.energyChartInstance.data.datasets[1].data = res.chart.expected;
                    window.energyChartInstance.update('active');
                }

                // 3. Actualizar Ranking Departamental
                if (res.ranking && res.ranking.length > 0) {
                    const rankingContainer = document.getElementById('topRankingContainer');
                    if (rankingContainer) {
                        const itemsHtml = res.ranking.map((dept, idx) => `
                            <div class="kin-rank-item">
                                <div class="kin-rank-line"><strong><span class="kin-rank-number">${String(idx + 1).padStart(2, '0')}</span>${escapeHtml(dept.name)}</strong><span>${escapeHtml(dept.kwh_mwh)}</span></div>
                                <div class="kin-progress"><span style="width:${Math.min(100, Math.max(0, dept.share))}%"></span></div>
                                <div class="kin-rank-meta"><span>${escapeHtml(dept.co2)}</span><span>${dept.share}%</span></div>
                            </div>
                        `).join('');
                        rankingContainer.innerHTML = itemsHtml + `<a class="kin-text-link" href="{{ route('reports.index') }}">Explorar los 22 departamentos <i data-lucide="arrow-right"></i></a>`;
                    }
                }

                // 4. Si disparó alerta RF-14, insertar la nueva alerta en vivo en la tabla
                if (res.new_alert) {
                    const alert = res.new_alert;
                    const tbody = document.getElementById('activeAlertsTbody');
                    const container = document.getElementById('activeAlertsContainer');

                    const newRowHtml = `
                        <tr class="bg-rose-500/15 animate-pulse transition-all duration-700">
                            <td>
                                <strong>${escapeHtml(alert.farm_name)}</strong>
                                <span class="block text-[10px] mt-1 text-rose-600 dark:text-rose-400 font-semibold">${escapeHtml(alert.department_name)} · ⚡ Telemetría en vivo</span>
                            </td>
                            <td>${escapeHtml(alert.period)}</td>
                            <td>${escapeHtml(alert.estimated_kwh)} kWh</td>
                            <td class="text-rose-600 dark:text-rose-400 font-bold">${escapeHtml(alert.real_kwh)} kWh</td>
                            <td><x-badge variant="danger">−${alert.deviation_percentage}%</x-badge></td>
                            <td><a class="kin-text-link font-semibold text-rose-600 dark:text-rose-400" href="${escapeHtml(alert.show_url)}">Ver detalles <i data-lucide="arrow-up-right"></i></a></td>
                        </tr>
                    `;

                    if (tbody) {
                        tbody.insertAdjacentHTML('afterbegin', newRowHtml);
                    } else if (container) {
                        container.innerHTML = `
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm" id="activeAlertsTable">
                                    <thead class="border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500">
                                        <tr><th class="p-3">Granja solar</th><th class="p-3">Período</th><th class="p-3">Esperada</th><th class="p-3">Real</th><th class="p-3">Déficit</th><th class="p-3">Seguimiento</th></tr>
                                    </thead>
                                    <tbody id="activeAlertsTbody">
                                        ${newRowHtml}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    }
                }

                // 5. Mostrar caja de feedback ejecutivo
                if (feedbackBox && feedbackContent && res.reading) {
                    feedbackBox.classList.remove('hidden');
                    if (res.reading.alert_triggered) {
                        feedbackBox.className = 'mt-4 p-4 rounded-xl text-xs flex items-start gap-3 bg-rose-500/10 border border-rose-500/30 text-rose-900 dark:text-rose-200 transition-all duration-300';
                        feedbackContent.innerHTML = `
                            <div class="font-bold text-sm text-rose-700 dark:text-rose-300 flex items-center justify-between">
                                <span>⚠️ Alerta SCADA Automática Generada (RF-14: Déficit ≥ 20%)</span>
                                <span class="text-[11px] font-normal opacity-80">${res.reading.timestamp}</span>
                            </div>
                            <p class="mt-1 font-medium text-slate-800 dark:text-slate-100">Inyección a <strong>${escapeHtml(res.reading.farm_name)}</strong> (${escapeHtml(res.reading.department_name)}): Generación real de <strong>${res.reading.real_kwh} kWh</strong> frente a una esperada de ${res.reading.estimated_kwh} kWh (<strong class="text-rose-600 dark:text-rose-400">Déficit: −${res.reading.deficit_percentage}%</strong>).</p>
                            <p class="mt-1 text-[11px] text-slate-600 dark:text-slate-300">${escapeHtml(res.message)} · Se registró la alerta activa en el sistema y se actualizaron la gráfica y los totales nacionales sin recargar la página.</p>
                        `;
                    } else {
                        feedbackBox.className = 'mt-4 p-4 rounded-xl text-xs flex items-start gap-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-900 dark:text-emerald-200 transition-all duration-300';
                        feedbackContent.innerHTML = `
                            <div class="font-bold text-sm text-emerald-700 dark:text-emerald-300 flex items-center justify-between">
                                <span>✓ Telemetría IoT Inyectada con Éxito (Operación Normal)</span>
                                <span class="text-[11px] font-normal opacity-80">${res.reading.timestamp}</span>
                            </div>
                            <p class="mt-1 font-medium text-slate-800 dark:text-slate-100">Inyección a <strong>${escapeHtml(res.reading.farm_name)}</strong>: Generación real de <strong>${res.reading.real_kwh} kWh</strong> (Desviación: −${res.reading.deficit_percentage}%, dentro de tolerancia). CO₂ evitado: <strong>${res.reading.co2_kg} kg</strong>.</p>
                            <p class="mt-1 text-[11px] text-slate-600 dark:text-slate-300">${escapeHtml(res.message)} · Gráfica histórica y métricas actualizadas en tiempo real.</p>
                        `;
                    }
                }

                window.lucide?.createIcons();

            } catch (err) {
                if (feedbackBox && feedbackContent) {
                    feedbackBox.classList.remove('hidden');
                    feedbackBox.className = 'mt-4 p-4 rounded-xl text-xs flex items-start gap-3 bg-rose-500/10 border border-rose-500/30 text-rose-900 dark:text-rose-200';
                    feedbackContent.innerHTML = `<strong>Error en la simulación:</strong> ${escapeHtml(err.message)}`;
                }
            } finally {
                btnSubmit.disabled = false;
                if (btnText) btnText.textContent = 'Inyectar telemetría';
                window.lucide?.createIcons();
            }
        });
    }
</script>
@endpush
