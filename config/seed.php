<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Contraseñas de los usuarios semilla (DatabaseSeeder)
    |--------------------------------------------------------------------------
    |
    | OWASP A02/A07: env() solo se llama aquí, nunca directamente en el seeder.
    | Fuera de config/, una llamada a env() deja de funcionar en cuanto se corre
    | `php artisan config:cache` (docs/07-PLAN-DESPLIEGUE.md lo ejecuta después
    | de cada despliegue) — Laravel congela la config y esas llamadas devuelven
    | null, silenciosamente.
    |
    | No hay valor de respaldo: si una variable no está definida, config()
    | devuelve null y DatabaseSeeder debe fallar explícitamente en vez de
    | sembrar con una contraseña predecible, incluso en desarrollo local.
    |
    */

    'admin_password' => env('SEED_ADMIN_PASSWORD'),
    'operador_password' => env('SEED_OPERADOR_PASSWORD'),
    'evaluador_password' => env('SEED_EVALUADOR_PASSWORD'),

];
