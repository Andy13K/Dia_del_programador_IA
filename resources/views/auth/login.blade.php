<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Iniciar sesión — {{ config('app.name', 'Solar Guatemala') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css'])
        @else
            {{-- Estilo mínimo de respaldo: Agente C reemplazará esto con el sistema de diseño
                 definitivo (docs/06-CONTRATOS-HORA-1.md §4) cuando compile los assets. --}}
            <style>
                * { box-sizing: border-box; }
                body {
                    margin: 0;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #F8FAFC;
                    font-family: ui-sans-serif, system-ui, sans-serif;
                    color: #0F172A;
                }
                .card {
                    width: 100%;
                    max-width: 24rem;
                    background: #FFFFFF;
                    border: 1px solid #E2E8F0;
                    border-radius: 12px;
                    padding: 2rem;
                    box-shadow: 0 1px 3px 0 rgba(0,0,0,.1);
                }
                h1 { font-size: 1.25rem; margin: 0 0 .25rem; }
                p.subtitle { margin: 0 0 1.5rem; color: #64748B; font-size: .875rem; }
                label { display: block; font-size: .875rem; font-weight: 500; margin-bottom: .25rem; }
                input {
                    width: 100%;
                    padding: .5rem .75rem;
                    border: 1px solid #E2E8F0;
                    border-radius: 8px;
                    font-size: .875rem;
                    margin-bottom: 1rem;
                }
                input:focus { outline: 2px solid #F59E0B; outline-offset: 1px; }
                .error { color: #DC2626; font-size: .8rem; margin: -.75rem 0 1rem; }
                button {
                    width: 100%;
                    padding: .625rem 1rem;
                    background: #D97706;
                    color: #FFFFFF;
                    border: none;
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: .875rem;
                    cursor: pointer;
                }
                button:hover { background: #B45309; }
                .status { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; border-radius: 8px; padding: .5rem .75rem; font-size: .8rem; margin-bottom: 1rem; }
            </style>
        @endif
    </head>
    <body>
        <div class="card">
            <h1>Sistema Solar Guatemala</h1>
            <p class="subtitle">Inicia sesión para continuar</p>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                @csrf

                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror

                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror

                <button type="submit">Iniciar sesión</button>
            </form>
        </div>
    </body>
</html>
