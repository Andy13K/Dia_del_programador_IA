@extends('layouts.app', ['title' => 'Editar Usuario'])

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
                Editar Usuario #{{ $user->id }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Actualice los datos personales, rol o credenciales del usuario.
            </p>
        </div>

        <div>
            @if($user->role === 'admin')
                <x-badge variant="danger" size="md">Administrador</x-badge>
            @elseif($user->role === 'operador')
                <x-badge variant="warning" size="md">Operador</x-badge>
            @else
                <x-badge variant="success" size="md">Visualizador</x-badge>
            @endif
        </div>
    </div>

    <x-card>
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nombre Completo -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    Nombre Completo <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"
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
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255"
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
                    <option value="operador" {{ old('role', $user->role) === 'operador' ? 'selected' : '' }}>Operador (Gestión de granjas, mediciones y alertas)</option>
                    <option value="visualizador" {{ old('role', $user->role) === 'visualizador' ? 'selected' : '' }}>Visualizador (Solo consulta técnica y reportes)</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador (Control total del sistema y usuarios)</option>
                </select>
                @if($user->id === auth()->id())
                    <span class="text-[10px] text-amber-600 dark:text-amber-400 block mt-1">
                        Nota: Es tu propia cuenta. Si revocas tu rol de administrador perderás acceso a esta sección.
                    </span>
                @endif
                @error('role')
                    <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Cambio opcional de contraseña -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">Cambiar Contraseña (Opcional)</span>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-3">
                    Deje estos campos en blanco si no desea modificar la contraseña actual del usuario.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Nueva Contraseña
                        </label>
                        <input type="password" id="password" name="password" minlength="8"
                               placeholder="Mínimo 8 caracteres"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('password') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-slate-300 dark:border-slate-700' }} bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('password')
                            <span class="text-[11px] text-rose-500 block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Confirmar Nueva Contraseña
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                               placeholder="Repita la nueva contraseña"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <x-button href="{{ route('users.index') }}" variant="secondary" size="md">
                    Cancelar
                </x-button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-md shadow-amber-500/20 active:scale-95 flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Actualizar Usuario</span>
                </button>
            </div>
        </form>
    </x-card>

</div>
@endsection
