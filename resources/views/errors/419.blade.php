@extends('layouts.app', ['title' => 'Página Expirada (419)'])

@section('content')
<div class="min-h-[60vh] flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center space-y-6 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-sky-500/10 text-sky-500 flex items-center justify-center mx-auto">
            <i data-lucide="clock" class="w-8 h-8"></i>
        </div>
        <div class="space-y-2">
            <span class="text-xs font-mono font-bold uppercase tracking-widest text-sky-500">Error 419 • Token Expirado</span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                Sesión o Token CSRF Caducado
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Por motivos de seguridad contra ataques de falsificación de peticiones en sitios cruzados (OWASP A01: CSRF), el token del formulario ha expirado tras un período de inactividad.
            </p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <button type="button" onclick="window.location.reload()" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-md shadow-amber-500/20 flex items-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Recargar Página</span>
            </button>
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition flex items-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Iniciar Sesión</span>
            </a>
        </div>
    </div>
</div>
@endsection
