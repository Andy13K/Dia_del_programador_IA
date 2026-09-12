@extends('layouts.app', ['title' => 'Granjas Solares'])

@section('content')
<div class="space-y-6">

    <!-- Encabezado y Acciones -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 font-bold text-xs">
                    Requerimientos RF-03 a RF-07
                </span>
                <span class="text-xs text-slate-400">Infraestructura Fotovoltaica</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Catálogo Nacional de Granjas Solares
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Plantas fotovoltaicas registradas y supervisadas con geolocalización en los 22 departamentos.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @can('manage-farms')
                <x-button href="{{ route('farms.create') }}" variant="primary" size="md">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                    <span>Registrar Nueva Granja</span>
                </x-button>
            @endcan
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('farms.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
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

    <!-- Tabla de Granjas -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                <tr>
                    <th class="px-6 py-4">Granja Solar</th>
                    <th class="px-6 py-4">Departamento</th>
                    <th class="px-6 py-4">Coordenadas GPS</th>
                    <th class="px-6 py-4 text-right">Potencia (kW)</th>
                    <th class="px-6 py-4 text-right">Familias</th>
                    <th class="px-6 py-4 text-center">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @forelse($farms as $farm)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 flex-shrink-0">
                                    <i data-lucide="sun" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <a href="{{ route('farms.show', $farm) }}" class="font-bold text-slate-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-400 transition">
                                        {{ $farm->name }}
                                    </a>
                                    <span class="block text-[11px] text-slate-400">
                                        {{ $farm->solarPanels->sum('pivot.quantity') }} paneles instalados
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                            {{ $farm->department->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-500 text-[11px]">
                            {{ number_format((float)$farm->latitude, 4) }}, {{ number_format((float)$farm->longitude, 4) }}
                        </td>
                        <td class="px-6 py-4 text-right font-extrabold text-amber-600 dark:text-amber-400">
                            {{ number_format((float)$farm->calculated_capacity_kw, 1) }} kW
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-700 dark:text-slate-300">
                            {{ number_format($farm->benefited_families) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($farm->status === 'active')
                                <x-badge variant="success">Activa</x-badge>
                            @elseif($farm->status === 'maintenance')
                                <x-badge variant="warning">Mantenimiento</x-badge>
                            @else
                                <x-badge variant="neutral">Inactiva</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="{{ route('farms.show', $farm) }}" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white transition" title="Ver ficha">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                @can('manage-farms')
                                    <a href="{{ route('farms.edit', $farm) }}" class="p-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-950/40 text-amber-600 hover:text-amber-700 transition" title="Editar">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('farms.destroy', $farm) }}" class="inline" onsubmit="return confirm('¿Confirmas que deseas eliminar esta granja solar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-500 hover:text-rose-700 transition" title="Eliminar">
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
        </x-table>

        @if($farms->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $farms->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
