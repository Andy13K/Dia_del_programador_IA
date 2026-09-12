@extends('layouts.app', ['title' => 'Detalle de Alerta'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('alerts.index') }}" class="hover:text-amber-500 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Volver a Alertas</span>
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Ficha de Alerta #{{ $alert->id }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Incidencia técnica por déficit de generación igual o superior al 20% (RF-14).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($alert->solarFarm)
                <a href="{{ route('farms.show', $alert->solarFarm) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold text-slate-700 dark:text-slate-200 hover:border-amber-400 hover:text-amber-500 transition shadow-sm">
                    <i data-lucide="sun" class="w-3.5 h-3.5 text-amber-500"></i>
                    <span>Ver Granja</span>
                </a>
                <a href="{{ route('map.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs font-bold text-slate-700 dark:text-slate-200 hover:border-emerald-400 hover:text-emerald-500 transition shadow-sm">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-500"></i>
                    <span>Ver en Mapa</span>
                </a>
            @endif
            @if($alert->status === 'active')
                <x-badge variant="danger" size="md">Alerta Activa</x-badge>
            @else
                <x-badge variant="success" size="md">Alerta Resuelta</x-badge>
            @endif
        </div>
    </div>

    <!-- Comparativa Numérica de la Anomalía -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-card>
            <span class="text-xs text-slate-400 font-semibold uppercase">Generación Esperada</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1 font-mono">
                {{ number_format((float)$alert->estimated_kwh, 1) }}
            </div>
            <span class="text-[11px] text-slate-500">kWh programados</span>
        </x-card>

        <x-card>
            <span class="text-xs text-slate-400 font-semibold uppercase">Generación Real</span>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1 font-mono">
                {{ number_format((float)$alert->real_kwh, 1) }}
            </div>
            <span class="text-[11px] text-slate-500">kWh registrados en planta</span>
        </x-card>

        <x-card>
            <span class="text-xs text-slate-400 font-semibold uppercase">Déficit Crítico</span>
            <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1 font-mono">
                -{{ $alert->deviation_percentage }}%
            </div>
            <span class="text-[11px] text-rose-500 font-bold">Umbral de activación &ge; 20%</span>
        </x-card>
    </div>

    <!-- Ficha de la Planta y Evento -->
    <x-card>
        <div class="space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-slate-400 block font-semibold">Granja Solar Afectada:</span>
                    <strong class="text-slate-900 dark:text-white text-sm">{{ $alert->solarFarm->name ?? 'N/A' }}</strong>
                    <span class="text-slate-500 block text-[11px]">{{ $alert->solarFarm->department->name ?? 'Guatemala' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Período de Medición:</span>
                    <strong class="text-slate-900 dark:text-white text-sm font-mono">{{ $alert->period }}</strong>
                    <span class="text-slate-500 block text-[11px]">Generada: {{ $alert->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if($alert->status === 'resolved')
                <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 space-y-2">
                    <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-300 font-bold">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Incidencia Resuelta</span>
                    </div>
                    <p class="text-slate-700 dark:text-slate-300">
                        <strong>Notas de Resolución:</strong> {{ $alert->resolution_notes }}
                    </p>
                    <div class="text-[11px] text-slate-500 pt-1 border-t border-emerald-200 dark:border-emerald-800">
                        Atendido por: {{ $alert->resolvedBy->name ?? 'Operador del Sistema' }} el {{ $alert->resolved_at }}
                    </div>
                </div>
            @else
                <!-- Formulario para Resolver Alerta -->
                @can('manage-alerts')
                    <div class="p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800">
                        <h4 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-2">
                            <i data-lucide="wrench" class="w-4 h-4 text-amber-500"></i>
                            <span>Atención y Cierre Técnico de la Incidencia</span>
                        </h4>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 mb-3">
                            Describa las acciones correctivas aplicadas en la granja (ej. lavado de módulos, reemplazo de inversor, reconexión de string fotovoltaico).
                        </p>

                        <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="space-y-3">
                            @csrf
                            <textarea name="resolution_notes" rows="3" required maxlength="500" placeholder="Detalle técnico de la resolución aplicada..."
                                      class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500"></textarea>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-md shadow-amber-500/20 flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                    <span>Registrar Resolución y Cerrar Alerta</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endif
        </div>
    </x-card>

</div>
@endsection
