<?php

namespace App\Models\Concerns;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::creating(function ($model): void {
            if (empty($model->tenant_id) && TenantContext::getId()) {
                $model->tenant_id = TenantContext::getId();
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenantId = TenantContext::getId();
            if ($tenantId !== null) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
