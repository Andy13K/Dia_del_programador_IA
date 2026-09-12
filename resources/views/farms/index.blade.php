@extends('layouts.app', ['title' => 'Granjas Solares'])

@section('content')
<div class="space-y-6">

    <!-- Encabezado y Acciones -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 font-bold text-xs">
                    REGISTRO SOLAR
                </span>
                <span class="text-xs text-slate-400">Infraestructura Fotovoltaica</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Cada granja, una nueva posibilidad.
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Plantas fotovoltaicas registradas y supervisadas con geolocalización en los 22 departamentos.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @can('manage-farms')
                <x-button href="{{ route('farms.create') }}" variant="primary" size="md">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                    <span>Registrar granja</span>
                </x-button>
            @endcan
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form data-reactive-filter method="GET" action="{{ route('farms.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Buscar por Nombre</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ej. Escuintla, Guayacán..." 
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Departamento</label>
                <select name="department_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Todos los departamentos</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ ($filters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Estado Operativo</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Todos los estados</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Activa</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactiva</option>
                    <option value="maintenance" {{ ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 text-xs font-bold rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 transition">
                    Filtrar
                </button>
                <a href="{{ route('farms.index') }}" class="px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- VISTA MÓVIL: Tarjetas limpias sin scroll lateral (md:hidden) -->
    <div class="md:hidden space-y-3">
        @forelse($farms as $farm)
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 flex-shrink-0">
                        <i data-lucide="sun" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">
                            {{ $farm->name }}
                        </h4>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            <span class="truncate font-medium">{{ $farm->department->name ?? 'N/A' }}</span>
                            <span>•</span>
                            @if($farm->status === 'active')
                                <span class="text-emerald-500 font-semibold">Activa</span>
                            @elseif($farm->status === 'maintenance')
                                <span class="text-amber-500 font-semibold">Mantenimiento</span>
                            @else
                                <span class="text-slate-400 font-semibold">Inactiva</span>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('farms.show', $farm) }}" class="flex-shrink-0 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs flex items-center gap-1 shadow-sm transition active:scale-95">
                    <span>Ver detalles</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        @empty
            <div class="p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-slate-400 text-xs">
                No se encontraron granjas solares con los filtros seleccionados.
            </div>
        @endforelse

        @if($farms->hasPages())
            <div class="p-3 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800">
                {{ $farms->links() }}
            </div>
        @endif
    </div>

    <!-- VISTA ESCRITORIO: Tabla Completa Estética, Centrada y Justificada (hidden md:block) -->
    <div class="hidden md:block bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200/80 dark:border-slate-800 text-[11px] uppercase font-bold text-slate-500 dark:text-slate-400 tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left whitespace-nowrap">Granja Solar</th>
                        <th scope="col" class="px-6 py-4 text-left whitespace-nowrap">Departamento</th>
                        <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Coordenadas GPS</th>
                        <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Potencia (kW)</th>
                        <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Familias</th>
                        <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Estado</th>
                        <th scope="col" class="px-6 py-4 text-center whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($farms as $farm)
                        <tr class="hover:bg-amber-50/40 dark:hover:bg-slate-800/60 transition-colors duration-150">
                            <!-- Granja Solar (Nombre en una sola línea que admite más caracteres) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 flex-shrink-0 shadow-sm">
                                        <i data-lucide="sun" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('farms.show', $farm) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition-colors whitespace-nowrap text-[13px]">
                                            {{ $farm->name }}
                                        </a>
                                        <span class="block text-[11px] text-slate-400 whitespace-nowrap">
                                            {{ $farm->solarPanels->sum('pivot.quantity') }} paneles instalados
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Departamento -->
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-slate-700 dark:text-slate-300 text-left">
                                <span class="inline-flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ $farm->department->name ?? 'N/A' }}
                                </span>
                            </td>

                            <!-- Coordenadas GPS (En una sola línea, centrado) -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="font-mono text-[11px] px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60 whitespace-nowrap inline-block">
                                    {{ number_format((float)$farm->latitude, 4) }}°, {{ number_format((float)$farm->longitude, 4) }}°
                                </span>
                            </td>

                            <!-- Potencia (kW) (En una sola línea, centrado) -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="font-extrabold text-amber-600 dark:text-amber-400 text-xs whitespace-nowrap px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/50 inline-block">
                                    {{ number_format((float)$farm->calculated_capacity_kw, 1) }} kW
                                </span>
                            </td>

                            <!-- Familias (Centrado) -->
                            <td class="px-6 py-4 whitespace-nowrap text-center font-semibold text-slate-700 dark:text-slate-300">
                                {{ number_format($farm->benefited_families) }}
                            </td>

                            <!-- Estado (Centrado) -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($farm->status === 'active')
                                    <x-badge variant="success">Activa</x-badge>
                                @elseif($farm->status === 'maintenance')
                                    <x-badge variant="warning">Mantenimiento</x-badge>
                                @else
                                    <x-badge variant="neutral">Inactiva</x-badge>
                                @endif
                            </td>

                            <!-- Acciones (Solo el ojito para ver detalles en vista web, más editar/eliminar) -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <!-- Botón Solo Ojito -->
                                    <a href="{{ route('farms.show', $farm) }}" 
                                       class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-amber-500 hover:text-slate-950 dark:bg-slate-800 dark:hover:bg-amber-500 dark:hover:text-slate-950 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 shadow-2xs" 
                                       title="Ver detalles completos">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    @can('manage-farms')
                                        <a href="{{ route('farms.edit', $farm) }}" 
                                           class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 shadow-2xs" 
                                           title="Editar granja">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>

                                        <form method="POST" action="{{ route('farms.destroy', $farm) }}" class="inline" onsubmit="return confirm('¿Confirmas que deseas eliminar esta granja solar?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 shadow-2xs" 
                                                    title="Eliminar granja">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <i data-lucide="sun" class="w-10 h-10 text-slate-300"></i>
                                    <p class="text-sm font-medium">No se encontraron granjas solares con los filtros seleccionados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($farms->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $farms->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
