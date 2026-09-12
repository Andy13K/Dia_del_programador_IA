<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión — Sistema Solar Guatemala</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 relative overflow-hidden bg-slate-950">

    <!-- Luces ambientales de fondo -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 space-y-6">
        
        <!-- Logo y Título -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 via-amber-400 to-emerald-400 p-0.5 shadow-xl shadow-amber-500/20">
                <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                    <i data-lucide="sun" class="w-7 h-7 text-amber-400 animate-pulse"></i>
                </div>
            </div>
            <h1 class="text-2xl font-black tracking-tight text-white">
                Solar<span class="text-amber-400">GT</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Nube</span>
            </h1>
            <p class="text-xs text-slate-400 max-w-xs mx-auto">
                Sistema Nacional de Registro y Monitoreo de Generación Solar en los 22 Departamentos
            </p>
        </div>

        <!-- Tarjeta de Login -->
        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl shadow-slate-950/80 space-y-6">
            
            @if (session('status'))
                <div class="p-3 rounded-xl bg-emerald-950/50 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-950/50 border border-rose-800 text-rose-300 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        <span>No se pudo iniciar sesión:</span>
                    </div>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Correo Institucional
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="usuario@solarguatemala.gob.gt"
                               class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••••••"
                               class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl border border-slate-700 bg-slate-950 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Ingresar a la Plataforma</span>
                    </button>
                </div>
            </form>

            <!-- Acceso Rápido para Demostración / Jurado -->
            <div class="border-t border-slate-800 pt-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Cuentas de Demostración (1 Clic)
                    </span>
                    <span class="text-[10px] text-amber-400 font-semibold">Para el Jurado</span>
                </div>
                
                <div class="grid grid-cols-3 gap-2 text-center text-[11px]">
                    <button type="button" onclick="fillDemo('admin@solarguatemala.gob.gt', 'Solar2026!Admin')" 
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-amber-400 font-bold transition">
                        👑 Admin
                    </button>
                    <button type="button" onclick="fillDemo('operador@solarguatemala.gob.gt', 'Operador2026!')" 
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-sky-400 font-bold transition">
                        🛠️ Operador
                    </button>
                    <button type="button" onclick="fillDemo('evaluador@umg.edu.gt', 'Evaluador2026!')" 
                            class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-emerald-400 font-bold transition">
                        📊 Jurado UMG
                    </button>
                </div>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-amber-400 font-semibold transition flex items-center justify-center gap-1">
                    <span>Ver Dashboard Público como Visualizador</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

        </div>

        <!-- Pie de Seguridad OWASP -->
        <div class="text-center text-[10px] text-slate-500 space-y-1">
            <p>Protección contra fuerza bruta con limitación de tasa (OWASP A07: throttle:5,1).</p>
            <p>Universidad Mariano Gálvez de Guatemala • Sede Puerto Barrios</p>
        </div>

    </div>

    <script>
        lucide.createIcons();

        function fillDemo(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
