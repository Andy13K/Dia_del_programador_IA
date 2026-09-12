<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Cabeceras de seguridad obligatorias (docs/02-SEGURIDAD-OWASP-2025.md, A02).
     *
     * La CSP es deliberadamente permisiva en script-src/style-src/img-src para no
     * romper Leaflet, Chart.js, Lucide y las fuentes de Google que ya usa el layout
     * (resources/views/layouts/app.blade.php) mientras siguen sirviéndose desde CDN
     * en vez de empaquetados con Vite (ver hallazgo A03/A08 en el PR).
     *
     * 'unsafe-inline' en script-src es una concesión conocida: el dashboard y el mapa
     * inicializan Chart.js/Leaflet con `<script>` inline que imprime datos del servidor
     * (@json(...)) directamente en las vistas de Agente C. Sin nonces por request (no
     * implementados todavía) bloquearlos rompe el dashboard y el mapa. Documentado como
     * pendiente en el PR: la solución correcta es un nonce por request o mover esa
     * inicialización a resources/js/app.js empaquetado por Vite.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Content-Security-Policy', implode(' ', [
            "default-src 'self';",
            "script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net;",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com;",
            "font-src 'self' https://fonts.gstatic.com data:;",
            "img-src 'self' data: https:;",
            "connect-src 'self';",
            "frame-ancestors 'self';",
        ]));

        return $response;
    }
}
