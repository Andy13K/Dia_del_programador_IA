@extends('layouts.app', ['title' => 'Acceso No Autorizado (403)'])

@section('content')
<div class="min-h-[60vh] flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center space-y-6 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
            <i data-lucide="shield-alert" class="w-8 h-8"></i>
        </div>
        <div class="space-y-2">
            <span class="text-xs font-mono font-bold uppercase tracking-widest text-rose-500">Error 403 • Prohibido</span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                Acceso Restringido por Rol
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Su usuario no cuenta con los privilegios o permisos necesarios bajo las políticas de control de acceso (RBAC) para realizar esta operación.
            </p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-md shadow-amber-500/20 flex items-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Volver al Dashboard</span>
            </a>
        </div>
    </div>
</div>
@endsection
