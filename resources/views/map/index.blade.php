@extends('layouts.app', ['title' => 'Mapa Interactivo de Guatemala'])

@push('styles')
<style>
    #guatemalaMap .leaflet-overlay-pane svg,
    #guatemalaMap .leaflet-container svg,
    #guatemalaMap svg {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Encabezado y Filtros Rápidos -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Requerimiento Obligatorio RF-13</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">
                Distribución Geográfica de Granjas Solares
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Visualización satelital y cartográfica en los 22 departamentos de la República de Guatemala.
            </p>
        </div>

        <!-- Controles / Filtros -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <select id="departmentFilter" onchange="filterFarms()" class="pl-3 pr-8 py-2 text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="all">Todos los Departamentos (22)</option>
                    @if(isset($departments) && $departments->count() > 0)
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" data-name="{{ $dept->name }}" data-lat="{{ $dept->latitude }}" data-lng="{{ $dept->longitude }}">{{ $dept->name }}</option>
                        @endforeach
                    @else
                        <option value="1" data-name="Guatemala" data-lat="14.6349" data-lng="-90.5069">Guatemala</option>
                        <option value="2" data-name="Quetzaltenango" data-lat="14.8347" data-lng="-91.5181">Quetzaltenango</option>
                        <option value="3" data-name="Escuintla" data-lat="14.3009" data-lng="-90.7850">Escuintla</option>
                        <option value="4" data-name="Izabal" data-lat="15.7278" data-lng="-88.5944">Izabal</option>
                        <option value="5" data-name="Petén" data-lat="16.9200" data-lng="-89.8900">Petén</option>
                        <option value="6" data-name="Alta Verapaz" data-lat="15.4700" data-lng="-90.3700">Alta Verapaz</option>
                        <option value="7" data-name="Baja Verapaz" data-lat="15.1000" data-lng="-90.3167">Baja Verapaz</option>
                        <option value="8" data-name="Chimaltenango" data-lat="14.6611" data-lng="-90.8194">Chimaltenango</option>
                        <option value="9" data-name="Chiquimula" data-lat="14.7978" data-lng="-89.5439">Chiquimula</option>
                        <option value="10" data-name="El Progreso" data-lat="14.8653" data-lng="-90.0764">El Progreso</option>
                        <option value="11" data-name="Huehuetenango" data-lat="15.3197" data-lng="-91.4708">Huehuetenango</option>
                        <option value="12" data-name="Jalapa" data-lat="14.6347" data-lng="-89.9889">Jalapa</option>
                        <option value="13" data-name="Jutiapa" data-lat="14.2817" data-lng="-89.8958">Jutiapa</option>
                        <option value="14" data-name="Retalhuleu" data-lat="14.5361" data-lng="-91.6778">Retalhuleu</option>
                        <option value="15" data-name="Sacatepéquez" data-lat="14.5586" data-lng="-90.7339">Sacatepéquez</option>
                        <option value="16" data-name="San Marcos" data-lat="14.9639" data-lng="-91.7944">San Marcos</option>
                        <option value="17" data-name="Santa Rosa" data-lat="14.2783" data-lng="-90.2989">Santa Rosa</option>
                        <option value="18" data-name="Sololá" data-lat="14.7722" data-lng="-91.1833">Sololá</option>
                        <option value="19" data-name="Suchitepéquez" data-lat="14.5342" data-lng="-91.5033">Suchitepéquez</option>
                        <option value="20" data-name="Totonicapán" data-lat="14.9117" data-lng="-91.3611">Totonicapán</option>
                        <option value="21" data-name="Zacapa" data-lat="14.9722" data-lng="-89.5306">Zacapa</option>
                        <option value="22" data-name="Quiché" data-lat="15.0306" data-lng="-91.1494">Quiché</option>
                    @endif
                </select>
            </div>

            <button type="button" onclick="resetMap()" class="px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition flex items-center gap-1.5">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>Centrar Guatemala</span>
            </button>
        </div>
    </div>

    <!-- Contenedor del Mapa con Paneles Flotantes -->
    <div class="relative w-full h-[620px] rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-xl bg-slate-900">
        
        <!-- Mapa Leaflet -->
        <div id="guatemalaMap" class="w-full h-full z-10"></div>

        <!-- Leyenda Flotante -->
        <div class="absolute bottom-6 left-6 z-20 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-lg text-xs space-y-2 max-w-xs">
            <div class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-amber-500"></i>
                <span>Leyenda de Granjas Solares</span>
            </div>
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                <span class="w-3.5 h-3.5 rounded-full bg-amber-500 ring-4 ring-amber-500/20 inline-block"></span>
                <span>Granja Activa (Generando)</span>
            </div>
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                <span class="w-3.5 h-3.5 rounded-full bg-rose-500 ring-4 ring-rose-500/20 inline-block"></span>
                <span>Alerta por Déficit (≥20%)</span>
            </div>
            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                <span class="w-3.5 h-3.5 rounded-full bg-slate-400 inline-block"></span>
                <span>En Mantenimiento</span>
            </div>
            <div class="pt-2 border-t border-slate-200 dark:border-slate-800 text-[11px] text-slate-400">
                Factor CO₂: <strong class="text-emerald-500">0.40 kg / kWh</strong>
            </div>
        </div>

        <!-- Contador flotante inferior derecho -->
        <div class="absolute bottom-6 right-6 z-20 flex gap-2">
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-lg text-xs flex items-center gap-3">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400">Granjas Visibles</div>
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white" id="visibleFarmsCount">12 granjas</div>
                </div>
                <div class="w-px h-6 bg-slate-200 dark:border-slate-800"></div>
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400">Potencia en Mapa</div>
                    <div class="text-sm font-extrabold text-amber-500" id="visiblePowerCount">8,450 kW</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inicialización del Mapa de Guatemala (Coordenadas centrales de la República)
    const GT_CENTER = [15.5000, -90.2500];
    const GT_ZOOM = 7.5;

    const map = L.map('guatemalaMap', {
        zoomSnap: 0.25,
        zoomDelta: 0.5,
        maxBounds: [
            [12.5, -93.5], // Suroeste
            [19.0, -87.0]  // Noreste
        ]
    }).setView(GT_CENTER, GT_ZOOM);

    // Capas Base 100% Libres y Gratuitas (Sin API Key, Sin marcas de agua)
    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
    });

    const esriSatLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        attribution: '&copy; Esri, Maxar, Earthstar Geographics &mdash; Satélite HD'
    });

    const esriTopoLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        attribution: '&copy; Esri &mdash; Relieve y Topografía IGN Guatemala'
    });

    // Activar capa OSM por defecto
    osmLayer.addTo(map);

    // Selector de Capas para el usuario y el jurado de la UMG
    const baseMaps = {
        "🗺️ Calles y Departamentos (OSM)": osmLayer,
        "🛰️ Satélite HD Fotográfico (Esri)": esriSatLayer,
        "⛰️ Topografía y Relieve (Esri)": esriTopoLayer
    };
    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

    // Capa de Contorno Departamental Dinámico (GeoJSON)
    let departmentsGeoData = null;
    let selectedDepartmentLayer = null;

    // Cargar los polígonos de los 22 departamentos de Guatemala
    fetch('/data/guatemala-departments.geojson')
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar GeoJSON de departamentos');
            return response.json();
        })
        .then(data => {
            departmentsGeoData = data;
        })
        .catch(err => console.warn('GeoJSON de departamentos no disponible:', err));

    // Datos iniciales de Granjas (se inyectan de la BD o datos representativos de los 22 departamentos)
    const rawFarmsData = @json($farmsJson ?? null);
    
    // Granjas semilla realistas para renderizado inmediato
    const defaultFarms = [
        { id: 1, name: "Parque Solar Escuintla Verde", dept_id: 3, dept_name: "Escuintla", lat: 14.3009, lng: -90.7850, capacity_kw: 1850.50, families: 4200, status: 'active', has_alert: false, monthly_kwh: 245000 },
        { id: 2, name: "Granja Solar Guayacán Petén", dept_id: 5, dept_name: "Petén", lat: 16.9200, lng: -89.8900, capacity_kw: 1200.00, families: 2800, status: 'active', has_alert: true, monthly_kwh: 110000 },
        { id: 3, name: "Central Fotovoltaica San Marcos", dept_id: 16, dept_name: "San Marcos", lat: 14.9639, lng: -91.7944, capacity_kw: 950.00, families: 1900, status: 'active', has_alert: false, monthly_kwh: 130000 },
        { id: 4, name: "Parque Solar Izabal Caribe", dept_id: 4, dept_name: "Izabal", lat: 15.7278, lng: -88.5944, capacity_kw: 1450.00, families: 3100, status: 'active', has_alert: false, monthly_kwh: 190000 },
        { id: 5, name: "Granja Solar Los Altos Quetzaltenango", dept_id: 2, dept_name: "Quetzaltenango", lat: 14.8347, lng: -91.5181, capacity_kw: 800.00, families: 1600, status: 'active', has_alert: false, monthly_kwh: 98000 },
        { id: 6, name: "Planta Solar Zacapa Sol", dept_id: 21, dept_name: "Zacapa", lat: 14.9722, lng: -89.5306, capacity_kw: 2100.00, families: 4900, status: 'active', has_alert: false, monthly_kwh: 285000 },
        { id: 7, name: "Granja Solar Metropolitana", dept_id: 1, dept_name: "Guatemala", lat: 14.6349, lng: -90.5069, capacity_kw: 650.00, families: 1200, status: 'maintenance', has_alert: false, monthly_kwh: 45000 },
        { id: 8, name: "Central Solar Chiquimula Oriente", dept_id: 9, dept_name: "Chiquimula", lat: 14.7978, lng: -89.5439, capacity_kw: 1100.00, families: 2300, status: 'active', has_alert: true, monthly_kwh: 85000 },
        { id: 9, name: "Parque Solar El Progreso Guastatoya", dept_id: 10, dept_name: "El Progreso", lat: 14.8653, lng: -90.0764, capacity_kw: 980.00, families: 2100, status: 'active', has_alert: false, monthly_kwh: 135000 },
        { id: 10, name: "Granja Solar Jutiapa Frontera", dept_id: 13, dept_name: "Jutiapa", lat: 14.2817, lng: -89.8958, capacity_kw: 1300.00, families: 2700, status: 'active', has_alert: false, monthly_kwh: 175000 }
    ];

    const farms = (rawFarmsData && rawFarmsData.length > 0) ? rawFarmsData : defaultFarms;
    let markersLayer = L.layerGroup().addTo(map);

    function createCustomPin(color, hasAlert) {
        const pulseHtml = hasAlert 
            ? `<div class="absolute -inset-1 rounded-full bg-rose-500 animate-ping opacity-75"></div>` 
            : '';
        return L.divIcon({
            className: 'custom-solar-pin',
            html: `
                <div class="relative w-8 h-8 flex items-center justify-center">
                    ${pulseHtml}
                    <div class="w-8 h-8 rounded-full ${color} text-white shadow-lg border-2 border-white dark:border-slate-900 flex items-center justify-center z-10 hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                        </svg>
                    </div>
                </div>
            `,
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -18]
        });
    }

    function renderMarkers(farmList) {
        markersLayer.clearLayers();
        let totalPower = 0;

        farmList.forEach(farm => {
            totalPower += parseFloat(farm.capacity_kw || 0);

            let pinColor = 'bg-amber-500';
            if (farm.has_alert) pinColor = 'bg-rose-600';
            else if (farm.status === 'maintenance') pinColor = 'bg-slate-500';

            const co2_kg = (farm.monthly_kwh || 0) * 0.40;
            const co2_tons = (co2_kg / 1000).toFixed(2);

            const popupContent = `
                <div class="p-1 font-sans min-w-[240px]">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-2 mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">${farm.dept_name}</span>
                        ${farm.has_alert 
                            ? '<span class="text-[10px] bg-rose-100 text-rose-700 font-bold px-1.5 py-0.5 rounded">Déficit ≥20%</span>'
                            : '<span class="text-[10px] bg-emerald-100 text-emerald-700 font-bold px-1.5 py-0.5 rounded">Activa</span>'
                        }
                    </div>
                    <h4 class="text-sm font-extrabold text-slate-900 mb-2">${farm.name}</h4>
                    <div class="space-y-1.5 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Capacidad Total:</span>
                            <strong class="text-amber-600 font-bold">${parseFloat(farm.capacity_kw).toLocaleString()} kW</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Familias Beneficiadas:</span>
                            <strong class="text-slate-800">${parseInt(farm.families).toLocaleString()}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">CO₂ Evitado (Mes):</span>
                            <strong class="text-emerald-600 font-bold">${co2_tons} Ton (${co2_kg.toLocaleString()} kg)</strong>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-[10px] text-slate-400">Lat: ${farm.lat.toFixed(4)}, Lng: ${farm.lng.toFixed(4)}</span>
                        <a href="/farms/${farm.id}" class="text-[11px] font-bold text-amber-600 hover:text-amber-700">Ver Ficha &rarr;</a>
                    </div>
                </div>
            `;

            const marker = L.marker([farm.lat, farm.lng], {
                icon: createCustomPin(pinColor, farm.has_alert)
            }).bindPopup(popupContent);

            markersLayer.addLayer(marker);
        });

        // Actualizar contadores
        document.getElementById('visibleFarmsCount').textContent = `${farmList.length} granjas`;
        document.getElementById('visiblePowerCount').textContent = `${Math.round(totalPower).toLocaleString()} kW`;
    }

    function filterFarms() {
        const select = document.getElementById('departmentFilter');
        const selectedDept = select.value;

        // 1. Limpiar contorno previo si existía
        if (selectedDepartmentLayer) {
            map.removeLayer(selectedDepartmentLayer);
            selectedDepartmentLayer = null;
        }

        if (selectedDept === 'all') {
            renderMarkers(farms);
            map.setView(GT_CENTER, GT_ZOOM);
            return;
        }

        const opt = select.options[select.selectedIndex];
        const deptName = opt ? opt.getAttribute('data-name') : null;
        const lat = opt ? parseFloat(opt.getAttribute('data-lat')) : NaN;
        const lng = opt ? parseFloat(opt.getAttribute('data-lng')) : NaN;

        // 2. Filtrar marcadores correspondientes
        const filtered = farms.filter(f => String(f.dept_id) === String(selectedDept));
        renderMarkers(filtered);

        // 3. Dibujar el contorno del departamento seleccionado
        if (departmentsGeoData && deptName) {
            const normalize = str => str ? str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().trim() : '';
            const targetNorm = normalize(deptName);

            const deptFeature = departmentsGeoData.features.find(f => {
                const fName = normalize(f.properties.name || f.properties.shapeName || '');
                return fName === targetNorm;
            });

            if (deptFeature) {
                selectedDepartmentLayer = L.geoJSON(deptFeature, {
                    style: {
                        color: '#f59e0b',        // Borde dorado ámbar solar brillante
                        weight: 3.5,             // Grosor nítido
                        opacity: 0.95,
                        fillColor: '#fbbf24',    // Relleno ámbar solar suave
                        fillOpacity: 0.22,       // Sombreado elegante del territorio
                        dashArray: ''            // Línea continua sin cuadros
                    }
                }).addTo(map);

                selectedDepartmentLayer.bindTooltip(`
                    <div class="font-sans py-0.5">
                        <div class="text-[10px] uppercase font-bold text-amber-400">Departamento</div>
                        <div class="text-sm font-extrabold text-white">${deptFeature.properties.name}</div>
                        <div class="text-[10px] text-slate-300 mt-0.5">${filtered.length} granjas registradas</div>
                    </div>
                `, {
                    sticky: true,
                    className: 'bg-slate-900/95 text-white text-xs px-3 py-1.5 rounded-xl border border-amber-500/50 shadow-2xl backdrop-blur-sm'
                });

                // Encuadrar la cámara suavemente al polígono del departamento
                map.fitBounds(selectedDepartmentLayer.getBounds(), {
                    padding: [45, 45],
                    maxZoom: 10.5
                });
                return;
            }
        }

        // Fallback si no ha cargado el GeoJSON
        if (!isNaN(lat) && !isNaN(lng)) {
            map.flyTo([lat, lng], 9.5, { duration: 1.2 });
        } else if (filtered.length > 0) {
            map.flyTo([filtered[0].lat, filtered[0].lng], 9.5, { duration: 1.2 });
        }
    }

    function resetMap() {
        document.getElementById('departmentFilter').value = 'all';
        if (selectedDepartmentLayer) {
            map.removeLayer(selectedDepartmentLayer);
            selectedDepartmentLayer = null;
        }
        renderMarkers(farms);
        map.setView(GT_CENTER, GT_ZOOM);
    }

    // Render inicial
    renderMarkers(farms);
</script>
@endpush
