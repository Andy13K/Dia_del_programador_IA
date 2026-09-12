<?php

use App\Http\Middleware\SecurityHeaders;
use App\Services\AuditService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // OWASP A09: registrar cada intento de acceso denegado (403) en audit_logs.
        // Gate::authorize() lanza AuthorizationException, pero Handler::prepareException()
        // ya la convierte a AccessDeniedHttpException antes de llegar a los renderable
        // callbacks (por eso se captura este tipo y no el original). Devolver null deja
        // que Laravel siga con el render 403 normal (resources/views/errors/403.blade.php).
        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            app(AuditService::class)->log('acceso_denegado', payload: [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            return null;
        });
    })->create();
