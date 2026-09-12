@props([
    'variant' => 'primary', // primary, secondary, danger, success, outline
    'size' => 'md',        // sm, md, lg
    'type' => 'button',
    'href' => null,
    'icon' => null
])

@php
    $base = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none shadow-sm cursor-pointer';
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-4 py-2 text-sm gap-2',
        'lg' => 'px-6 py-3 text-base gap-2.5',
    ];

    $variants = [
        'primary' => 'bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold focus:ring-amber-500 shadow-amber-500/20',
        'secondary' => 'bg-slate-800 hover:bg-slate-700 text-white focus:ring-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white focus:ring-emerald-500 shadow-emerald-600/20',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 shadow-rose-600/20',
        'outline' => 'bg-transparent border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <i data-lucide="{{ $icon }}" class="w-4 h-4"></i> @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <i data-lucide="{{ $icon }}" class="w-4 h-4"></i> @endif
        {{ $slot }}
    </button>
@endif
