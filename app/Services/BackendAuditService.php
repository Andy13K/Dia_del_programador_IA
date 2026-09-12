<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BackendAuditService
{
    public function record(Request $request, string $action, Model $model, ?int $userId = null): void
    {
        AuditLog::query()->create([
            'user_id' => $userId ?? $request->user()?->getAuthIdentifier(),
            'action' => $action,
            'model_type' => $model::class,
            'model_id' => $model->getKey(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr($request->userAgent() ?? '', 0, 500),
        ]);
    }
}
