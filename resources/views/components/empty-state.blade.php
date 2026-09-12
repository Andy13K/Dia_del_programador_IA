@props([
    'title' => 'No hay registros',
    'message' => 'No se encontraron datos para mostrar en esta sección.',
    'icon' => 'inbox',
    'actionLabel' => null,
    'actionUrl' => null,
    'actionIcon' => 'plus'
])

<div class="text-center py-12 px-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/40">
    <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center mb-4">
        <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
    </div>
    <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1">{{ $title }}</h3>
    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-6">{{ $message }}</p>
    @if($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm rounded-xl transition shadow-sm">
            <i data-lucide="{{ $actionIcon }}" class="w-4 h-4"></i>
            <span>{{ $actionLabel }}</span>
        </a>
    @endif
</div>
