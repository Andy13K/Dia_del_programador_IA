<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Registra una acción crítica del sistema en audit_logs (OWASP A09).
     *
     * @param array<string, mixed>|null $payload
     */
    public function log(string $action, ?string $modelType = null, ?int $modelId = null, ?array $payload = null): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }
}
