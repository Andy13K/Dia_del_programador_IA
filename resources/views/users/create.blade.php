@extends('layouts.app', ['title' => 'Nuevo Usuario'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('users.index') }}" class="hover:text-amber-500 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Volver a Usuarios</span>
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Registrar Nuevo Usuario
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Cree una cuenta asignando el rol de privilegios adecuado según la matriz RBAC.
            </p>
        </div>
    </div>

    <x-card>
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf

            <!-- Nombre Completo -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Nombre Completo <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255"
                       placeholder="Ej. Ing. Carlos Mendoza"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('name') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('name')
                    <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Correo Electrónico <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255"
                       placeholder="usuario@dominio.gob.gt"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('email') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('email')
                    <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Rol de Acceso (RBAC) -->
            <div>
                <label for="role" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Rol en el Sistema <span class="text-rose-500">*</span>
                </label>
                <select id="role" name="role" required
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('role') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Seleccione un rol...</option>
                    <option value="operador" {{ old('role') === 'operador' ? 'selected' : '' }}>Operador (Gestión de granjas, mediciones y alertas)</option>
                    <option value="visualizador" {{ old('role') === 'visualizador' ? 'selected' : '' }}>Visualizador (Solo consulta técnica y reportes)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador (Control total del sistema y usuarios)</option>
                </select>
                @error('role')
                    <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contraseña -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required minlength="8"
                           placeholder="Mínimo 8 caracteres"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('password') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @error('password')
                        <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Confirmar Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           placeholder="Repita la contraseña"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Matriz Informativa de Privilegios -->
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 text-[11px] text-slate-600 dark:text-slate-400 space-y-1">
                <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                    <span>Seguridad OWASP A01 — Matriz de Roles:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[10px]">
                    <li><strong>Administrador:</strong> Acceso irrestricto, auditoría y control de usuarios.</li>
                    <li><strong>Operador:</strong> Registra y edita granjas, mediciones y atiende alertas técnicas.</li>
                    <li><strong>Visualizador:</strong> Consulta mapa interactivo, gráficos de dashboard y reportes.</li>
                </ul>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <x-button href="{{ route('users.index') }}" variant="secondary" size="md">
                    Cancelar
                </x-button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-md shadow-amber-500/20 active:scale-95 flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Guardar Usuario</span>
                </button>
            </div>
        </form>
    </x-card>

</div>
@endsection
