@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => 'activity',
    'variant' => 'solar', // solar, eco, alert, sky, indigo
    'trend' => null
])

@php
    $variants = [
        'solar' => [
            'bg' => 'bg-amber-500/10 text-amber-500 dark:bg-amber-500/15',
            'border' => 'hover:border-amber-500/40',
            'icon' => 'text-amber-500',
            'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
        ],
        'eco' => [
            'bg' => 'bg-emerald-500/10 text-emerald-500 dark:bg-emerald-500/15',
            'border' => 'hover:border-emerald-500/40',
            'icon' => 'text-emerald-500',
            'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
        ],
        'alert' => [
            'bg' => 'bg-rose-500/10 text-rose-500 dark:bg-rose-500/15',
            'border' => 'hover:border-rose-500/40',
            'icon' => 'text-rose-500',
            'badge' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400'
        ],
        'sky' => [
            'bg' => 'bg-sky-500/10 text-sky-500 dark:bg-sky-500/15',
            'border' => 'hover:border-sky-500/40',
            'icon' => 'text-sky-500',
            'badge' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400'
        ],
        'indigo' => [
            'bg' => 'bg-indigo-500/10 text-indigo-500 dark:bg-indigo-500/15',
            'border' => 'hover:border-indigo-500/40',
            'icon' => 'text-indigo-500',
            'badge' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
        ],
    ];
    $v = $variants[$variant] ?? $variants['solar'];
@endphp

<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-200 {{ $v['border'] }} relative overflow-hidden group">
    <div class="flex items-start justify-between">
        <div>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $title }}
            </span>
            <div class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                {{ $value }}
            </div>
            @if($subtitle)
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5 font-medium">
                    {{ $subtitle }}
                </div>
            @endif
        </div>
        <div class="w-12 h-12 rounded-2xl {{ $v['bg'] }} flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
            <i data-lucide="{{ $icon }}" class="w-6 h-6 {{ $v['icon'] }}"></i>
        </div>
    </div>
    @if($trend)
        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs">
            <span class="text-slate-400">Estado</span>
            <span class="font-semibold {{ $v['badge'] }} px-2 py-0.5 rounded-full">{{ $trend }}</span>
        </div>
    @endif
</div>
