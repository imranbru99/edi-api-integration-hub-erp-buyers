<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'icon',
        'version',
        'is_core',
        'default_enabled',
        'plan_restrictions',
        'dependencies',
        'sort_order',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'plan_restrictions' => 'array',
            'dependencies' => 'array',
            'is_core' => 'boolean',
            'default_enabled' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ModuleCategory::class, 'category_id');
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot(['is_active', 'activated_at', 'deactivated_at', 'settings'])
            ->withTimestamps();
    }
}
