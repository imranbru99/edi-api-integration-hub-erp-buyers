<?php

namespace App\Policies;

use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;

abstract class TenantScopedPolicy
{
    protected function belongsToCurrentTenant(?Model $model): bool
    {
        if ($model === null || ! isset($model->tenant_id)) {
            return false;
        }

        $tenantId = TenantContext::getId();

        return $tenantId !== null && (int) $model->tenant_id === (int) $tenantId;
    }

    protected function userBelongsToTenant(User $user): bool
    {
        $tenantId = TenantContext::getId() ?? $user->current_tenant_id;

        if ($tenantId === null) {
            return false;
        }

        return $user->tenantMembers()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->exists();
    }
}
