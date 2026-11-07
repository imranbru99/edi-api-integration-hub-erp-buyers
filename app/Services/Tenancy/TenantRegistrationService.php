<?php

namespace App\Services\Tenancy;

use App\Models\Module;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\TenantMember;
use App\Models\TenantModule;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantRegistrationService
{
    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $tenantData
     * @param  array<string, mixed>  $ownerData
     */
    public function register(array $tenantData, array $ownerData, ?string $planCode = 'starter'): Tenant
    {
        return DB::transaction(function () use ($tenantData, $ownerData, $planCode) {
            $trialDays = (int) config('edi-hub.demo.trial_days', 14);

            $tenant = Tenant::query()->create([
                'name' => $tenantData['name'],
                'slug' => $tenantData['slug'] ?? Str::slug($tenantData['name']),
                'legal_name' => $tenantData['legal_name'] ?? null,
                'email' => $tenantData['email'] ?? $ownerData['email'] ?? null,
                'status' => 'active',
                'is_demo' => (bool) ($tenantData['is_demo'] ?? config('edi-hub.demo.enabled', false)),
                'trial_ends_at' => now()->addDays($trialDays),
                'settings' => $tenantData['settings'] ?? [],
            ]);

            $owner = User::query()->create([
                'name' => $ownerData['name'],
                'email' => $ownerData['email'],
                'password' => $ownerData['password'],
                'current_tenant_id' => $tenant->id,
                'is_active' => true,
            ]);

            TenantMember::query()->create([
                'tenant_id' => $tenant->id,
                'user_id' => $owner->id,
                'role_code' => 'owner',
                'is_owner' => true,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            $this->seedDefaultModules($tenant);

            $plan = Plan::query()->where('code', $planCode)->first()
                ?? Plan::query()->where('code', 'starter')->first();

            if ($plan) {
                Subscription::query()->create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'status' => 'trialing',
                    'trial_ends_at' => $tenant->trial_ends_at,
                    'starts_at' => now(),
                ]);
            }

            $this->auditLogger->log('tenant.registered', $tenant, null, $tenant->toArray());

            return $tenant->fresh();
        });
    }

    protected function seedDefaultModules(Tenant $tenant): void
    {
        $modules = Module::query()
            ->where(function ($query): void {
                $query->where('default_enabled', true)
                    ->orWhere('is_core', true);
            })
            ->get();

        foreach ($modules as $module) {
            TenantModule::query()->create([
                'tenant_id' => $tenant->id,
                'module_id' => $module->id,
                'is_active' => true,
                'activated_at' => now(),
            ]);
        }
    }
}
