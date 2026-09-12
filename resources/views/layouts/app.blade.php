<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Monitoreo Solar' }} — Sistema Nacional de Generación Solar Guatemala</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23F59E0B'><path d='M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41M12 7a5 5 0 100 10 5 5 0 000-10z'/></svg>">

    <!-- Tipografía Inter y Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS (Mapa Interactivo) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js para Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Vite Assets (Tailwind CSS v4 & JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
        [x-cloak] { display: none !important; }
        /* Transición suave para modo oscuro */
        * { transition: background-color 0.15s ease, border-color 0.15s ease; }
    </style>
</head>
<body class="h-full flex flex-col md:flex-row overflow-hidden">

    <!-- SIDEBAR DE NAVEGACIÓN -->
    <aside id="sidebar" class="w-full md:w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col z-30 border-r border-slate-800 transition-all duration-200">
        
        <!-- Logo / Marca -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-slate-800/80 bg-slate-950/40">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 via-amber-400 to-emerald-400 p-0.5 shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                        <i data-lucide="sun" class="w-5 h-5 text-amber-400 animate-pulse"></i>
                    </div>
                </div>
                <div>
                    <span class="text-base font-bold tracking-tight text-white flex items-center gap-1.5">
                        Solar<span class="text-amber-400">GT</span>
                        <span class="text-[10px] uppercase tracking-wider px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30">Nube</span>
                    </span>
                    <span class="text-[11px] text-slate-400 block -mt-0.5 font-medium">Monitoreo 22 Dptos</span>
                </div>
            </a>

            <!-- Botón cerrar en móvil -->
            <button type="button" class="md:hidden text-slate-400 hover:text-white" onclick="toggleSidebar()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Menú de Enlaces -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            
            <div class="px-3 pt-2 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-300">
                Principal
            </div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard', 'home') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 text-amber-400"></i>
                <span>Dashboard Nacional</span>
            </a>

            <!-- Mapa Interactivo (Obligatorio RF-13) -->
            <a href="{{ route('map.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('map.*') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="map-pin" class="w-4 h-4 text-emerald-400"></i>
                <span class="flex-1">Mapa de Guatemala</span>
                <span class="text-[10px] bg-emerald-500/20 text-emerald-300 font-bold px-1.5 py-0.5 rounded border border-emerald-500/30">GPS</span>
            </a>

            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-300">
                Gestión de Activos
            </div>

            <!-- Granjas Solares -->
            <a href="{{ route('farms.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('farms.*') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
                <span>Granjas Solares</span>
            </a>

            <!-- Catálogo de Paneles -->
            <a href="{{ route('panels.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('panels.*') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="grid" class="w-4 h-4 text-sky-400"></i>
                <span>Modelos de Panel</span>
            </a>

            <!-- Generación Energética -->
            <a href="{{ route('generations.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('generations.*') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="activity" class="w-4 h-4 text-emerald-400"></i>
                <span>Mediciones kWh & CO₂</span>
            </a>

            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-300">
                Analítica y Monitoreo
            </div>

            <!-- Alertas Automáticas (RF-14) -->
            <a href="{{ route('alerts.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('alerts.*') ? 'bg-rose-500/15 text-rose-400 font-semibold border-r-2 border-rose-500' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                <span class="flex-1">Alertas (Déficit ≥20%)</span>
            </a>

            <!-- Reportes Departamentales (RF-12) -->
            <a href="{{ route('reports.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-indigo-400"></i>
                <span>Reportes por Depto</span>
            </a>

            <!-- Proyecciones Climáticas (RF-15) -->
            <a href="{{ route('forecasts.index') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('forecasts.*') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="trending-up" class="w-4 h-4 text-teal-400"></i>
                <span>Proyecciones Futuras</span>
            </a>

            <div class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-300">
                Integración
            </div>

            <!-- API REST Documentada (RF-16) -->
            <a href="{{ route('api.docs') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('api.docs') ? 'bg-amber-500/15 text-amber-400 font-semibold border-r-2 border-amber-400' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                <i data-lucide="code-2" class="w-4 h-4 text-violet-400"></i>
                <span class="flex-1">API REST v1</span>
                <span class="text-[10px] bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded border border-slate-700">JSON</span>
            </a>
        </nav>

        <!-- Footer del Sidebar -->
        <div class="p-3 border-t border-slate-800/80 bg-slate-950/60">
            <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-3 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></div>
                    <div>
                        <div class="text-xs font-semibold text-white">AWS EC2 Online</div>
                        <div class="text-[10px] text-slate-300">PHP 8.3 • Nginx • MySQL</div>
                    </div>
                </div>
                <button type="button" onclick="toggleDarkMode()" title="Cambiar tema" class="p-1.5 text-slate-300 hover:text-amber-400 hover:bg-slate-800 rounded-lg">
                    <i data-lucide="moon" class="w-4 h-4 hidden dark:block"></i>
                    <i data-lucide="sun" class="w-4 h-4 block dark:hidden"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-slate-100 dark:bg-slate-950">
        
        <!-- TOPBAR -->
        <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 md:px-8 flex items-center justify-between flex-shrink-0 z-20">
            
            <div class="flex items-center gap-4">
                <button type="button" class="md:hidden text-slate-600 dark:text-slate-300" onclick="toggleSidebar()">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <div>
                    <h1 class="text-base md:text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        {{ $title ?? 'Monitoreo Solar' }}
                    </h1>
                </div>
            </div>

            <!-- Indicadores Rápidos en Topbar -->
            <div class="flex items-center gap-3">
                
                <!-- Badge de Cobertura -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 text-xs font-semibold">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                    <span>22 Departamentos Cubiertos</span>
                </div>

                <!-- Factor CO2 Oficial -->
                <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 text-xs font-semibold">
                    <i data-lucide="leaf" class="w-3.5 h-3.5 text-amber-500"></i>
                    <span>Factor Oficial: 0.40 kg CO₂/kWh</span>
                </div>

                <!-- Botón de Modo Oscuro -->
                <button type="button" onclick="toggleDarkMode()" class="p-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i data-lucide="moon" class="w-5 h-5 hidden dark:block"></i>
                    <i data-lucide="sun" class="w-5 h-5 block dark:hidden"></i>
                </button>

                <!-- Usuario / Rol -->
                <div class="flex items-center gap-2.5 pl-3 border-l border-slate-200 dark:border-slate-800">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                        {{ substr(auth()->user()->name ?? 'Andy & Carlos', 0, 2) }}
                    </div>
                    <div class="hidden md:block text-left">
                        <div class="text-xs font-bold text-slate-800 dark:text-white leading-tight">
                            {{ auth()->user()->name ?? 'Andy & Carlos' }}
                        </div>
                        <div class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 leading-tight">
                            {{ auth()->user()->role ?? 'Equipo Competencia' }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ÁREA DE CONTENIDO CON SCROLL -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            
            <!-- MENSAJES FLASH (Alertas de sesión) -->
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 shadow-sm animate-in fade-in">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 shadow-sm">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 flex-shrink-0"></i>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 shadow-sm">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500 flex-shrink-0"></i>
                    <div class="text-sm font-medium">{{ session('warning') }}</div>
                </div>
            @endif

            <!-- Slot principal o Yield -->
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- SCRIPTS BASE -->
    <script>
        // Inicializar iconos Lucide
        lucide.createIcons();

        // Modo Oscuro con LocalStorage
        function initTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        initTheme();

        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            lucide.createIcons();
        }

        // Sidebar móvil
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    @stack('scripts')
</body>
</html>
