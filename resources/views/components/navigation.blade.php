@php
    $navigation = [
        ['label' => 'Panorama nacional', 'route' => auth()->check() ? 'dashboard' : 'home', 'match' => ['dashboard', 'home'], 'icon' => 'layout-dashboard', 'group' => 'EXPLORAR'],
        ['label' => 'Mapa solar', 'route' => 'map.index', 'match' => ['map.*'], 'icon' => 'map', 'group' => null],
        ['label' => 'Granjas solares', 'route' => 'farms.index', 'match' => ['farms.*'], 'icon' => 'sun-medium', 'group' => 'GESTIONAR'],
        ['label' => 'Paneles', 'route' => 'panels.index', 'match' => ['panels.*'], 'icon' => 'grid-2x2', 'group' => null],
        ['label' => 'Mediciones', 'route' => 'generations.index', 'match' => ['generations.*'], 'icon' => 'activity', 'group' => null],
        ['label' => 'Alertas', 'route' => 'alerts.index', 'match' => ['alerts.*'], 'icon' => 'bell', 'group' => 'ANALIZAR'],
        ['label' => 'Reportes', 'route' => 'reports.index', 'match' => ['reports.*'], 'icon' => 'chart-no-axes-combined', 'group' => null],
        ['label' => 'Proyecciones', 'route' => 'forecasts.index', 'match' => ['forecasts.*'], 'icon' => 'sparkles', 'group' => null],
        ['label' => 'API y documentación', 'route' => 'api.docs', 'match' => ['api.docs'], 'icon' => 'braces', 'group' => 'DESARROLLADORES'],
    ];

    if (auth()->check() && auth()->user()->can('manage-users')) {
        $navigation[] = ['label' => 'Usuarios y roles', 'route' => 'users.index', 'match' => ['users.*'], 'icon' => 'users', 'group' => 'ADMINISTRAR'];
    }
@endphp
<nav class="kin-navigation" aria-label="Navegación principal">
    @foreach($navigation as $item)
        @if($item['group'])<p class="kin-nav-group">{{ $item['group'] }}</p>@endif
        <a href="{{ route($item['route']) }}" @class(['kin-nav-link', 'is-active' => request()->routeIs(...$item['match'])]) @if(request()->routeIs(...$item['match'])) aria-current="page" @endif>
            <i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i><span>{{ $item['label'] }}</span>
            @if(request()->routeIs(...$item['match']))<span class="kin-nav-dot" aria-hidden="true"></span>@endif
        </a>
    @endforeach
</nav>
