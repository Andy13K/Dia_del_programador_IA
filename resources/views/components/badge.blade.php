@props([
    'variant' => 'neutral', // success, warning, danger, solar, info, neutral
    'size' => 'sm'
])

@php
    $variants = [
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
        'solar' => 'bg-amber-500/10 text-amber-700 border-amber-400/30 dark:bg-amber-500/20 dark:text-amber-300',
        'info' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
        'neutral' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
    ];
    $cls = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 font-semibold px-2.5 py-0.5 rounded-full text-xs border {$cls}"]) }}>
    {{ $slot }}
</span>
