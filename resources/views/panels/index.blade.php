@extends('layouts.app', ['title' => 'Catálogo de Paneles Solares'])

@section('content')
<div class="space-y-6">

    <!-- Encabezado y Acciones -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-sky-500/10 text-sky-700 dark:text-sky-400 font-bold text-xs">
                    Requerimiento RF-02
                </span>
                <span class="text-xs text-slate-400">Especificaciones Fotovoltaicas</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Catálogo de Paneles Solares
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Módulos fotovoltaicos autorizados para su instalación en las plantas solares del país.
            </p>
        </div>

        <div class="flex items-center gap-3">
            @can('manage-panels')
                <x-button href="{{ route('panels.create') }}" variant="primary" size="md">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
                    <span>Nuevo Panel Solar</span>
                </x-button>
            @endcan
        </div>
    </div>

    <!-- Tabla de Paneles Solares -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                <tr>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Marca y Fabricante</th>
                    <th class="px-6 py-4">Modelo Técnico</th>
                    <th class="px-6 py-4 text-right">Potencia Nominal (kW)</th>
                    <th class="px-6 py-4 text-right">Potencia en Watts</th>
                    <th class="px-6 py-4 text-center">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @forelse($panels as $panel)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                        <td class="px-6 py-4 font-mono text-slate-400">
                            #{{ $panel->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-sky-500/10 flex items-center justify-center text-sky-500 flex-shrink-0">
                                    <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                                </div>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $panel->brand }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-slate-600 dark:text-slate-300">
                            {{ $panel->model }}
                        </td>
                        <td class="px-6 py-4 text-right font-extrabold text-amber-600 dark:text-amber-400">
                            {{ number_format((float)$panel->nominal_power_kw, 3) }} kW
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-700 dark:text-slate-300">
                            {{ number_format((float)$panel->nominal_power_kw * 1000) }} W
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($panel->status === 'active')
                                <x-badge variant="success">Activo</x-badge>
                            @elseif($panel->status === 'maintenance')
                                <x-badge variant="warning">Mantenimiento</x-badge>
                            @else
                                <x-badge variant="neutral">Inactivo</x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                @can('manage-panels')
                                    <a href="{{ route('panels.edit', $panel) }}" class="p-1.5 rounded-lg hover:bg-amber-50 dark:hover:bg-amber-950/40 text-amber-600 hover:text-amber-700 transition" title="Editar">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('panels.destroy', $panel) }}" class="inline" onsubmit="return confirm('¿Confirmas que deseas eliminar este panel solar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 text-rose-500 hover:text-rose-700 transition" title="Eliminar">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-[11px]">Lectura</span>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <i data-lucide="grid" class="w-10 h-10 text-slate-300"></i>
                                <p class="text-sm font-medium">No se han registrado modelos de paneles solares aún.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        @if($panels->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $panels->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
