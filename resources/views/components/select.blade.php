@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'error' => null,
    'required' => false,
    'hint' => null,
    'placeholder' => 'Seleccionar opción'
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
            {{ $label }}
            @if($required) <span class="text-rose-500">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <select 
            name="{{ $name }}" 
            id="{{ $name }}"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2.5 rounded-xl border bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all ' . 
                ($error ? 'border-rose-500 focus:border-rose-500 ring-rose-500/20' : 'border-slate-300 dark:border-slate-700 focus:border-amber-500')
            ]) }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $key => $val)
                @php
                    $isSelected = old($name, $selected) == $key;
                @endphp
                <option value="{{ $key }}" {{ $isSelected ? 'selected' : '' }}>
                    {{ $val }}
                </option>
            @endforeach
            {{ $slot }}
        </select>
    </div>

    @if($hint && !$error)
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 inline"></i>
            {{ $error }}
        </p>
    @endif
</div>
