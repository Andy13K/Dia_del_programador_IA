@extends('layouts.app', ['title' => 'Nueva Medición de Generación'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('generations.index') }}" class="hover:text-amber-500 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Volver a Mediciones</span>
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Registrar Medición Mensual de Generación
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Inyección de lecturas fotovoltaicas, cálculo normativo de CO₂ y disparo automático de alertas (RF-08 a RF-10, RF-14).
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300">
            <div class="font-bold flex items-center gap-2 mb-1">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>Errores en la captura:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('generations.store') }}" class="space-y-6">
        @csrf

        <x-card>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Granja Solar Seleccionada *
                    </label>
                    <select name="solar_farm_id" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                        <option value="">Seleccione la planta solar...</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ (old('solar_farm_id', request('farm_id')) == $farm->id) ? 'selected' : '' }}>
                                {{ $farm->name }} ({{ $farm->department->name ?? 'Guatemala' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Período Mensual (YYYY-MM) *
                        </label>
                        <input type="text" name="period" value="{{ old('period', date('Y-m')) }}" required pattern="\d{4}-\d{2}" placeholder="2026-08"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 font-mono">
                        <span class="text-[11px] text-slate-400 mt-1 block">Formato estricto YYYY-MM</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Fecha de Toma de Lectura *
                        </label>
                        <input type="date" name="record_date" value="{{ old('record_date', date('Y-m-d')) }}" required
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Generación Esperada / Estimada (kWh) *
                        </label>
                        <input type="number" step="0.01" name="estimated_kwh" id="estKwh" value="{{ old('estimated_kwh', '150000.00') }}" required min="0" oninput="recalc()"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Generación Real Producida (kWh) *
                        </label>
                        <input type="number" step="0.01" name="real_kwh" id="realKwh" value="{{ old('real_kwh', '145000.00') }}" required min="0" oninput="recalc()"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 font-mono">
                    </div>
                </div>

                <!-- Panel de Cálculo en Tiempo Real y Alerta -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700/80 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-semibold">CO₂ Evitado (Factor: {{ number_format((float) config('solar.co2_kg_per_kwh'), 2) }} kg / kWh):<x-co2-info /></span>
                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm font-mono" id="previewCo2">
                            58,000.00 kg (58.00 Ton)
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-700 pt-2">
                        <span class="text-slate-500 font-semibold">Evaluación de Desempeño:</span>
                        <span id="previewAlertStatus" class="font-bold text-emerald-600 dark:text-emerald-400">
                            Producción Óptima (&gt;80% de esperada)
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Observaciones Técnicas (Opcional)
                    </label>
                    <textarea name="notes" rows="3" maxlength="500" placeholder="Condiciones climáticas, mantenimiento preventivo de inversores o incidencias en la red..."
                              class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('generations.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Guardar Medición</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function recalc() {
        const est = parseFloat(document.getElementById('estKwh').value) || 0;
        const real = parseFloat(document.getElementById('realKwh').value) || 0;

        const co2Kg = real * 0.40;
        const co2Ton = co2Kg / 1000;
        document.getElementById('previewCo2').innerText = `${co2Kg.toLocaleString('es-GT', {minimumFractionDigits: 2, maximumFractionDigits: 2})} kg (${co2Ton.toLocaleString('es-GT', {minimumFractionDigits: 2, maximumFractionDigits: 2})} Ton)`;

        const alertEl = document.getElementById('previewAlertStatus');
        if (est > 0 && real <= (est * 0.80)) {
            const defPct = (((est - real) / est) * 100).toFixed(1);
            alertEl.className = 'font-black text-rose-600 dark:text-rose-400 flex items-center gap-1';
            alertEl.innerHTML = `<svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> ¡ALERTA! Déficit del ${defPct}% (Supera el umbral del 20%)`;
        } else {
            alertEl.className = 'font-bold text-emerald-600 dark:text-emerald-400';
            alertEl.innerText = 'Desempeño Aceptable (Sin alerta)';
        }
    }
    recalc();
</script>
@endpush
@endsection
