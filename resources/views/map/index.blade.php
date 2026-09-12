@extends('layouts.app', ['title' => 'Mapa Interactivo de Guatemala'])

@push('styles')
<style>
    #guatemalaMap .leaflet-overlay-pane svg,
    #guatemalaMap .leaflet-container svg,
    #guatemalaMap svg,
    .leaflet-pane > svg {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }
    path.leaflet-interactive:focus,
    path:focus {
        outline: none !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-3 sm:space-y-4">

    <!-- Encabezado y Filtros Rápidos (Compacto en móvil para cero scroll) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3 bg-white dark:bg-slate-900 p-2.5 sm:p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between sm:block">
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">TERRITORIO SOLAR</span>
                </div>
                <h2 class="text-xs sm:text-base md:text-lg font-bold text-slate-900 dark:text-white leading-tight mt-0.5">
                    La energía en el territorio
                </h2>
            </div>
            <!-- Botón Centrar en móvil compacto -->
            <button type="button" onclick="resetMap()" class="sm:hidden p-1.5 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition active:scale-95 flex items-center gap-1" title="Centrar Guatemala">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-amber-500"></i>
            </button>
        </div>

        <!-- Controles / Filtros -->
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:flex-initial sm:w-auto">
                <label for="departmentFilter" class="sr-only">Filtrar por departamento</label>
                <select id="departmentFilter" onchange="filterFarms()" class="w-full sm:w-auto pl-2.5 pr-8 py-1.5 sm:py-2 text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="all">Todos los Deptos (22)</option>
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

            <button type="button" onclick="resetMap()" class="hidden sm:flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition active:scale-95">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>Centrar Guatemala</span>
            </button>
        </div>
    </div>

    <!-- Contenedor del Mapa con Paneles Flotantes (Cero scroll garantizado) -->
    <div class="relative w-full h-[calc(100vh-190px)] min-h-[300px] md:h-[calc(100vh-220px)] md:min-h-[520px] rounded-2xl sm:rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-xl bg-slate-900">
        
        <!-- Mapa Leaflet -->
        <div id="guatemalaMap" class="w-full h-full z-10"></div>

        <!-- Leyenda Flotante (Siempre visible y contenida en pantalla) -->
        <div class="absolute bottom-2 left-2 sm:bottom-3 sm:left-3 z-20 bg-slate-900/95 text-white backdrop-blur-md p-2 sm:p-3.5 rounded-xl sm:rounded-2xl border border-slate-700/80 shadow-2xl text-[9px] sm:text-xs space-y-1 sm:space-y-1.5 max-w-[145px] sm:max-w-xs select-none">
            <div class="font-bold text-amber-400 flex items-center gap-1 leading-none text-[10px] sm:text-xs">
                <i data-lucide="layers" class="w-3 h-3 sm:w-4 sm:h-4 text-amber-400"></i>
                <span>Leyenda Solar</span>
            </div>
            <div class="flex items-center gap-1.5 text-slate-200 leading-none">
                <span class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-amber-500 ring-2 ring-amber-500/30 inline-block flex-shrink-0"></span>
                <span class="truncate">Activa</span>
            </div>
            <div class="flex items-center gap-1.5 text-slate-200 leading-none">
                <span class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-rose-500 ring-2 ring-rose-500/30 inline-block flex-shrink-0"></span>
                <span class="truncate">Déficit (≥20%)</span>
            </div>
            <div class="flex items-center gap-1.5 text-slate-200 leading-none">
                <span class="w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-slate-400 inline-block flex-shrink-0"></span>
                <span class="truncate">No operativa</span>
            </div>
            <div class="hidden sm:block pt-1.5 border-t border-slate-700 text-[10px] text-slate-400">
                Tamaño del pin: capacidad instalada
            </div>
        </div>

        <!-- Contador flotante inferior derecho -->
        <div class="absolute bottom-2 right-2 sm:bottom-3 sm:right-3 z-20 select-none">
            <div class="bg-slate-900/95 text-white backdrop-blur-md px-2.5 py-1.5 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl border border-slate-700/80 shadow-2xl text-[9px] sm:text-xs flex items-center gap-2 sm:gap-3">
                <div>
                    <div class="text-[8px] sm:text-[10px] uppercase font-bold text-slate-400 leading-none">Granjas</div>
                    <div class="text-xs sm:text-sm font-extrabold text-white leading-tight mt-0.5" id="visibleFarmsCount">0</div>
                </div>
                <div class="w-px h-4 sm:h-6 bg-slate-700"></div>
                <div>
                    <div class="text-[8px] sm:text-[10px] uppercase font-bold text-slate-400 leading-none">Potencia</div>
                    <div class="text-xs sm:text-sm font-extrabold text-amber-400 leading-tight mt-0.5" id="visiblePowerCount">0 kW</div>
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

    // 1. Contorno Nacional Permanente de la República de Guatemala (SIEMPRE visible para contraste)
    let gtNationalLayer = null;

    fetch('/data/guatemala.geojson')
        .then(response => {
            if (!response.ok) throw new Error('Error al cargar GeoJSON nacional');
            return response.json();
        })
        .then(geoData => {
            gtNationalLayer = L.geoJSON(geoData, {
                style: {
                    color: '#f59e0b',        // Amarillo ámbar solar continuo
                    weight: 2.5,             // Trazo elegante y definido
                    opacity: 0.90,           // Muy visible
                    fillColor: '#fbbf24',    // Sombreado amarillo solar suave
                    fillOpacity: 0.08,       // Contraste sutil y elegante contra países vecinos
                    dashArray: ''            // Línea continua sin marco
                },
                interactive: false           // No interfiere con clics en departamentos ni pines
            }).addTo(map);
        })
        .catch(err => console.warn('GeoJSON nacional no disponible:', err));

    // 2. Capa de Contorno Departamental Dinámico (GeoJSON de los 22 departamentos)
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
    
    const farms = Array.isArray(rawFarmsData) ? rawFarmsData : [];
    let markersLayer = L.layerGroup().addTo(map);
    const farmUrl = @json(route('farms.show', ['farm' => '__FARM__']));
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[character]);

    function createCustomPin(color, hasAlert, capacity) {
        const size = Number(capacity) >= 1000 ? 42 : Number(capacity) >= 500 ? 36 : 30;
        const pulseHtml = hasAlert 
            ? `<div class="absolute -inset-1 rounded-full bg-rose-500 animate-ping opacity-75"></div>` 
            : '';
        return L.divIcon({
            className: 'custom-solar-pin',
            html: `
                <div class="relative flex items-center justify-center" style="width:${size}px;height:${size}px">
                    ${pulseHtml}
                    <div class="w-full h-full rounded-full ${color} text-white shadow-lg border-2 border-white dark:border-slate-900 flex items-center justify-center z-10 hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                        </svg>
                    </div>
                </div>
            `,
            iconSize: [size, size],
            iconAnchor: [size / 2, size / 2],
            popupAnchor: [0, -size / 2]
        });
    }

    const farmMarkersMap = {};

    function renderMarkers(farmList) {
        markersLayer.clearLayers();
        let totalPower = 0;

        farmList.forEach(farm => {
            totalPower += parseFloat(farm.capacity_kw || 0);

            let pinColor = 'bg-amber-500';
            if (farm.has_alert) pinColor = 'bg-rose-600';
            else if (farm.status !== 'active') pinColor = 'bg-slate-500';

            const co2_kg = (farm.monthly_kwh || 0) * 0.40;
            const co2_tons = (co2_kg / 1000).toFixed(2);

            const popupContent = `
                <div class="p-1 font-sans min-w-[240px]">
                    <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-2 mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">${escapeHtml(farm.dept_name)}</span>
                        ${farm.has_alert 
                            ? '<span class="text-[10px] bg-rose-100 text-rose-700 font-bold px-1.5 py-0.5 rounded">Déficit ≥20%</span>'
                            : `<span class="text-[10px] bg-slate-100 text-slate-700 font-bold px-1.5 py-0.5 rounded">${farm.status === 'active' ? 'Activa' : farm.status === 'maintenance' ? 'Mantenimiento' : 'Inactiva'}</span>`
                        }
                    </div>
                    <h4 class="text-sm font-extrabold text-slate-900 mb-2">${escapeHtml(farm.name)}</h4>
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
                        <span class="text-[10px] text-slate-400">Lat: ${Number(farm.lat).toFixed(4)}, Lng: ${Number(farm.lng).toFixed(4)}</span>
                        <a href="${escapeHtml(farmUrl.replace('__FARM__', encodeURIComponent(farm.id)))}" class="text-[11px] font-bold text-amber-600 hover:text-amber-700">Ver ficha &rarr;</a>
                    </div>
                </div>
            `;

            const marker = L.marker([farm.lat, farm.lng], {
                icon: createCustomPin(pinColor, farm.has_alert, farm.capacity_kw),
                title: `${farm.name}: ${farm.capacity_kw} kW`,
                alt: `Granja ${farm.name}`
            }).bindPopup(popupContent);

            farmMarkersMap[farm.id] = marker;
            markersLayer.addLayer(marker);
        });

        // Actualizar contadores
        document.getElementById('visibleFarmsCount').textContent = `${farmList.length}`;
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
                        color: '#b45309',        // Borde dorado profundo / ámbar intenso para destacar
                        weight: 4.0,             // Contorno nítido y prominente
                        opacity: 1.0,            // Máxima opacidad
                        fillColor: '#f59e0b',    // Relleno ámbar solar cálido
                        fillOpacity: 0.30,       // Mayor contraste para el departamento activo
                        dashArray: ''            // Línea continua sin cuadros
                    }
                }).addTo(map);

                selectedDepartmentLayer.bindTooltip(`
                    <div class="font-sans py-0.5">
                        <div class="text-[10px] uppercase font-bold text-amber-400">Departamento</div>
                        <div class="text-sm font-extrabold text-white">${escapeHtml(deptFeature.properties.name)}</div>
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

    // Enfocar granja específica por ID, resaltar su departamento y abrir su popup
    function focusFarmById(farmId) {
        const targetFarm = farms.find(f => String(f.id) === String(farmId));
        if (targetFarm) {
            const select = document.getElementById('departmentFilter');
            if (select) {
                select.value = String(targetFarm.dept_id);
                filterFarms();
            }

            // Zoom suave y moderado (alrededor de 9.8) para ver todo el departamento delimitado y la granja sin zoom exagerado
            setTimeout(() => {
                const moderateZoom = 9.8;
                map.flyTo([targetFarm.lat, targetFarm.lng], moderateZoom, { duration: 1.0 });
                setTimeout(() => {
                    if (farmMarkersMap[targetFarm.id]) {
                        farmMarkersMap[targetFarm.id].openPopup();
                    }
                }, 1000);
            }, 350);
        }
    }

    // Render inicial
    renderMarkers(farms);

    // Revisar si viene ?farm=ID o ?farm_id=ID en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const targetFarmParam = urlParams.get('farm') || urlParams.get('farm_id');
    if (targetFarmParam) {
        setTimeout(() => {
            focusFarmById(targetFarmParam);
        }, 500);
    }
</script>
@endpush
