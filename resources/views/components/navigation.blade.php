@php
    $navigation = [
        ['label' => 'Panorama nacional', 'route' => auth()->check() ? 'dashboard' : 'home', 'match' => ['dashboard', 'home'], 'icon' => 'layout-dashboard', 'group' => 'EXPLORAR', 'color' => 'text-amber-400'],
        ['label' => 'Mapa solar', 'route' => 'map.index', 'match' => ['map.*'], 'icon' => 'map', 'group' => null, 'color' => 'text-emerald-400'],
        ['label' => 'Granjas solares', 'route' => 'farms.index', 'match' => ['farms.*'], 'icon' => 'sun-medium', 'group' => 'GESTIONAR', 'color' => 'text-amber-400'],
        ['label' => 'Paneles', 'route' => 'panels.index', 'match' => ['panels.*'], 'icon' => 'grid-2x2', 'group' => null, 'color' => 'text-sky-400'],
        ['label' => 'Mediciones', 'route' => 'generations.index', 'match' => ['generations.*'], 'icon' => 'activity', 'group' => null, 'color' => 'text-emerald-400'],
        ['label' => 'Alertas', 'route' => 'alerts.index', 'match' => ['alerts.*'], 'icon' => 'bell', 'group' => 'ANALIZAR', 'color' => 'text-rose-400'],
        ['label' => 'Reportes', 'route' => 'reports.index', 'match' => ['reports.*'], 'icon' => 'chart-no-axes-combined', 'group' => null, 'color' => 'text-indigo-400'],
        ['label' => 'Proyecciones', 'route' => 'forecasts.index', 'match' => ['forecasts.*'], 'icon' => 'sparkles', 'group' => null, 'color' => 'text-teal-400'],
        ['label' => 'Simulador SCADA IoT', 'route' => 'simulator.index', 'match' => ['simulator.*'], 'icon' => 'radio', 'group' => 'LABORATORIO', 'color' => 'text-amber-500'],
        ['label' => 'API y documentación', 'route' => 'api.docs', 'match' => ['api.docs'], 'icon' => 'braces', 'group' => 'DESARROLLADORES', 'color' => 'text-violet-400'],
    ];

    if (auth()->check() && auth()->user()->can('manage-users')) {
        $navigation[] = ['label' => 'Usuarios y roles', 'route' => 'users.index', 'match' => ['users.*'], 'icon' => 'users', 'group' => 'ADMINISTRAR', 'color' => 'text-indigo-400'];
    }
@endphp
<nav class="kin-navigation" aria-label="Navegación principal">
    @foreach($navigation as $item)
        @if($item['group'])<p class="kin-nav-group">{{ $item['group'] }}</p>@endif
        <a href="{{ route($item['route']) }}" @class(['kin-nav-link', 'is-active' => request()->routeIs(...$item['match'])]) @if(request()->routeIs(...$item['match'])) aria-current="page" @endif>
            <i data-lucide="{{ $item['icon'] }}" class="{{ $item['color'] }}" aria-hidden="true"></i><span>{{ $item['label'] }}</span>
            @if(request()->routeIs(...$item['match']))<span class="kin-nav-dot" aria-hidden="true"></span>@endif
        </a>
    @endforeach
</nav>
