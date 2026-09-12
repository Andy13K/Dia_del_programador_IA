<!DOCTYPE html>
<html lang="es" class="antialiased">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Iniciar sesión — K'in Solar Guatemala</title>
    <link rel="icon" type="image/png" href="{{ asset('images/kin-icon-dorado.png') }}">
    <script>document.documentElement.classList.toggle('dark',localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && matchMedia('(prefers-color-scheme: dark)').matches));</script>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="kin-login">
    <section class="kin-login-story">
        <x-brand />
        <div><div class="kin-login-orbit" aria-hidden="true"></div><h1>Una fuente de energía.<br>Millones de posibilidades.</h1><p>Conectamos la energía del sol con el futuro de Guatemala. Cada medición cuenta.</p></div>
        <footer class="text-[10px] text-slate-400 mt-8">K'IN SOLAR GUATEMALA · 22 DEPARTAMENTOS</footer>
    </section>
    <section class="kin-login-form">
        <div>
            <div class="flex justify-between items-center mb-9"><span class="kin-eyebrow">BIENVENIDO A K'INSOLAR</span><button class="kin-icon-button" type="button" onclick="toggleDarkMode()" aria-label="Cambiar tema"><i data-lucide="sun" class="hidden dark:block"></i><i data-lucide="moon" class="dark:hidden"></i></button></div>
            <h2>Tu energía, en perspectiva.</h2>
            <p class="text-sm leading-relaxed" style="color:var(--kin-muted)">Inicia sesión para gestionar y dar seguimiento a la generación fotovoltaica.</p>
            @if(session('status'))<div class="kin-flash mt-5" role="status">{{ session('status') }}</div>@endif
            @if($errors->any())
                <div class="kin-flash kin-flash-error mt-5" role="alert"><i data-lucide="circle-alert"></i><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nombre@organizacion.gt" @error('email') aria-invalid="true" @enderror>
                <label for="password">Contraseña</label>
                <div class="kin-password"><input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña"><button type="button" class="kin-icon-button" aria-label="Mostrar contraseña" aria-pressed="false" onclick="const field=document.getElementById('password');const visible=field.type==='password';field.type=visible?'text':'password';this.setAttribute('aria-pressed',visible);this.setAttribute('aria-label',visible?'Ocultar contraseña':'Mostrar contraseña')"><i data-lucide="eye"></i></button></div>
                <x-button type="submit" class="w-full mt-7" size="lg" icon="arrow-right">Iniciar sesión</x-button>
            </form>
            <div class="kin-login-foot"><a href="{{ route('home') }}" class="kin-text-link">Explorar el panorama público <i data-lucide="arrow-up-right"></i></a><p class="mt-5">Plataforma Nacional de Monitoreo Fotovoltaico.<br>Universidad Mariano Gálvez · Puerto Barrios.</p></div>
        </div>
    </section>
</body>
</html>
