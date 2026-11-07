<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'legal_name',
        'organization_types',
        'email',
        'phone',
        'website',
        'country',
        'timezone',
        'currency',
        'logo_path',
        'branding',
        'status',
        'is_demo',
        'trial_ends_at',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'organization_types' => 'array',
            'branding' => 'array',
            'settings' => 'array',
            'is_demo' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant): void {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) Str::uuid();
            }
            if (empty($tenant->slug) && ! empty($tenant->name)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    public function members(): HasMany
    {
        return $this->hasMany(TenantMember::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['is_active', 'activated_at', 'deactivated_at', 'settings'])
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function usageRecords(): HasMany
    {
        return $this->hasMany(UsageRecord::class);
    }

    public function tradingPartners(): HasMany
    {
        return $this->hasMany(TradingPartner::class);
    }

    public function aiAuthorized(): bool
    {
        if ((bool) data_get($this->settings, 'ai.authorized', false)) {
            return true;
        }

        return AiProviderSetting::query()
            ->where('tenant_id', $this->id)
            ->where('authorized', true)
            ->exists();
    }
}
