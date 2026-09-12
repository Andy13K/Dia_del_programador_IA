@extends('layouts.app', ['title' => 'Nueva Granja Solar'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('farms.index') }}" class="hover:text-amber-500 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Volver a Granjas Solares</span>
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Registrar Nueva Granja Solar
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Ingrese los datos técnicos, coordenadas GPS y asignación de paneles fotovoltaicos.
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300">
            <div class="font-bold flex items-center gap-2 mb-1">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>Por favor corrija los siguientes errores:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('farms.store') }}" class="space-y-6">
        @csrf

        <!-- Datos Básicos -->
        <x-card>
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-amber-500"></i>
                    <span>Información General de la Planta</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Nombre de la Granja Solar *
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="150"
                           placeholder="Ej. Parque Solar Guayacán Petén"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Departamento (Guatemala) *
                    </label>
                    <select id="deptSelect" name="department_id" required onchange="updateDeptCoords()"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">Seleccione un departamento...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" 
                                    data-lat="{{ $dept->latitude }}" 
                                    data-lng="{{ $dept->longitude }}"
                                    {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Estado Operativo *
                    </label>
                    <select name="status" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Activa</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Latitud GPS (13.00 a 18.50) *
                    </label>
                    <input type="number" step="0.0000001" name="latitude" id="latInput" value="{{ old('latitude', '14.6349') }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Longitud GPS (-93.00 a -87.50) *
                    </label>
                    <input type="number" step="0.0000001" name="longitude" id="lngInput" value="{{ old('longitude', '-90.5069') }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Familias Beneficiadas *
                    </label>
                    <input type="number" name="benefited_families" value="{{ old('benefited_families', 1000) }}" required min="0"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Número estimado de hogares abastecidos por la potencia generada.</span>
                </div>
            </div>
        </x-card>

        <!-- Asignación de Paneles Solares -->
        <x-card>
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3 mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="grid" class="w-4 h-4 text-sky-500"></i>
                        <span>Módulos Fotovoltaicos Instalados (RF-05 / RF-06)</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Asigne el tipo de panel y la cantidad para calcular la potencia nominal en kW.</p>
                </div>
                <button type="button" onclick="addPanelRow()" class="px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 text-xs font-bold hover:bg-sky-100 transition border border-sky-200 dark:border-sky-800 flex items-center gap-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Agregar Panel</span>
                </button>
            </div>

            <div id="panelsContainer" class="space-y-3">
                <div class="panel-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                    <div class="sm:col-span-7">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Modelo de Panel Solar *</label>
                        <select name="panels[0][panel_id]" class="panel-select w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                            <option value="">Seleccione panel fotovoltaico...</option>
                            @foreach($panels as $panel)
                                <option value="{{ $panel->id }}" data-kw="{{ $panel->nominal_power_kw }}">
                                    {{ $panel->brand }} {{ $panel->model }} ({{ number_format((float)$panel->nominal_power_kw, 3) }} kW / {{ (float)$panel->nominal_power_kw * 1000 }}W)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Cantidad de Unidades *</label>
                        <input type="number" name="panels[0][quantity]" value="500" min="1" class="panel-qty w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="sm:col-span-1 flex justify-center pb-1">
                        <button type="button" onclick="removePanelRow(this)" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg transition" title="Quitar">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('farms.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Guardar Granja Solar</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    let panelIndex = 1;

    function updateDeptCoords() {
        const select = document.getElementById('deptSelect');
        const selectedOption = select.options[select.selectedIndex];
        const lat = selectedOption.getAttribute('data-lat');
        const lng = selectedOption.getAttribute('data-lng');
        if (lat && lng) {
            document.getElementById('latInput').value = parseFloat(lat).toFixed(6);
            document.getElementById('lngInput').value = parseFloat(lng).toFixed(6);
        }
    }

    function addPanelRow() {
        const container = document.getElementById('panelsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'panel-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60';
        
        let optionsHtml = '<option value="">Seleccione panel fotovoltaico...</option>';
        @foreach($panels as $panel)
            optionsHtml += `<option value="{{ $panel->id }}" data-kw="{{ $panel->nominal_power_kw }}">{{ $panel->brand }} {{ $panel->model }} ({{ number_format((float)$panel->nominal_power_kw, 3) }} kW)</option>`;
        @endforeach

        newRow.innerHTML = `
            <div class="sm:col-span-7">
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Modelo de Panel Solar *</label>
                <select name="panels[${panelIndex}][panel_id]" class="panel-select w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                    ${optionsHtml}
                </select>
            </div>
            <div class="sm:col-span-4">
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Cantidad de Unidades *</label>
                <input type="number" name="panels[${panelIndex}][quantity]" value="500" min="1" class="panel-qty w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
            </div>
            <div class="sm:col-span-1 flex justify-center pb-1">
                <button type="button" onclick="removePanelRow(this)" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg transition" title="Quitar">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        panelIndex++;
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function removePanelRow(btn) {
        const rows = document.querySelectorAll('.panel-row');
        if (rows.length > 1) {
            btn.closest('.panel-row').remove();
        } else {
            alert('Debe conservar al menos una asignación de paneles para la planta.');
        }
    }
</script>
@endpush
@endsection
