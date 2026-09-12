@extends('layouts.app', ['title' => 'Nuevo Panel Solar'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('panels.index') }}" class="hover:text-amber-500 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Volver al Catálogo</span>
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Registrar Nuevo Módulo Fotovoltaico
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Ingrese las especificaciones técnicas del panel solar para el catálogo nacional (RF-02).
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300">
            <div class="font-bold flex items-center gap-2 mb-1">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>Errores en el formulario:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('panels.store') }}" class="space-y-6">
        @csrf

        <x-card>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Marca / Fabricante *
                    </label>
                    <input type="text" name="brand" value="{{ old('brand') }}" required maxlength="100"
                           placeholder="Ej. Canadian Solar, Jinko Solar, LONGi Solar"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Modelo Técnico *
                    </label>
                    <input type="text" name="model" value="{{ old('model') }}" required maxlength="100"
                           placeholder="Ej. HiKu6 Mono PERC CS6W-550MS"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Potencia Nominal en kW *
                        </label>
                        <input type="number" step="0.001" name="nominal_power_kw" id="kwInput" value="{{ old('nominal_power_kw', '0.550') }}" required min="0.001" max="100"
                               oninput="updateWatts()"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 font-mono">
                        <span class="text-[11px] text-slate-400 mt-1 block">Equivalente a <strong id="wattsPreview" class="text-amber-500 font-bold">550</strong> Watts</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Estado *
                        </label>
                        <select name="status" required
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('panels.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Registrar Panel</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function updateWatts() {
        const val = parseFloat(document.getElementById('kwInput').value) || 0;
        document.getElementById('wattsPreview').innerText = (val * 1000).toLocaleString();
    }
</script>
@endpush
@endsection
