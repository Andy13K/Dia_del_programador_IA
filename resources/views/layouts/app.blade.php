<!DOCTYPE html>
<html lang="es" class="h-full antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0B0F17">
    <title>{{ $title ?? 'Panorama nacional' }} — K'in Solar Guatemala</title>
    <link rel="icon" type="image/png" href="{{ asset('images/kin-icon-dorado.png') }}">
    <script>
        const savedTheme = localStorage.getItem('theme');
        document.documentElement.classList.toggle('dark', savedTheme ? savedTheme === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches);
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    {{-- OWASP A03/A08: versión fija + integrity (SRI), igual que Leaflet arriba. "@latest" y la
         URL de chart.js sin versión podían cambiar de contenido sin que el equipo lo notara. --}}
    <script src="https://unpkg.com/lucide@1.45.0/dist/umd/lucide.min.js" integrity="sha384-1K5mjRr9EvBwlp0ALeQf1DEysXoqYs+nRQmDBi/UrEty5RgCjPRph7NJZkRjAFWJ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js" integrity="sha384-jb8JQMbMoBUzgWatfe6COACi2ljcDdZQ2OxczGA3bGNeWe+6DChMTBJemed7ZnvJ" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="kin-app" data-page="{{ request()->route()?->getName() }}">
    <a href="#main-content" class="kin-skip">Saltar al contenido</a>
    <aside id="sidebar" class="kin-sidebar">
        <div class="kin-sidebar-brand"><x-brand /></div>
        <x-navigation />
        <div class="kin-sidebar-bottom">
            <div class="kin-country"><span aria-hidden="true">◧</span> Guatemala <span>22 departamentos</span></div>
            @auth
                <div class="kin-profile">
                    <span class="kin-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
                    <div><strong>{{ auth()->user()->name }}</strong><span>{{ ['admin' => 'Administrador', 'operador' => 'Operador', 'visualizador' => 'Visualizador'][auth()->user()->role] ?? 'Usuario' }}</span></div>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="kin-icon-button" title="Cerrar sesión" aria-label="Cerrar sesión"><i data-lucide="log-out"></i></button></form>
                </div>
            @else
                <x-button :href="route('login')" variant="primary" class="w-full" icon="log-in">Iniciar sesión</x-button>
            @endauth
        </div>
    </aside>
    <div class="kin-workspace">
        <header class="kin-topbar">
            <div class="kin-breadcrumb"><span class="hidden sm:inline">Plataforma nacional</span><span class="hidden sm:inline kin-divider">/</span><strong>{{ $title ?? 'Panorama nacional' }}</strong></div>
            <div class="kin-topbar-actions">
                <span class="kin-live"><span aria-hidden="true"></span><span class="hidden sm:inline">Conectado</span></span>
                <button type="button" class="kin-icon-button kin-theme-toggle" onclick="toggleDarkMode()" aria-label="Cambiar tema" title="Cambiar tema"><i data-lucide="sun" class="hidden dark:block"></i><i data-lucide="moon" class="dark:hidden"></i></button>
                @auth<a href="{{ route('alerts.index') }}" class="kin-icon-button" aria-label="Ver alertas"><i data-lucide="bell"></i></a>@endauth
                <span class="kin-avatar kin-top-avatar" title="{{ auth()->user()->name ?? 'Consulta pública' }}">{{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'GT', 0, 2)) }}</span>
            </div>
        </header>
        <main id="main-content" tabindex="-1" @class(['kin-main', 'kin-map-main' => request()->routeIs('map.*')])>
            @foreach(['success' => 'check-circle', 'error' => 'circle-alert', 'warning' => 'triangle-alert', 'status' => 'info'] as $flash => $icon)
                @if(session($flash))
                    <div class="kin-flash kin-flash-{{ $flash }}" role="{{ $flash === 'error' ? 'alert' : 'status' }}"><i data-lucide="{{ $icon }}" aria-hidden="true"></i><span>{{ session($flash) }}</span></div>
                @endif
            @endforeach
            {{ $slot ?? '' }}
            @yield('content')
            @unless(request()->routeIs('map.*'))<footer class="kin-page-footer"><span>K'in Solar Guatemala</span><span>Plataforma Nacional de Monitoreo Fotovoltaico</span></footer>@endunless
        </main>
    </div>
    <nav id="mobileBottomNav" aria-label="Navegación móvil">
        @foreach([['home','Inicio','layout-dashboard',['home','dashboard']],['map.index','Mapa','map',['map.*']],['farms.index','Granjas','sun-medium',['farms.*']],['alerts.index','Alertas','bell',['alerts.*']]] as [$route,$label,$icon,$matches])
            <a href="{{ route($route) }}" @class(['kin-bottom-link','is-active' => request()->routeIs(...$matches)]) @if(request()->routeIs(...$matches)) aria-current="page" @endif><i data-lucide="{{ $icon }}" aria-hidden="true"></i><span>{{ $label }}</span></a>
        @endforeach
        <button type="button" class="kin-bottom-link" onclick="openMobileDrawer()" aria-controls="mobileDrawer" aria-expanded="false"><i data-lucide="menu" aria-hidden="true"></i><span>Más</span></button>
    </nav>
    <div id="mobileDrawerBackdrop" class="kin-drawer-backdrop" onclick="closeMobileDrawer()" aria-hidden="true"></div>
    <section id="mobileDrawer" class="kin-drawer" role="dialog" aria-modal="true" aria-label="Menú principal" aria-hidden="true" inert>
        <div class="kin-drawer-heading"><x-brand :compact="true"/><button type="button" class="kin-icon-button" onclick="closeMobileDrawer()" aria-label="Cerrar menú"><i data-lucide="x"></i></button></div>
        <x-navigation />
        <div class="kin-drawer-footer">
            @auth
                <div class="kin-profile"><span class="kin-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span><div><strong>{{ auth()->user()->name }}</strong><span>{{ ['admin'=>'Administrador','operador'=>'Operador','visualizador'=>'Visualizador'][auth()->user()->role] ?? 'Usuario' }}</span></div></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<x-button type="submit" variant="danger" icon="log-out" class="w-full">Cerrar sesión</x-button></form>
            @else
                <x-button :href="route('login')" class="w-full" icon="log-in">Iniciar sesión</x-button>
            @endauth
        </div>
    </section>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
