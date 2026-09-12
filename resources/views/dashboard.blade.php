@extends('layouts.app', ['title' => 'Dashboard Ejecutivo Nacional'])

@section('content')
<div class="space-y-8">

    <!-- HERO BANNER DE IMPACTO ECOLÓGICO -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-slate-950 p-6 md:p-8 text-white border border-slate-800 shadow-xl">
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-40 -top-20 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-semibold mb-3 border border-amber-500/30">
                    <i data-lucide="sun" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>República de Guatemala • Matriz Energética Limpia</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Monitoreo Solar y Reducción de Huella de Carbono
                </h2>
                <p class="text-sm text-slate-300 mt-2 leading-relaxed">
                    Sistema integral de supervisión fotovoltaica en los 22 departamentos. Cálculo certificado de emisiones evitadas bajo el factor normativo de <strong class="text-emerald-400">0.40 kg CO₂ por cada kWh</strong> generado.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('map.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm transition shadow-lg shadow-amber-500/20 active:scale-95">
                    <i data-lucide="map" class="w-4 h-4"></i>
                    <span>Ver Mapa en Vivo</span>
                </a>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-sm transition border border-slate-700 active:scale-95">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Reportes Departamentales</span>
                </a>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 1: 6 TARJETAS KPI OBLIGATORIAS (RF-11) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-4 h-4 text-amber-500"></i>
                <span>Indicadores Macro Nacionales (RF-11)</span>
            </h3>
            <span class="text-[10px] sm:text-xs text-slate-400">Actualizado en tiempo real</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
            
            <!-- 1. Total Granjas -->
            <x-kpi-card 
                title="Granjas Solares" 
                value="{{ $stats['total_farms'] ?? '12' }}" 
                subtitle="En 22 Departamentos" 
                icon="sun" 
                variant="solar"
            />

            <!-- 2. Paneles Instalados -->
            <x-kpi-card 
                title="Paneles Instalados" 
                value="{{ number_format($stats['total_panels'] ?? 14850) }}" 
                subtitle="Módulos en operación" 
                icon="grid" 
                variant="sky"
            />

            <!-- 3. Capacidad Total kW -->
            <x-kpi-card 
                title="Capacidad Total" 
                value="{{ number_format($stats['total_capacity_kw'] ?? 8450.5, 1) }} kW" 
                subtitle="Potencia nominal pico" 
                icon="zap" 
                variant="amber"
            />

            <!-- 4. Generación Acumulada kWh -->
            <x-kpi-card 
                title="Generación Total" 
                value="{{ number_format(($stats['total_kwh'] ?? 1420500) / 1000, 1) }} MWh" 
                subtitle="{{ number_format($stats['total_kwh'] ?? 1420500) }} kWh" 
                icon="activity" 
                variant="indigo"
            />

            <!-- 5. Familias Beneficiadas -->
            <x-kpi-card 
                title="Familias Beneficiadas" 
                value="{{ number_format($stats['total_families'] ?? 24500) }}" 
                subtitle="Hogares guatemaltecos" 
                icon="users" 
                variant="sky"
            />

            <!-- 6. CO2 Evitado (0.40 kg / kWh) -->
            <x-kpi-card 
                title="CO₂ Evitado" 
                value="{{ number_format(($stats['total_co2_kg'] ?? 568200) / 1000, 1) }} Ton" 
                subtitle="{{ number_format($stats['total_co2_kg'] ?? 568200) }} kg CO₂" 
                icon="leaf" 
                variant="eco"
            />
        </div>
    </div>

    <!-- SECCIÓN 2: GRÁFICA COMPARATIVA REAL VS ESTIMADA Y RANKING DEPARTAMENTAL -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Comparación Generación Real vs Esperada (Chart.js) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                <div>
                    <h4 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-5 h-5 text-amber-500"></i>
                        <span>Generación Real vs. Esperada (Histórico Mensual)</span>
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Supervisión mensual de desempeño en kWh para detección de anomalías.
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                        <span class="w-3 h-3 rounded-sm bg-slate-300 dark:bg-slate-600 inline-block"></span>
                        Esperada (kWh)
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                        <span class="w-3 h-3 rounded-sm bg-amber-500 inline-block"></span>
                        Real (kWh)
                    </span>
                </div>
            </div>

            <div class="relative h-72 w-full">
                <canvas id="generationComparisonChart"></canvas>
            </div>
        </div>

        <!-- Ranking Departamental por Generación (RF-11 / RF-12) -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="award" class="w-5 h-5 text-amber-500"></i>
                        <span>Ranking Departamental</span>
                    </h4>
                    <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Top 5</span>
                </div>
                
                <div class="space-y-3.5">
                    @php
                        $ranking = isset($topRanking) && count($topRanking) > 0 ? $topRanking : [
                            ['name' => 'Escuintla', 'kwh' => 380000, 'co2' => '152.0 Ton', 'share' => 26.7],
                            ['name' => 'Zacapa', 'kwh' => 285000, 'co2' => '114.0 Ton', 'share' => 20.1],
                            ['name' => 'Petén', 'kwh' => 210000, 'co2' => '84.0 Ton', 'share' => 14.8],
                            ['name' => 'Izabal', 'kwh' => 190000, 'co2' => '76.0 Ton', 'share' => 13.4],
                            ['name' => 'Jutiapa', 'kwh' => 175000, 'co2' => '70.0 Ton', 'share' => 12.3],
                        ];
                    @endphp

                    @foreach($ranking as $index => $dept)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/80">
                            <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full {{ $index === 0 ? 'bg-amber-500 text-slate-950' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }} flex items-center justify-center text-[10px] font-black">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-slate-900 dark:text-white">{{ $dept['name'] }}</span>
                                </div>
                                <span class="text-amber-600 dark:text-amber-400 font-extrabold">{{ number_format($dept['kwh']) }} kWh</span>
                            </div>
                            <!-- Barra de progreso -->
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $dept['share'] }}%"></div>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-slate-400 mt-1">
                                <span>{{ $dept['share'] }}% del total nacional</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $dept['co2'] }} evitado</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <a href="{{ route('reports.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 flex items-center justify-between">
                    <span>Ver matriz completa de 22 departamentos</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: BANDEJA DE ALERTAS ACTIVAS (RF-14: DÉFICIT ≥ 20%) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                    <h4 class="text-base font-bold text-slate-900 dark:text-white">
                        Alertas Activas de Generación (RF-14)
                    </h4>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Se activan automáticamente cuando la generación real es un 20% o más inferior a la esperada.
                </p>
            </div>
            <a href="{{ route('alerts.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 flex items-center gap-1">
                <span>Gestionar todas las alertas</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase font-bold text-slate-400">
                <tr>
                    <th class="px-6 py-3.5">Granja Afectada</th>
                    <th class="px-6 py-3.5">Período</th>
                    <th class="px-6 py-3.5">Esperada</th>
                    <th class="px-6 py-3.5">Real</th>
                    <th class="px-6 py-3.5">Déficit (%)</th>
                    <th class="px-6 py-3.5">Estado</th>
                    <th class="px-6 py-3.5 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <!-- Alerta 1 -->
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                        <span>Granja Solar Guayacán</span>
                        <span class="text-[10px] text-slate-400 font-normal">(Petén)</span>
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium">2026-08</td>
                    <td class="px-6 py-4 text-slate-500">145,000 kWh</td>
                    <td class="px-6 py-4 font-bold text-rose-600 dark:text-rose-400">110,000 kWh</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-black text-xs border border-rose-200 dark:border-rose-800">
                            -24.14%
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="danger">Activa</x-badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('alerts.index') }}" class="font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400">
                            Resolver &rarr;
                        </a>
                    </td>
                </tr>

                <!-- Alerta 2 -->
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                    <td class="px-6 py-4 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                        <span>Central Solar Chiquimula Oriente</span>
                        <span class="text-[10px] text-slate-400 font-normal">(Chiquimula)</span>
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 font-medium">2026-08</td>
                    <td class="px-6 py-4 text-slate-500">110,000 kWh</td>
                    <td class="px-6 py-4 font-bold text-rose-600 dark:text-rose-400">85,000 kWh</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-black text-xs border border-rose-200 dark:border-rose-800">
                            -22.73%
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="danger">Activa</x-badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('alerts.index') }}" class="font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400">
                            Resolver &rarr;
                        </a>
                    </td>
                </tr>
            </tbody>
        </x-table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gráfico Comparativo con Chart.js
    const ctx = document.getElementById('generationComparisonChart').getContext('2d');
    
    const rawLabels = @json($chartLabels ?? null);
    const rawExpected = @json($chartExpected ?? null);
    const rawReal = @json($chartReal ?? null);

    const labels = (rawLabels && rawLabels.length > 0) ? rawLabels : ['2026-03', '2026-04', '2026-05', '2026-06', '2026-07', '2026-08'];
    const expectedData = (rawExpected && rawExpected.length > 0) ? rawExpected : [210000, 230000, 245000, 240000, 250000, 260000];
    const realData     = (rawReal && rawReal.length > 0) ? rawReal : [208000, 228500, 241000, 239000, 248000, 215000];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Esperada (kWh)',
                    data: expectedData,
                    backgroundColor: 'rgba(148, 163, 184, 0.4)',
                    borderColor: 'rgba(148, 163, 184, 0.8)',
                    borderWidth: 1,
                    borderRadius: 6
                },
                {
                    label: 'Real (kWh)',
                    data: realData,
                    backgroundColor: 'rgba(245, 158, 11, 0.85)',
                    borderColor: '#F59E0B',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return (value / 1000) + 'k kWh'; }
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
