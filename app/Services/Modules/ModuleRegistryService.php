<?php

namespace App\Services\Modules;

use App\Models\Module;
use App\Models\ModuleActivityLog;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Services\Audit\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ModuleRegistryService
{
    public function __construct(
        protected AuditLogger $auditLogger,
    ) {}

    public function activate(Tenant $tenant, string $moduleCode): TenantModule
    {
        $module = Module::query()->where('code', $moduleCode)->firstOrFail();

        $this->assertDependenciesActive($tenant, $module);

        $tenantModule = TenantModule::query()
            ->withoutGlobalScopes()
            ->updateOrCreate(
                ['tenant_id' => $tenant->id, 'module_id' => $module->id],
                [
                    'is_active' => true,
                    'activated_at' => now(),
                    'deactivated_at' => null,
                ],
            );

        $this->logActivity($tenant, $module, 'activated');

        $this->auditLogger->log('module.activated', $tenantModule, null, [
            'module_code' => $moduleCode,
        ], tenantId: $tenant->id);

        return $tenantModule;
    }

    public function deactivate(Tenant $tenant, string $moduleCode): TenantModule
    {
        $module = Module::query()->where('code', $moduleCode)->firstOrFail();

        if ($module->is_core) {
            throw ValidationException::withMessages([
                'module' => 'Core modules cannot be deactivated.',
            ]);
        }

        $this->assertNoDependentsActive($tenant, $module);

        $tenantModule = TenantModule::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('module_id', $module->id)
            ->firstOrFail();

        $tenantModule->update([
            'is_active' => false,
            'deactivated_at' => now(),
        ]);

        $this->logActivity($tenant, $module, 'deactivated');

        $this->auditLogger->log('module.deactivated', $tenantModule, null, [
            'module_code' => $moduleCode,
        ], tenantId: $tenant->id);

        return $tenantModule->fresh();
    }

    public function isActive(Tenant $tenant, string $moduleCode): bool
    {
        $module = Module::query()->where('code', $moduleCode)->first();

        if ($module === null) {
            return false;
        }

        return TenantModule::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('module_id', $module->id)
            ->where('is_active', true)
            ->exists();
    }

    protected function assertDependenciesActive(Tenant $tenant, Module $module): void
    {
        foreach ($module->dependencies ?? [] as $dependencyCode) {
            if (! $this->isActive($tenant, $dependencyCode)) {
                throw ValidationException::withMessages([
                    'module' => "Dependency module [{$dependencyCode}] must be active first.",
                ]);
            }
        }
    }

    protected function assertNoDependentsActive(Tenant $tenant, Module $module): void
    {
        $dependents = Module::query()
            ->whereJsonContains('dependencies', $module->code)
            ->pluck('code');

        foreach ($dependents as $dependentCode) {
            if ($this->isActive($tenant, $dependentCode)) {
                throw ValidationException::withMessages([
                    'module' => "Deactivate dependent module [{$dependentCode}] first.",
                ]);
            }
        }
    }

    protected function logActivity(Tenant $tenant, Module $module, string $action): void
    {
        ModuleActivityLog::query()->create([
            'tenant_id' => $tenant->id,
            'module_id' => $module->id,
            'user_id' => Auth::id(),
            'action' => $action,
        ]);
    }
}
