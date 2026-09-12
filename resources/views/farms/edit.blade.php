@extends('layouts.app', ['title' => 'Editar Granja Solar'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('farms.show', $farm) }}" class="hover:text-amber-500 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Volver a Ficha de Granja</span>
                </a>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">
                Editar Granja Solar: {{ $farm->name }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Actualice los datos técnicos, coordenadas o asignación de paneles solares.
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

    <form method="POST" action="{{ route('farms.update', $farm) }}" class="space-y-6">
        @csrf
        @method('PUT')

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
                    <input type="text" name="name" value="{{ old('name', $farm->name) }}" required maxlength="150"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Departamento (Guatemala) *
                    </label>
                    <select id="deptSelect" name="department_id" required onchange="updateDeptCoords()"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" 
                                    data-lat="{{ $dept->latitude }}" 
                                    data-lng="{{ $dept->longitude }}"
                                    {{ old('department_id', $farm->department_id) == $dept->id ? 'selected' : '' }}>
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
                        <option value="active" {{ old('status', $farm->status) === 'active' ? 'selected' : '' }}>Activa</option>
                        <option value="maintenance" {{ old('status', $farm->status) === 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                        <option value="inactive" {{ old('status', $farm->status) === 'inactive' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Latitud GPS (13.00 a 18.50) *
                    </label>
                    <input type="number" step="0.0000001" name="latitude" id="latInput" value="{{ old('latitude', $farm->latitude) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Longitud GPS (-93.00 a -87.50) *
                    </label>
                    <input type="number" step="0.0000001" name="longitude" id="lngInput" value="{{ old('longitude', $farm->longitude) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <!-- Selector Interactivo de Ubicación en el Mapa -->
                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                            <i data-lucide="crosshair" class="w-3.5 h-3.5 text-amber-500"></i>
                            <span>Fijar Ubicación en el Mapa (Clic o arrastre el marcador)</span>
                        </label>
                        <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400">
                            Coordenadas automáticas
                        </span>
                    </div>
                    <div class="relative w-full h-64 sm:h-72 rounded-2xl overflow-hidden border border-slate-300 dark:border-slate-700 shadow-inner bg-slate-900">
                        <div id="farmPickerMap" class="w-full h-full z-10"></div>
                        <div class="absolute bottom-2 left-2 z-20 bg-slate-900/90 backdrop-blur-md px-2.5 py-1 rounded-lg border border-slate-800 text-[10px] text-slate-300 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                            <span id="mapCoordsDisplay">{{ number_format((float)$farm->latitude, 7) }}, {{ number_format((float)$farm->longitude, 7) }}</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">
                        Haga clic en el mapa para ajustar la ubicación exacta y autocompletar la latitud y longitud. También puede escribirlas manualmente o elegir otro departamento arriba para centrar el mapa.
                    </p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Familias Beneficiadas *
                    </label>
                    <input type="number" name="benefited_families" value="{{ old('benefited_families', $farm->benefited_families) }}" required min="0"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>
        </x-card>

        <!-- Asignación de Paneles Solares -->
        <x-card>
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3 mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="grid" class="w-4 h-4 text-sky-500"></i>
                        <span>Módulos Fotovoltaicos Asignados</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Ajuste la cantidad de paneles para recalcular la potencia total instalada.</p>
                </div>
                <button type="button" onclick="addPanelRow()" class="px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 text-xs font-bold hover:bg-sky-100 transition border border-sky-200 dark:border-sky-800 flex items-center gap-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Agregar Panel</span>
                </button>
            </div>

            <div id="panelsContainer" class="space-y-3">
                @forelse($farm->solarPanels as $idx => $assignedPanel)
                    <div class="panel-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                        <div class="sm:col-span-7">
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Modelo de Panel Solar *</label>
                            <select name="panels[{{ $idx }}][panel_id]" class="panel-select w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                                @foreach($panels as $panel)
                                    <option value="{{ $panel->id }}" {{ $panel->id == $assignedPanel->id ? 'selected' : '' }}>
                                        {{ $panel->brand }} {{ $panel->model }} ({{ number_format((float)$panel->nominal_power_kw, 3) }} kW / {{ (float)$panel->nominal_power_kw * 1000 }}W)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Cantidad de Unidades *</label>
                            <input type="number" name="panels[{{ $idx }}][quantity]" value="{{ $assignedPanel->pivot->quantity ?? 100 }}" min="1" class="panel-qty w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="sm:col-span-1 flex justify-center pb-1">
                            <button type="button" onclick="removePanelRow(this)" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg transition" title="Quitar">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="panel-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                        <div class="sm:col-span-7">
                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">Modelo de Panel Solar *</label>
                            <select name="panels[0][panel_id]" class="panel-select w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                                <option value="">Seleccione panel fotovoltaico...</option>
                                @foreach($panels as $panel)
                                    <option value="{{ $panel->id }}">
                                        {{ $panel->brand }} {{ $panel->model }} ({{ number_format((float)$panel->nominal_power_kw, 3) }} kW)
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
                @endforelse
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('farms.show', $farm) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                <span>Actualizar Granja Solar</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    let panelIndex = {{ count($farm->solarPanels) > 0 ? count($farm->solarPanels) : 1 }};
    let pickerMap, pickerMarker;

    // OWASP A03: los datos de panel (marca/modelo, texto libre del operador) se pasan como
    // JSON real vía el directivo Blade de JS, nunca interpolados con el escape normal de Blade
    // dentro de un string de JS — ese escape solo protege contexto HTML y permite romper el
    // string con comilla invertida o llaves de interpolación.
    const panelsData = @js($panels->map(fn ($panel) => [
        'id' => $panel->id,
        'kw' => (float) $panel->nominal_power_kw,
        'label' => $panel->brand.' '.$panel->model.' ('.number_format((float) $panel->nominal_power_kw, 3).' kW)',
    ])->values());
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[character]);

    function buildPanelOptionsHtml() {
        let html = '<option value="">Seleccione panel fotovoltaico...</option>';
        panelsData.forEach(panel => {
            html += `<option value="${escapeHtml(panel.id)}" data-kw="${escapeHtml(panel.kw)}">${escapeHtml(panel.label)}</option>`;
        });
        return html;
    }

    function initPickerMap() {
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');
        const coordsDisplay = document.getElementById('mapCoordsDisplay');

        let initialLat = parseFloat(latInput.value) || 14.6349;
        let initialLng = parseFloat(lngInput.value) || -90.5069;

        pickerMap = L.map('farmPickerMap', {
            zoomSnap: 0.5
        }).setView([initialLat, initialLng], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>'
        }).addTo(pickerMap);

        const customPin = L.divIcon({
            className: 'picker-pin',
            html: `
                <div class="relative w-8 h-8 flex items-center justify-center">
                    <div class="w-8 h-8 rounded-full bg-amber-500 text-white shadow-xl border-2 border-white dark:border-slate-900 flex items-center justify-center hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                    </div>
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        pickerMarker = L.marker([initialLat, initialLng], {
            icon: customPin,
            draggable: true
        }).addTo(pickerMap);

        function updateCoords(lat, lng) {
            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);
            if (coordsDisplay) {
                coordsDisplay.textContent = `${lat.toFixed(7)}, ${lng.toFixed(7)}`;
            }
        }

        pickerMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        pickerMap.on('click', function(e) {
            pickerMarker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });

        function onManualInput() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng) && lat >= 13 && lat <= 19 && lng >= -94 && lng <= -87) {
                pickerMarker.setLatLng([lat, lng]);
                pickerMap.panTo([lat, lng]);
                if (coordsDisplay) {
                    coordsDisplay.textContent = `${lat.toFixed(7)}, ${lng.toFixed(7)}`;
                }
            }
        }

        latInput.addEventListener('input', onManualInput);
        lngInput.addEventListener('input', onManualInput);

        updateCoords(initialLat, initialLng);
    }

    if (typeof L !== 'undefined') {
        initPickerMap();
    } else {
        window.addEventListener('load', initPickerMap);
    }

    function updateDeptCoords() {
        const select = document.getElementById('deptSelect');
        const selectedOption = select.options[select.selectedIndex];
        const lat = selectedOption.getAttribute('data-lat');
        const lng = selectedOption.getAttribute('data-lng');
        if (lat && lng) {
            const fLat = parseFloat(lat);
            const fLng = parseFloat(lng);
            document.getElementById('latInput').value = fLat.toFixed(7);
            document.getElementById('lngInput').value = fLng.toFixed(7);

            if (pickerMarker && pickerMap) {
                pickerMarker.setLatLng([fLat, fLng]);
                pickerMap.setView([fLat, fLng], 11);
                const coordsDisplay = document.getElementById('mapCoordsDisplay');
                if (coordsDisplay) {
                    coordsDisplay.textContent = `${fLat.toFixed(7)}, ${fLng.toFixed(7)}`;
                }
            }
        }
    }

    function addPanelRow() {
        const container = document.getElementById('panelsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'panel-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-end p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60';

        const optionsHtml = buildPanelOptionsHtml();

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
