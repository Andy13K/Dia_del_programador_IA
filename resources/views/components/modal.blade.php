@props([
    'name',
    'title' => null,
    'size' => 'md' // sm, md, lg, xl
])

@php
    $maxWidths = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
    $mw = $maxWidths[$size] ?? 'max-w-lg';
@endphp

<div id="{{ $name }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full {{ $mw }} bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        
        @if($title)
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
                <button type="button" onclick="document.getElementById('{{ $name }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        @endif

        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
