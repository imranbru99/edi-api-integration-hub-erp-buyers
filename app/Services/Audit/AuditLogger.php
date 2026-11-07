<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public function log(
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        ?int $tenantId = null,
    ): AuditLog {
        return AuditLog::query()->create([
            'tenant_id' => $tenantId ?? TenantContext::getId(),
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? $auditable::class : null,
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
