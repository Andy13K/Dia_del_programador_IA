@extends('layouts.app', ['title' => 'Gestión de Alertas de Generación'])

@section('content')
<div class="space-y-6">

    <!-- Header Alertas -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold text-xs">
                    Requerimiento RF-14
                </span>
                <span class="text-xs text-slate-400">Regla: Real &le; 80% de Esperada (Déficit &ge; 20%)</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Monitoreo de Anomalías y Desviaciones
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Registro de eventos donde la producción real cayó por debajo del umbral mínimo de tolerancia.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 font-bold text-xs border border-rose-200 dark:border-rose-800/60 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                <span>{{ $stats['active'] ?? 2 }} Alertas Activas</span>
            </span>
        </div>
    </div>

    <!-- VISTA MÓVIL: Tarjetas de Alertas sin scroll horizontal (md:hidden) -->
    <div class="md:hidden space-y-3">
        @if(isset($alerts) && count($alerts) > 0)
            @foreach($alerts as $alert)
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl {{ $alert->status === 'active' ? 'bg-rose-500/10 text-rose-500' : 'bg-emerald-500/10 text-emerald-500' }} flex items-center justify-center flex-shrink-0">
                                <i data-lucide="{{ $alert->status === 'active' ? 'alert-triangle' : 'check-circle' }}" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-mono text-slate-400 block">#ALT-{{ str_pad((string)$alert->id, 2, '0', STR_PAD_LEFT) }} • {{ $alert->period }}</span>
                                <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate mt-0.5">
                                    {{ $alert->solarFarm->name ?? 'Granja #'.$alert->solar_farm_id }}
                                </h4>
                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                    {{ $alert->solarFarm->department->name ?? 'Guatemala' }}
                                </div>
                            </div>
                        </div>

                        <!-- Badge de Estado -->
                        <div class="flex-shrink-0">
                            @if($alert->status === 'active')
                                <x-badge variant="danger">Activa</x-badge>
                            @else
                                <x-badge variant="success">Resuelta</x-badge>
                            @endif
                        </div>
                    </div>

                    <!-- Resumen del Déficit y Botón Rojo "Ver más detalles" -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Déficit Crítico</span>
                            <span class="text-sm font-black text-rose-600 dark:text-rose-400">
                                -{{ $alert->deviation_percentage }}%
                            </span>
                        </div>

                        <!-- Botón Rojo Ver más detalles -->
                        <a href="{{ route('alerts.show', $alert) }}" 
                           class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm shadow-rose-600/20 active:scale-95 transition">
                            <span>Ver más detalles</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- VISTA ESCRITORIO: Tabla Completa de Alertas (hidden md:block) -->
    <div class="hidden md:block bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-6">
        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                <tr>
                    <th class="px-5 py-4 text-center whitespace-nowrap">ID</th>
                    <th class="px-5 py-4 text-left whitespace-nowrap">Granja Afectada</th>
                    <th class="px-5 py-4 text-left whitespace-nowrap">Departamento</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Período</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Esperada (kWh)</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Real (kWh)</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Desviación</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Estado</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @if(isset($alerts) && count($alerts) > 0)
                    @foreach($alerts as $alert)
                        <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/60 transition-colors duration-150 {{ $alert->status === 'resolved' ? 'opacity-75' : '' }}">
                            <td class="px-5 py-4 font-mono text-slate-400 text-center whitespace-nowrap">#ALT-{{ str_pad((string)$alert->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($alert->status === 'active')
                                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                                    @else
                                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 flex-shrink-0"></i>
                                    @endif
                                    <a href="{{ route('alerts.show', $alert) }}" class="hover:text-rose-600 dark:hover:text-rose-400 transition">
                                        {{ $alert->solarFarm->name ?? 'Granja #'.$alert->solar_farm_id }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300 font-semibold whitespace-nowrap text-left">{{ $alert->solarFarm->department->name ?? 'Guatemala' }}</td>
                            <td class="px-5 py-4 font-mono font-medium text-slate-700 dark:text-slate-300 text-center whitespace-nowrap">{{ $alert->period }}</td>
                            <td class="px-5 py-4 text-center font-mono text-slate-500 whitespace-nowrap">{{ number_format((float)$alert->estimated_kwh, 2) }}</td>
                            <td class="px-5 py-4 text-center font-mono font-bold whitespace-nowrap {{ $alert->status === 'active' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-300' }}">{{ number_format((float)$alert->real_kwh, 2) }}</td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full {{ $alert->status === 'active' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold' }} text-xs border {{ $alert->status === 'active' ? 'border-rose-200 dark:border-rose-800' : 'border-slate-200 dark:border-slate-700' }}">
                                    -{{ $alert->deviation_percentage }}%
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if($alert->status === 'active')
                                    <x-badge variant="danger">Activa</x-badge>
                                @else
                                    <x-badge variant="success">Resuelta</x-badge>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <a href="{{ route('alerts.show', $alert) }}" 
                                       class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-600 hover:text-white dark:bg-slate-800 dark:hover:bg-rose-600 dark:hover:text-white text-slate-600 dark:text-slate-300 flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 shadow-2xs" 
                                       title="Ver detalles completos">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if($alert->status === 'active')
                                        @can('manage-alerts')
                                            <button type="button" 
                                                    onclick="openResolveModal('{{ $alert->id }}', '{{ addslashes($alert->solarFarm->name ?? '') }}', '-{{ $alert->deviation_percentage }}%')" 
                                                    class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition-all duration-200 hover:scale-105 active:scale-95 shadow-sm">
                                                Atender
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <!-- Alerta Demo 1 -->
                    <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/60 transition-colors duration-150">
                        <td class="px-5 py-4 font-mono text-slate-400 text-center whitespace-nowrap">#ALT-01</td>
                        <td class="px-5 py-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500 flex-shrink-0"></i>
                                <span>Granja Solar Guayacán</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-300 font-semibold whitespace-nowrap text-left">Petén</td>
                        <td class="px-5 py-4 font-mono font-medium text-slate-700 dark:text-slate-300 text-center whitespace-nowrap">2026-08</td>
                        <td class="px-5 py-4 text-center font-mono text-slate-500 whitespace-nowrap">145,000.00</td>
                        <td class="px-5 py-4 text-center font-mono font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap">110,000.00</td>
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-black text-xs border border-rose-200 dark:border-rose-800">
                                -24.14%
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <x-badge variant="danger">Activa</x-badge>
                        </td>
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <button type="button" onclick="openResolveModal('1', 'Granja Solar Guayacán', '-24.14%')" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition active:scale-95 shadow-sm">
                                Atender
                            </button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </x-table>
    </div>

    <!-- Modal de Resolución de Alertas -->
    <div id="resolveModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-5 h-5 text-amber-500"></i>
                    <span>Resolución Técnica de Alerta</span>
                </h3>
                <button type="button" onclick="closeResolveModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form id="resolveForm" method="POST" action="">
                @csrf
                <div class="space-y-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Granja:</span>
                        <strong id="modalFarmName" class="text-sm font-bold text-slate-900 dark:text-white"></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Desviación Registrada:</span>
                        <strong id="modalDeviation" class="text-sm font-bold text-rose-600"></strong>
                    </div>

                    <div>
                        <label for="resolution_notes" class="block font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Causa y Acción Correctiva <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="resolution_notes" id="resolution_notes" rows="3" required placeholder="Ej: Se reemplazaron fusibles en el inversor 2 y se realizó lavado de paneles tras tormenta." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeResolveModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-200">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-sm">
                            Marcar como Resuelta
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openResolveModal(alertId, farmName, deviation) {
        document.getElementById('modalFarmName').textContent = farmName;
        document.getElementById('modalDeviation').textContent = deviation;
        document.getElementById('resolveForm').action = `/alerts/${alertId}/resolve`;
        document.getElementById('resolveModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').classList.add('hidden');
    }
</script>
@endpush
