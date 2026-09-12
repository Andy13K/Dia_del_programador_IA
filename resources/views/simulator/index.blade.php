@extends('layouts.app', ['title' => 'Simulador SCADA IoT'])

@section('content')
<div class="space-y-6">

    <!-- Encabezado del Centro de Control SCADA -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="z-10">
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500" id="liveStatusLed"></span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 font-bold text-xs tracking-wider uppercase">
                    Laboratorio SCADA · Telemetría IoT en Vivo
                </span>
                <span class="text-xs text-slate-400 hidden sm:inline">Protocolo Modbus-TCP / MQTT v5</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-1.5 flex items-center gap-2">
                Centro de Control y Simulación Fotovoltaica
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 max-w-2xl">
                Emulador interactivo de telemetría de campo: modifique en caliente la irradiancia solar, desconecte inversores y evalúe la respuesta reactiva ante contingencias y alertas RF-14.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 z-10">
            <button type="button" id="btnToggleStream" class="inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-200 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-0.5 active:scale-[0.98] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 min-h-11 px-4 py-2 text-sm gap-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold focus:ring-amber-500 shadow-amber-500/20 cursor-pointer">
                <i data-lucide="play" class="w-4 h-4" id="iconStream"></i>
                <span id="textStream">Iniciar Flujo en Vivo</span>
            </button>
            <button type="button" id="btnResetNominal" class="inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-200 ease-[cubic-bezier(0.16,1,0.3,1)] hover:-translate-y-0.5 active:scale-[0.98] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 min-h-11 px-3 py-2 text-sm gap-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 cursor-pointer">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Restablecer</span>
            </button>
        </div>
    </div>

    <!-- Barra de Selección de Sucursal / Granja -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex-1">
            <label for="scadaFarmSelect" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">
                Granja Solar a Monitorear y Simular:
            </label>
            <select id="scadaFarmSelect" class="w-full text-sm font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" 
                            data-capacity="{{ $farm->calculated_capacity_kw }}"
                            data-name="{{ $farm->name }}"
                            data-dept="{{ $farm->department?->name ?? 'Nacional' }}"
                            data-families="{{ $farm->benefited_families }}"
                            {{ $selectedFarm->id === $farm->id ? 'selected' : '' }}>
                        {{ $farm->name }} ({{ $farm->department?->name ?? 'Guatemala' }}) · Potencia Nominal: {{ number_format($farm->calculated_capacity_kw, 1) }} kW
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-3 text-xs">
            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Capacidad Base</span>
                <strong id="cardNominalCapacity" class="text-sm text-amber-600 dark:text-amber-400">{{ number_format($selectedFarm->calculated_capacity_kw, 1) }} kW</strong>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                <span class="block text-[10px] text-slate-400 uppercase font-semibold">Comunidades</span>
                <strong id="cardFamilies" class="text-sm text-sky-600 dark:text-sky-400">{{ number_format($selectedFarm->benefited_families) }} familias</strong>
            </div>
        </div>
    </div>

    <!-- Banner de Alerta Crítica en Vivo (Oculto por defecto, se activa al haber déficit >= 20%) -->
    <div id="scadaAlarmBanner" class="hidden p-4 rounded-2xl bg-rose-500/15 border-2 border-rose-500 text-rose-900 dark:text-rose-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-pulse">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-rose-500 text-white rounded-xl">
                <i data-lucide="triangle-alert" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold flex items-center gap-2">
                    <span>INCIDENTE SCADA ACTIVO: DÉFICIT CRÍTICO ≥ 20%</span>
                    <span id="alarmDeficitBadge" class="px-2 py-0.5 rounded-full text-xs bg-rose-600 text-white font-mono">-26.5%</span>
                </h4>
                <p class="text-xs text-rose-800 dark:text-rose-200 mt-0.5" id="alarmDescriptionText">
                    Se detectó una desviación severa en los inversores. La regla normativa RF-14 generó un incidente activo.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('alerts.index') }}" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1.5 transition">
                <span>Ver en Alertas</span>
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
            </a>
            <button type="button" onclick="dismissAlarmBanner()" class="px-2 py-1.5 text-xs text-rose-700 dark:text-rose-300 hover:underline">
                Ocultar aviso
            </button>
        </div>
    </div>

    <!-- Cuadrícula de Instrumentos y Medidores Digitales (Gauges) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5">
        <!-- Potencia Instantánea -->
        <article class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-all duration-300" id="gaugeCardPower">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span class="font-medium">Potencia Generada</span>
                <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span id="valCurrentKw" class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">0.0</span>
                <span class="text-xs font-semibold text-slate-400">kW</span>
            </div>
            <div class="mt-2">
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                    <div id="barCapacityPercent" class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>
                <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                    <span>Eficiencia: <strong id="valCapacityPercent" class="text-amber-600 dark:text-amber-400">0%</strong></span>
                    <span id="valExpectedKw">Esp: 0 kW</span>
                </div>
            </div>
        </article>

        <!-- Irradiancia Solar -->
        <article class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span class="font-medium">Irradiancia (GHI)</span>
                <i data-lucide="sun" class="w-4 h-4 text-amber-500" id="iconSunWeather"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span id="valIrradiance" class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">980</span>
                <span class="text-xs font-semibold text-slate-400">W/m²</span>
            </div>
            <p id="valWeatherLabel" class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-2 truncate">
                ☀️ Cielo despejado · Radiación pico
            </p>
        </article>

        <!-- Temperatura de Células FV -->
        <article class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span class="font-medium">Temp. Módulos</span>
                <i data-lucide="thermometer" class="w-4 h-4 text-rose-500"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span id="valTemperature" class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white font-mono">42.5</span>
                <span class="text-xs font-semibold text-slate-400">°C</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
                Pérdida térmica: <strong id="valThermalLoss" class="text-slate-600 dark:text-slate-300">-6.8%</strong>
            </p>
        </article>

        <!-- Inversores en Operación -->
        <article class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm" id="gaugeCardInverters">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span class="font-medium">Inversores Activos</span>
                <i data-lucide="cpu" class="w-4 h-4 text-emerald-500"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span id="valActiveInverters" class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono">4 / 4</span>
            </div>
            <p id="valInvertersNote" class="text-[11px] text-slate-400 mt-2">
                100% potencia nominal disponible
            </p>
        </article>

        <!-- Tasa de Mitigación CO2 -->
        <article class="col-span-2 lg:col-span-1 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span class="font-medium">Tasa CO₂ Evitada<x-co2-info /></span>
                <i data-lucide="leaf" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span id="valCo2Rate" class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono">0.0</span>
                <span class="text-xs font-semibold text-slate-400">kg/h</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
                Norma CNEE 0.40 kg/kWh
            </p>
        </article>
    </div>

    <!-- Gráfica Osciloscopio en Vivo (Streaming continuo) -->
    <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-amber-500"></i>
                    <span>Curva de Potencia Viva (Osciloscopio SCADA)</span>
                </h3>
                <p class="text-xs text-slate-400">Transmisión de telemetría de alta frecuencia muestreada en vivo.</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Potencia Generada (kW)</span>
                <span class="flex items-center gap-1.5"><span class="w-4 border-t-2 border-dashed border-slate-400"></span> Potencia Teórica</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-sky-400"></span> Radiación (W/m²)</span>
            </div>
        </div>
        <div class="h-64 sm:h-72 mt-3 w-full">
            <canvas id="scadaOscilloscopeChart"></canvas>
        </div>
    </div>

    <!-- Panel de Control Interactivo y Terminal de Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Columna Izquierda: Controles e Inyector de Contingencias -->
        <div class="lg:col-span-6 space-y-6">

            <!-- Control de Clima y Radiación -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                    <i data-lucide="cloud-sun" class="w-4 h-4 text-amber-500"></i>
                    <span>1. Simular Condiciones Climáticas en Vivo</span>
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    <button type="button" onclick="setWeather('sunny', 1000)" id="btnWeatherSunny" class="p-3 rounded-xl border border-amber-500 bg-amber-500/10 text-amber-800 dark:text-amber-200 text-xs font-bold text-center flex flex-col items-center gap-1.5 transition cursor-pointer">
                        <i data-lucide="sun" class="w-5 h-5 text-amber-500"></i>
                        <span>Pleno Sol</span>
                        <small class="text-[10px] opacity-75">1000 W/m²</small>
                    </button>
                    <button type="button" onclick="setWeather('partly_cloudy', 600)" id="btnWeatherCloudy" class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 text-xs font-medium text-center flex flex-col items-center gap-1.5 transition cursor-pointer">
                        <i data-lucide="cloud-sun" class="w-5 h-5 text-slate-400"></i>
                        <span>Nubosidad</span>
                        <small class="text-[10px] opacity-75">600 W/m²</small>
                    </button>
                    <button type="button" onclick="setWeather('stormy', 180)" id="btnWeatherStormy" class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 text-xs font-medium text-center flex flex-col items-center gap-1.5 transition cursor-pointer">
                        <i data-lucide="cloud-lightning" class="w-5 h-5 text-sky-500"></i>
                        <span>Tormenta</span>
                        <small class="text-[10px] opacity-75">180 W/m²</small>
                    </button>
                    <button type="button" onclick="setWeather('night', 0)" id="btnWeatherNight" class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 text-slate-700 dark:text-slate-300 text-xs font-medium text-center flex flex-col items-center gap-1.5 transition cursor-pointer">
                        <i data-lucide="moon" class="w-5 h-5 text-indigo-400"></i>
                        <span>Noche</span>
                        <small class="text-[10px] opacity-75">0 W/m²</small>
                    </button>
                </div>
            </div>

            <!-- Interruptores de Inversores (Breakers de Campo) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="toggle-right" class="w-4 h-4 text-emerald-500"></i>
                        <span>2. Interruptores de Inversores Fotovoltaicos (4 Canales)</span>
                    </h3>
                    <span class="text-[10px] text-slate-400">Clic para alternar ON/OFF</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                    @for($i = 1; $i <= 4; $i++)
                    <div id="inverterCard{{ $i }}" class="p-3 rounded-xl border border-emerald-500/40 bg-emerald-500/5 text-center flex flex-col items-center gap-2 transition">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-100">Inversor #{{ $i }}</span>
                        <button type="button" onclick="toggleInverter({{ $i }})" id="btnInverterToggle{{ $i }}" class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer">
                            ACTIVO
                        </button>
                        <span class="text-[10px] text-slate-400 font-mono" id="txtInverterPower{{ $i }}">25% cap</span>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Botones de Contingencias e Inyección a Base de Datos -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="radio" class="w-4 h-4 text-rose-500"></i>
                    <span>3. Inyector de Contingencias & Persistencia Oficial</span>
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <button type="button" onclick="triggerIncident('inverter_failure', 26.5)" class="p-3 rounded-xl border border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 text-rose-900 dark:text-rose-200 text-xs font-bold text-left flex items-start gap-2.5 transition cursor-pointer">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="block">Falla de Inversores (-26.5%)</strong>
                            <span class="text-[10px] font-normal opacity-90">Desconecta 2 ramas y persiste alerta activa RF-14 en BD.</span>
                        </div>
                    </button>
                    <button type="button" onclick="triggerIncident('severe_storm', 48.0)" class="p-3 rounded-xl border border-sky-500/30 bg-sky-500/10 hover:bg-sky-500/20 text-sky-900 dark:text-sky-200 text-xs font-bold text-left flex items-start gap-2.5 transition cursor-pointer">
                        <i data-lucide="cloud-lightning" class="w-5 h-5 text-sky-600 dark:text-sky-400 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <strong class="block">Tormenta Severa (-48.0%)</strong>
                            <span class="text-[10px] font-normal opacity-90">Caída drástica de irradiancia solar y alerta crítica en BD.</span>
                        </div>
                    </button>
                </div>
                <div class="pt-2 flex flex-col sm:flex-row gap-2">
                    <button type="button" onclick="persistCurrentSnapshot()" id="btnPersistSnapshot" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-slate-950 flex items-center justify-center gap-2 transition cursor-pointer shadow-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Persistir Medición Actual en Base de Datos</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Columna Derecha: Terminal SCADA de Telemetría Cruda en Tiempo Real -->
        <div class="lg:col-span-6 bg-slate-950 rounded-3xl p-5 border border-slate-800 shadow-lg flex flex-col h-[520px] lg:h-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="flex gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-300 ml-2">CONSOLA SCADA TELEMETRÍA (IoT RAW STREAM)</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="clearScadaTerminal()" class="text-[11px] font-mono text-slate-400 hover:text-white px-2 py-1 rounded bg-slate-800/80 transition cursor-pointer">
                        Limpiar
                    </button>
                    <button type="button" onclick="copyTerminalLogs()" class="text-[11px] font-mono text-slate-400 hover:text-white px-2 py-1 rounded bg-slate-800/80 transition cursor-pointer">
                        Copiar
                    </button>
                </div>
            </div>

            <!-- Contenedor del stream de logs con scroll monospaciado -->
            <div id="scadaTerminalLogs" class="flex-1 overflow-y-auto font-mono text-[11px] leading-relaxed p-3 space-y-1.5 text-slate-300 scrollbar-thin scrollbar-thumb-slate-800">
                <div class="text-emerald-400 font-bold">[SISTEMA] Conexión SCADA establecida con Gateway RTU-GT-01 (IP 192.168.10.45:502).</div>
                <div class="text-slate-400">[INICIALIZACIÓN] Granja activa: {{ $selectedFarm->name }} (Capacidad Nominal: {{ number_format($selectedFarm->calculated_capacity_kw, 1) }} kW).</div>
                <div class="text-amber-400">[INFO] Presione "Iniciar Flujo en Vivo" para recibir paquetes continuos de campo cada 2s.</div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-[10px] font-mono text-slate-400">
                <span>ESTADO: <strong class="text-emerald-400" id="terminalStatusText">LISTO</strong></span>
                <span>PAQUETES RECIBIDOS: <strong class="text-white" id="terminalPacketCount">0</strong></span>
                <span class="hidden sm:inline">BAUD RATE: 115200 bps · CRC-16 OK</span>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Variables de Estado del Laboratorio SCADA
    let streamInterval = null;
    let isStreaming = false;
    let packetCount = 0;
    let activeInverters = [true, true, true, true]; // 4 inversores
    let currentWeather = 'sunny';
    let currentIrradiance = 1000; // W/m2 base
    let baseCapacityKw = {{ (float) $selectedFarm->calculated_capacity_kw }};
    let farmName = "{{ $selectedFarm->name }}";
    let farmId = {{ (int) $selectedFarm->id }};

    // Historial temporal para la gráfica de osciloscopio (últimos 15 puntos)
    const maxDataPoints = 15;
    const chartLabels = [];
    const chartPowerData = [];
    const chartExpectedData = [];
    const chartIrradianceData = [];

    // Inicializar puntos de arranque
    for (let i = maxDataPoints; i > 0; i--) {
        const time = new Date(Date.now() - (i * 2000));
        chartLabels.push(time.toTimeString().split(' ')[0]);
        chartPowerData.push(Number((baseCapacityKw * 0.95).toFixed(1)));
        chartExpectedData.push(Number(baseCapacityKw.toFixed(1)));
        chartIrradianceData.push(1000);
    }

    // Inicialización del Gráfico Chart.js
    let scadaChart = null;
    const chartCanvas = document.getElementById('scadaOscilloscopeChart');
    if (chartCanvas && window.Chart) {
        const ctx = chartCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(245, 158, 11, 0.28)');
        gradient.addColorStop(1, 'rgba(245, 158, 11, 0.00)');

        scadaChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Potencia Real (kW)',
                        data: chartPowerData,
                        borderColor: '#f59e0b',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 2,
                        yAxisID: 'yPower'
                    },
                    {
                        label: 'Potencia Nominal (kW)',
                        data: chartExpectedData,
                        borderColor: '#94a3b8',
                        borderDash: [5, 5],
                        tension: 0.1,
                        borderWidth: 1.5,
                        pointRadius: 0,
                        yAxisID: 'yPower'
                    },
                    {
                        label: 'Radiación (W/m²)',
                        data: chartIrradianceData,
                        borderColor: '#38bdf8',
                        borderWidth: 1.5,
                        pointRadius: 0,
                        yAxisID: 'yIrradiance'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 400 },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#8493a7', font: { size: 10 } }
                    },
                    yPower: {
                        position: 'left',
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.08)' },
                        ticks: {
                            color: '#f59e0b',
                            font: { size: 10 },
                            callback: v => v + ' kW'
                        }
                    },
                    yIrradiance: {
                        position: 'right',
                        beginAtZero: true,
                        max: 1200,
                        grid: { display: false },
                        ticks: {
                            color: '#38bdf8',
                            font: { size: 10 },
                            callback: v => v + ' W'
                        }
                    }
                }
            }
        });
    }

    // Manejo de cambio de Granja Solar en el selector
    document.getElementById('scadaFarmSelect')?.addEventListener('change', function(e) {
        const option = this.options[this.selectedIndex];
        farmId = parseInt(this.value);
        baseCapacityKw = parseFloat(option.dataset.capacity) || 100.0;
        farmName = option.dataset.name;

        document.getElementById('cardNominalCapacity').textContent = baseCapacityKw.toFixed(1) + ' kW';
        document.getElementById('cardFamilies').textContent = Number(option.dataset.families).toLocaleString() + ' familias';

        logTerminal(`[SISTEMA] Canal SCADA sintonizado a: ${farmName} (${option.dataset.dept}) - Potencia Nominal: ${baseCapacityKw.toFixed(1)} kW.`, 'text-sky-400');
        updateReadouts(true);
    });

    // Control del Flujo en Vivo (Auto-Tick cada 2 segundos)
    const btnToggleStream = document.getElementById('btnToggleStream');
    btnToggleStream?.addEventListener('click', function() {
        if (isStreaming) {
            pauseStreaming();
        } else {
            startStreaming();
        }
    });

    function startStreaming() {
        isStreaming = true;
        document.getElementById('textStream').textContent = 'Pausar Flujo en Vivo';
        document.getElementById('iconStream').setAttribute('data-lucide', 'pause');
        document.getElementById('liveStatusLed').className = 'relative inline-flex rounded-full h-3 w-3 bg-emerald-500 animate-pulse';
        document.getElementById('terminalStatusText').textContent = 'TRANSMITIENDO EN VIVO (2s)';
        document.getElementById('terminalStatusText').className = 'text-emerald-400 font-bold';

        logTerminal(`[SCADA] Transmisión continua activada: muestreo Modbus-TCP cada 2,000 ms.`, 'text-emerald-400');
        window.lucide?.createIcons();

        streamInterval = setInterval(tickTelemetry, 2000);
    }

    function pauseStreaming() {
        isStreaming = false;
        clearInterval(streamInterval);
        document.getElementById('textStream').textContent = 'Iniciar Flujo en Vivo';
        document.getElementById('iconStream').setAttribute('data-lucide', 'play');
        document.getElementById('liveStatusLed').className = 'relative inline-flex rounded-full h-3 w-3 bg-slate-400';
        document.getElementById('terminalStatusText').textContent = 'PAUSADO';
        document.getElementById('terminalStatusText').className = 'text-amber-400';

        logTerminal(`[SCADA] Flujo de telemetría pausado por el operador.`, 'text-amber-400');
        window.lucide?.createIcons();
    }

    // Cálculo central de telemetría instantánea
    function calculateTelemetry() {
        // Factor de inversores (cada uno pesa 25%)
        const activeCount = activeInverters.filter(Boolean).length;
        const inverterFactor = activeCount / 4;

        // Ruido aleatorio realista de campo (+- 1.5%)
        const jitter = 0.985 + (Math.random() * 0.03);

        // Factor de radiación (1000 W/m2 = 1.0)
        const irradianceFactor = Math.max(0, currentIrradiance / 1000);

        // Potencia generada instantánea
        const currentKw = Number((baseCapacityKw * irradianceFactor * inverterFactor * jitter).toFixed(1));
        const expectedKw = Number((baseCapacityKw * irradianceFactor).toFixed(1));

        // Porcentaje de eficiencia
        const capacityPercent = baseCapacityKw > 0 ? Math.round((currentKw / baseCapacityKw) * 100) : 0;

        // Déficit respecto a lo esperado por radiación
        const deficitPercent = expectedKw > 0 ? Math.max(0, Number((((expectedKw - currentKw) / expectedKw) * 100).toFixed(1))) : 0;

        // Temperatura estimada de módulos: ambiente (28°C) + irradiancia * 0.02
        const temp = Number((26.0 + (currentIrradiance * 0.018) + (Math.random() * 0.6)).toFixed(1));

        // Tasa horaria de CO2 mitigado
        const co2Rate = Number((currentKw * 0.40).toFixed(2));

        return {
            currentKw,
            expectedKw,
            capacityPercent,
            deficitPercent,
            activeCount,
            temp,
            co2Rate
        };
    }

    // Función de tick periódico
    function tickTelemetry() {
        packetCount++;
        document.getElementById('terminalPacketCount').textContent = packetCount;

        const data = updateReadouts();

        // Enviar log a la terminal
        const now = new Date();
        const timeStr = now.toTimeString().split(' ')[0];
        
        if (data.deficitPercent >= 20.0) {
            logTerminal(`[${timeStr}] [ALERTA-RF14] Inversor alarm: Déficit ${data.deficitPercent}% detectado en ${farmName}. Potencia: ${data.currentKw} kW (Esp: ${data.expectedKw} kW).`, 'text-rose-400 font-bold');
        } else if (packetCount % 3 === 0) {
            logTerminal(`[${timeStr}] [MODBUS-TCP] RTU-${farmId}: ${data.activeCount}/4 Inversores OK | GHI: ${currentIrradiance} W/m² | ModTemp: ${data.temp}°C | P: ${data.currentKw} kW`, 'text-slate-300');
        }
    }

    // Actualización de los indicadores en el DOM y la gráfica
    function updateReadouts(forceRedraw = false) {
        const data = calculateTelemetry();

        // Instrumentos numéricos
        document.getElementById('valCurrentKw').textContent = data.currentKw.toFixed(1);
        document.getElementById('valCapacityPercent').textContent = data.capacityPercent + '%';
        document.getElementById('barCapacityPercent').style.width = Math.min(100, data.capacityPercent) + '%';
        document.getElementById('valExpectedKw').textContent = 'Esp: ' + data.expectedKw.toFixed(1) + ' kW';
        document.getElementById('valIrradiance').textContent = currentIrradiance;
        document.getElementById('valTemperature').textContent = data.temp.toFixed(1);
        document.getElementById('valActiveInverters').textContent = `${data.activeCount} / 4`;
        document.getElementById('valCo2Rate').textContent = data.co2Rate.toFixed(1);

        // Control visual de alarma RF-14
        const banner = document.getElementById('scadaAlarmBanner');
        const badge = document.getElementById('alarmDeficitBadge');
        const desc = document.getElementById('alarmDescriptionText');
        const gaugePower = document.getElementById('gaugeCardPower');

        if (data.deficitPercent >= 20.0 && data.expectedKw > 0) {
            banner.classList.remove('hidden');
            badge.textContent = `-${data.deficitPercent}%`;
            desc.textContent = `Pérdida en ${farmName}: Potencia actual ${data.currentKw} kW frente a ${data.expectedKw} kW esperados. Déficit supera el umbral crítico del 20% (RF-14).`;
            gaugePower.classList.add('ring-2', 'ring-rose-500');
        } else {
            banner.classList.add('hidden');
            gaugePower.classList.remove('ring-2', 'ring-rose-500');
        }

        // Desplazar gráfica de osciloscopio
        if (scadaChart) {
            const now = new Date();
            const timeStr = now.toTimeString().split(' ')[0];

            scadaChart.data.labels.push(timeStr);
            scadaChart.data.datasets[0].data.push(data.currentKw);
            scadaChart.data.datasets[1].data.push(data.expectedKw);
            scadaChart.data.datasets[2].data.push(currentIrradiance);

            if (scadaChart.data.labels.length > maxDataPoints) {
                scadaChart.data.labels.shift();
                scadaChart.data.datasets[0].data.shift();
                scadaChart.data.datasets[1].data.shift();
                scadaChart.data.datasets[2].data.shift();
            }

            scadaChart.update(forceRedraw ? 'default' : 'none');
        }

        return data;
    }

    // Modificar condición climática
    window.setWeather = function(weather, irradiance) {
        currentWeather = weather;
        currentIrradiance = irradiance;

        ['btnWeatherSunny', 'btnWeatherCloudy', 'btnWeatherStormy', 'btnWeatherNight'].forEach(id => {
            const btn = document.getElementById(id);
            btn.className = btn.className.replace('border-amber-500 bg-amber-500/10 text-amber-800 dark:text-amber-200 font-bold', 'border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-medium');
        });

        const activeBtn = {
            'sunny': 'btnWeatherSunny',
            'partly_cloudy': 'btnWeatherCloudy',
            'stormy': 'btnWeatherStormy',
            'night': 'btnWeatherNight'
        }[weather];

        const btn = document.getElementById(activeBtn);
        if (btn) {
            btn.className = 'p-3 rounded-xl border border-amber-500 bg-amber-500/10 text-amber-800 dark:text-amber-200 text-xs font-bold text-center flex flex-col items-center gap-1.5 transition cursor-pointer';
        }

        const labels = {
            'sunny': '☀️ Cielo despejado · Radiación pico',
            'partly_cloudy': '⛅ Nubosidad variable · Radiación media',
            'stormy': '⛈️ Tormenta eléctrica · Radiación atenuada',
            'night': '🌙 Sin radiación solar (Noche)'
        };
        document.getElementById('valWeatherLabel').textContent = labels[weather];

        logTerminal(`[METEO-STATION] Ajuste climático: ${labels[weather]} (${irradiance} W/m²).`, 'text-sky-400');
        updateReadouts(true);
    };

    // Alternar interruptor de inversor (Breakers)
    window.toggleInverter = function(index) {
        const idx = index - 1;
        activeInverters[idx] = !activeInverters[idx];

        const btn = document.getElementById(`btnInverterToggle${index}`);
        const card = document.getElementById(`inverterCard${index}`);

        if (activeInverters[idx]) {
            btn.textContent = 'ACTIVO';
            btn.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer';
            card.className = 'p-3 rounded-xl border border-emerald-500/40 bg-emerald-500/5 text-center flex flex-col items-center gap-2 transition';
            logTerminal(`[MODBUS-COMMAND] Inversor #${index} sincronizado y conectado a la red.`, 'text-emerald-400');
        } else {
            btn.textContent = 'TRIPPED';
            btn.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition cursor-pointer';
            card.className = 'p-3 rounded-xl border border-rose-500/40 bg-rose-500/10 text-center flex flex-col items-center gap-2 transition';
            logTerminal(`[PROTECCIÓN] Inversor #${index} disparado por interruptor de campo (OFF).`, 'text-rose-400 font-bold');
        }

        updateReadouts(true);
    };

    // Disparar Incidente Controlado con persistencia oficial
    window.triggerIncident = async function(type, targetDeficit) {
        if (type === 'inverter_failure') {
            activeInverters[0] = false;
            activeInverters[1] = false;
            toggleInverterVisual(1, false);
            toggleInverterVisual(2, false);
            logTerminal(`[INCIDENTE] Falla crítica forzada en Inversores #1 y #2 en ${farmName}.`, 'text-rose-400 font-bold');
        } else if (type === 'severe_storm') {
            setWeather('stormy', 180);
            logTerminal(`[INCIDENTE] Frente meteorológico severo inducido sobre ${farmName}.`, 'text-rose-400 font-bold');
        }

        updateReadouts(true);
        await persistCurrentSnapshot(targetDeficit, `Incidente simulado tipo ${type}`);
    };

    function toggleInverterVisual(index, active) {
        const btn = document.getElementById(`btnInverterToggle${index}`);
        const card = document.getElementById(`inverterCard${index}`);
        if (!btn || !card) return;

        if (active) {
            btn.textContent = 'ACTIVO';
            btn.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer';
            card.className = 'p-3 rounded-xl border border-emerald-500/40 bg-emerald-500/5 text-center flex flex-col items-center gap-2 transition';
        } else {
            btn.textContent = 'TRIPPED';
            btn.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition cursor-pointer';
            card.className = 'p-3 rounded-xl border border-rose-500/40 bg-rose-500/10 text-center flex flex-col items-center gap-2 transition';
        }
    }

    // Persistir Medición Actual en la Base de Datos oficial
    window.persistCurrentSnapshot = async function(overrideDeficit = null, notes = null) {
        const data = calculateTelemetry();
        const deficit = overrideDeficit !== null ? overrideDeficit : data.deficitPercent;
        const btnPersist = document.getElementById('btnPersistSnapshot');

        if (btnPersist) {
            btnPersist.disabled = true;
            btnPersist.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Guardando en BD...';
            window.lucide?.createIcons();
        }

        try {
            const response = await fetch("{{ route('simulator.event') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    solar_farm_id: farmId,
                    irradiance_wm2: currentIrradiance,
                    active_inverters: data.activeCount,
                    weather: currentWeather,
                    current_kw: data.currentKw,
                    deficit_percentage: deficit,
                    notes: notes
                })
            });

            if (!response.ok) {
                throw new Error('Error al registrar evento en servidor SCADA.');
            }

            const res = await response.json();
            logTerminal(`[BASE DE DATOS] ${res.message} - kWh: ${res.generation.real_kwh} | CO2: ${res.generation.co2_kg} kg`, 'text-emerald-400 font-bold');

            if (res.alert) {
                logTerminal(`[ALERTA REGISTRADA] Incidente RF-14 persistido en bandeja oficial (ID #${res.alert.id}, -${res.alert.deviation_percentage}%).`, 'text-rose-400 font-bold');
                window.refreshNotifications?.();
            }

        } catch (err) {
            logTerminal(`[ERROR DB] Fallo de persistencia: ${err.message}`, 'text-rose-500');
        } finally {
            if (btnPersist) {
                btnPersist.disabled = false;
                btnPersist.innerHTML = '<i data-lucide="save" class="w-4 h-4"></i> <span>Persistir Medición Actual en Base de Datos</span>';
                window.lucide?.createIcons();
            }
        }
    };

    // Restablecer valores nominales
    document.getElementById('btnResetNominal')?.addEventListener('click', async function() {
        activeInverters = [true, true, true, true];
        for (let i = 1; i <= 4; i++) {
            toggleInverterVisual(i, true);
        }
        setWeather('sunny', 1000);
        dismissAlarmBanner();

        logTerminal(`[COMANDO] Restablecimiento nominal emitido. Inversores al 100% y sol radiante.`, 'text-emerald-400 font-bold');

        try {
            await fetch("{{ route('simulator.reset') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ solar_farm_id: farmId })
            });
            logTerminal(`[BASE DE DATOS] Alertas resueltas y normalizadas para ${farmName}.`, 'text-emerald-300');
            window.refreshNotifications?.();
        } catch (e) {
            // Silencioso
        }

        updateReadouts(true);
    });

    // Utilidades de la Terminal SCADA
    function logTerminal(msg, colorClass = 'text-slate-300') {
        const term = document.getElementById('scadaTerminalLogs');
        if (!term) return;

        const line = document.createElement('div');
        line.className = colorClass;
        line.textContent = msg;
        term.appendChild(line);

        // Auto-scroll al final
        term.scrollTop = term.scrollHeight;
    }

    window.clearScadaTerminal = function() {
        const term = document.getElementById('scadaTerminalLogs');
        if (term) {
            term.innerHTML = '<div class="text-slate-500 font-mono">[TERMINAL LIMPIADA POR EL OPERADOR]</div>';
        }
    };

    window.copyTerminalLogs = function() {
        const term = document.getElementById('scadaTerminalLogs');
        if (!term) return;
        navigator.clipboard.writeText(term.innerText).then(() => {
            logTerminal('[CLIPBOARD] Logs de telemetría copiados al portapapeles.', 'text-sky-300');
        });
    };

    window.dismissAlarmBanner = function() {
        document.getElementById('scadaAlarmBanner')?.classList.add('hidden');
    };

    // Renderizado inicial de métricas
    updateReadouts(true);
</script>
@endpush
