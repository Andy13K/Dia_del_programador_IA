<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMcpKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-MCP-Key');
        $expected = config('services.mcp.key');

        // OWASP A10: fallar cerrado. Si no hay llave configurada en el servidor,
        // NADIE pasa — nunca se debe interpretar "sin llave configurada" como "todo permitido".
        if (empty($expected) || empty($provided) || ! hash_equals($expected, $provided)) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 401);
        }

        return $next($request);
    }
}
