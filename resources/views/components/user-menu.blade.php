@props(['caret' => false])
@auth
    <div {{ $attributes->class(['kin-user-menu']) }}>
        <button type="button" class="kin-user-trigger" onclick="toggleUserMenu(this)" aria-haspopup="true" aria-expanded="false" aria-label="Cuenta de {{ auth()->user()->name }}, abrir menú">
            <span class="kin-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}</span>
            @if($caret)<i data-lucide="chevron-down" class="kin-user-caret" aria-hidden="true"></i>@endif
        </button>
        <div class="kin-user-popover" role="menu">
            <div class="kin-user-popover-info">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ ['admin' => 'Administrador', 'operador' => 'Operador', 'visualizador' => 'Visualizador'][auth()->user()->role] ?? 'Usuario' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="kin-user-logout" role="menuitem">
                    <i data-lucide="log-out" aria-hidden="true"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </div>
@else
    <span {{ $attributes->class(['kin-avatar']) }} title="Consulta pública">GT</span>
@endauth
