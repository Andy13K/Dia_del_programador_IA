@extends('layouts.app', ['title' => 'Recurso No Encontrado (404)'])

@section('content')
<div class="min-h-[60vh] flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center space-y-6 bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto">
            <i data-lucide="map-pin-off" class="w-8 h-8"></i>
        </div>
        <div class="space-y-2">
            <span class="text-xs font-mono font-bold uppercase tracking-widest text-amber-500">Error 404 • No Encontrado</span>
            <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                Página o Activo Inexistente
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                La ruta o el recurso solicitado no existe o fue reubicado dentro del Sistema de Monitoreo Solar de Guatemala.
            </p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-md shadow-amber-500/20 flex items-center gap-2">
                <i data-lucide="home" class="w-4 h-4"></i>
                <span>Volver al Dashboard</span>
            </a>
            <a href="{{ route('map.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition flex items-center gap-2">
                <i data-lucide="map" class="w-4 h-4"></i>
                <span>Explorar Mapa</span>
            </a>
        </div>
    </div>
</div>
@endsection
