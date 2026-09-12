<div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-slate-200/80 dark:divide-slate-800 text-left text-sm']) }}>
        {{ $slot }}
    </table>
</div>
