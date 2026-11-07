<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Tenant;
use App\Services\Modules\ModuleRegistryService;
use Database\Seeders\ModuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ModuleActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_module_dependency_must_be_active_first(): void
    {
        $this->seed(ModuleSeeder::class);

        $tenant = Tenant::query()->create([
            'name' => 'Module Tenant',
            'slug' => 'module-tenant',
            'status' => 'active',
        ]);

        $registry = app(ModuleRegistryService::class);

        $this->expectException(ValidationException::class);
        $registry->activate($tenant, 'webhooks');
    }

    public function test_dependent_module_activates_when_parent_enabled(): void
    {
        $this->seed(ModuleSeeder::class);

        $tenant = Tenant::query()->create([
            'name' => 'Module Tenant B',
            'slug' => 'module-tenant-b',
            'status' => 'active',
        ]);

        $registry = app(ModuleRegistryService::class);
        $registry->activate($tenant, 'dashboard');
        $registry->activate($tenant, 'api_hub');

        $module = Module::query()->where('code', 'webhooks')->first();
        $this->assertNotNull($module);

        $tenantModule = $registry->activate($tenant, 'webhooks');
        $this->assertTrue($tenantModule->is_active);
    }
}
