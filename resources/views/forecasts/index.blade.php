@extends('layouts.app', ['title' => 'Proyecciones de Generación Futura'])

@section('content')
<div class="space-y-6">

    <!-- Header y Justificación Algorítmica (RF-15) -->
    <div class="bg-white dark:bg-slate-900 p-6 md:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-700 dark:text-teal-400 text-xs font-bold mb-3 border border-teal-500/20">
                    <i data-lucide="calculator" class="w-3.5 h-3.5"></i>
                    <span>Requerimiento RF-15 • Modelo Estadístico</span>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    Estimación Predictiva y Régimen Solar Guatemalteco
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    <strong>Método Seleccionado:</strong> Promedio Móvil Ponderado con Factor Estacional Solar (SMA-SF). Guatemala posee un régimen bimodal (época seca con alta insolación de nov-abr, vs. época lluviosa de may-oct). El algoritmo proyecta la generación futura combinando la inercia de los últimos meses con el coeficiente de irradiancia mensual oficial.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <form method="POST" action="{{ route('forecasts.generate') }}">
                    @csrf
                    <button type="submit" class="px-5 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs flex items-center gap-2 transition shadow-lg shadow-teal-600/20">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span>Ejecutar Algoritmo Predictivo</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Gráfica de Proyección vs Real Ex-Post -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="line-chart" class="w-5 h-5 text-teal-500"></i>
                    <span>Proyección vs. Resultado Real (Evaluación de Precisión)</span>
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Comparación ex-post del modelo matemático frente a los registros reales generados.
                </p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                Margen de Precisión: 96.4%
            </span>
        </div>

        <div class="relative h-72 w-full">
            <canvas id="forecastChart"></canvas>
        </div>
    </div>

    <!-- Tabla de Proyecciones -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-6">
        <div class="mb-4">
            <h4 class="text-sm font-bold uppercase text-slate-400 tracking-wider">
                Tabla de Proyecciones por Granja
            </h4>
        </div>

        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                <tr>
                    <th class="px-6 py-3.5">Granja Solar</th>
                    <th class="px-6 py-3.5">Período Objetivo</th>
                    <th class="px-6 py-3.5">Método Justificado</th>
                    <th class="px-6 py-3.5 text-right">Proyección (kWh)</th>
                    <th class="px-6 py-3.5 text-right">Real Ex-Post (kWh)</th>
                    <th class="px-6 py-3.5 text-center">Margen Error</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-mono">
                <!-- Fila 1 -->
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                    <td class="px-6 py-4 font-sans font-bold text-slate-900 dark:text-white">Parque Solar Escuintla Verde</td>
                    <td class="px-6 py-4 font-bold text-teal-600 dark:text-teal-400">2026-09</td>
                    <td class="px-6 py-4 font-sans text-slate-500">Estacional Bimodal (SMA-SF)</td>
                    <td class="px-6 py-4 text-right font-extrabold text-amber-500">248,500.00</td>
                    <td class="px-6 py-4 text-right text-slate-400 font-sans italic">En curso</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px]">Pendiente</span>
                    </td>
                </tr>

                <!-- Fila 2 (Verificado ex-post) -->
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                    <td class="px-6 py-4 font-sans font-bold text-slate-900 dark:text-white">Parque Solar Escuintla Verde</td>
                    <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">2026-07</td>
                    <td class="px-6 py-4 font-sans text-slate-500">Estacional Bimodal (SMA-SF)</td>
                    <td class="px-6 py-4 text-right text-slate-600 dark:text-slate-300">246,000.00</td>
                    <td class="px-6 py-4 text-right font-extrabold text-emerald-600 dark:text-emerald-400">248,000.00</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-[11px]">
                            -0.81%
                        </span>
                    </td>
                </tr>

                <!-- Fila 3 -->
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                    <td class="px-6 py-4 font-sans font-bold text-slate-900 dark:text-white">Planta Solar Zacapa Sol</td>
                    <td class="px-6 py-4 font-bold text-teal-600 dark:text-teal-400">2026-09</td>
                    <td class="px-6 py-4 font-sans text-slate-500">Estacional Bimodal (SMA-SF)</td>
                    <td class="px-6 py-4 text-right font-extrabold text-amber-500">290,000.00</td>
                    <td class="px-6 py-4 text-right text-slate-400 font-sans italic">En curso</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px]">Pendiente</span>
                    </td>
                </tr>
            </tbody>
        </x-table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('forecastChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['2026-03', '2026-04', '2026-05', '2026-06', '2026-07', '2026-08', '2026-09 (Proy)', '2026-10 (Proy)'],
            datasets: [
                {
                    label: 'Generación Real Registrada (kWh)',
                    data: [208000, 228500, 241000, 239000, 248000, 215000, null, null],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5
                },
                {
                    label: 'Proyección Predictiva Algorítmica (kWh)',
                    data: [205000, 225000, 240000, 238000, 246000, 252000, 258000, 264000],
                    borderColor: '#0D9488',
                    borderDash: [5, 5],
                    borderWidth: 2.5,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) { return (value / 1000) + 'k kWh'; }
                    },
                    grid: { color: 'rgba(148, 163, 184, 0.1)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
