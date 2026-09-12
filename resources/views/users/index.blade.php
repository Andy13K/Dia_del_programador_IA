@extends('layouts.app', ['title' => 'Gestión de Usuarios'])

@section('content')
<div class="space-y-6">

    <!-- Encabezado y Acciones -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-bold text-xs">
                    Control de Acceso RBAC
                </span>
                <span class="text-xs text-slate-400">OWASP A01: Broken Access Control</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Administración de Usuarios y Roles
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Gestión de cuentas con permisos segmentados: Administrador, Operador y Visualizador.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-button href="{{ route('users.create') }}" variant="primary" size="md">
                <i data-lucide="user-plus" class="w-4 h-4 mr-1.5"></i>
                <span>Nuevo Usuario</span>
            </x-button>
        </div>
    </div>

    <!-- KPIs Rápidos de Usuarios -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Cuentas</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $stats['total'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider block">Administradores</span>
                <span class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-0.5 block">{{ $stats['admins'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Operadores</span>
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-0.5 block">{{ $stats['operadores'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                <i data-lucide="wrench" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider block">Visualizadores</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block">{{ $stats['visualizadores'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <i data-lucide="eye" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Barra de Búsqueda y Filtros -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Buscar por Nombre o Correo</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o correo del usuario..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Filtrar por Rol</label>
                <div class="flex items-center gap-2">
                    <select name="role" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Todos los roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="operador" {{ request('role') === 'operador' ? 'selected' : '' }}>Operador</option>
                        <option value="visualizador" {{ request('role') === 'visualizador' ? 'selected' : '' }}>Visualizador</option>
                    </select>
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition active:scale-95 flex items-center gap-1 flex-shrink-0">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Filtrar</span>
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('users.index') }}" class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-rose-500 text-xs transition flex-shrink-0" title="Limpiar filtros">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- VISTA MÓVIL: Tarjetas de Usuarios sin Scroll Horizontal (md:hidden) -->
    <div class="md:hidden space-y-3">
        @forelse($users as $user)
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr {{ $user->role === 'admin' ? 'from-rose-500 to-rose-600' : ($user->role === 'operador' ? 'from-amber-500 to-amber-600' : 'from-emerald-500 to-emerald-600') }} flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white truncate">
                                {{ $user->name }}
                            </h4>
                            <span class="text-xs text-slate-500 dark:text-slate-400 block truncate">
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>

                    <div>
                        @if($user->role === 'admin')
                            <x-badge variant="danger">Admin</x-badge>
                        @elseif($user->role === 'operador')
                            <x-badge variant="warning">Operador</x-badge>
                        @else
                            <x-badge variant="success">Visualizador</x-badge>
                        @endif
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span>Registrado: {{ $user->created_at?->format('d/m/Y') ?? 'N/A' }}</span>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('users.edit', $user) }}" class="p-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-amber-500 hover:border-amber-400 transition" title="Editar">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                        </a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Confirma que desea eliminar este usuario?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-rose-200 dark:border-rose-900/50 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 text-center">
                <i data-lucide="user-x" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                <p class="text-xs text-slate-500">No se encontraron usuarios registrados con los filtros aplicados.</p>
            </div>
        @endforelse
    </div>

    <!-- VISTA ESCRITORIO: Tabla Completa (hidden md:block) -->
    <div class="hidden md:block bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-6">
        <x-table>
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-[11px] uppercase font-extrabold text-slate-400 tracking-wider">
                <tr>
                    <th class="px-5 py-4 text-center whitespace-nowrap">ID</th>
                    <th class="px-5 py-4 text-left whitespace-nowrap">Usuario</th>
                    <th class="px-5 py-4 text-left whitespace-nowrap">Correo Electrónico</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Rol de Acceso</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Fecha de Registro</th>
                    <th class="px-5 py-4 text-center whitespace-nowrap">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="px-5 py-3.5 text-center font-mono text-slate-400">
                            #{{ $user->id }}
                        </td>
                        <td class="px-5 py-3.5 text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr {{ $user->role === 'admin' ? 'from-rose-500 to-rose-600' : ($user->role === 'operador' ? 'from-amber-500 to-amber-600' : 'from-emerald-500 to-emerald-600') }} flex items-center justify-center text-white font-bold text-xs shadow-sm flex-shrink-0">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div>
                                    <strong class="text-slate-900 dark:text-white block text-[13px] whitespace-nowrap">{{ $user->name }}</strong>
                                    @if($user->id === auth()->id())
                                        <span class="text-[10px] text-amber-500 font-bold">(Tu cuenta actual)</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-left text-slate-600 dark:text-slate-300 font-mono text-xs whitespace-nowrap">
                            {{ $user->email }}
                        </td>
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            @if($user->role === 'admin')
                                <x-badge variant="danger">Administrador</x-badge>
                            @elseif($user->role === 'operador')
                                <x-badge variant="warning">Operador</x-badge>
                            @else
                                <x-badge variant="success">Visualizador</x-badge>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $user->created_at?->format('d/m/Y H:i') ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('users.edit', $user) }}" 
                                   class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:border-amber-400 hover:text-amber-500 transition shadow-sm hover:scale-105 active:scale-95" 
                                   title="Editar Usuario">
                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                </a>

                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Seguro que deseas eliminar al usuario {{ $user->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-white dark:bg-slate-800 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition shadow-sm hover:scale-105 active:scale-95" 
                                                title="Eliminar Usuario">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            No hay usuarios registrados que coincidan con la búsqueda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
