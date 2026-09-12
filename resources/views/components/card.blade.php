@props(['header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden']) }}>
    @if($header)
        <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800 font-semibold text-slate-800 dark:text-white flex items-center justify-between">
            {{ $header }}
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-3 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200/80 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400">
            {{ $footer }}
        </div>
    @endif
</div>
