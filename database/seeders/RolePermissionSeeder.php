<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $platformPermissions = [
            'platform.tenants.view',
            'platform.tenants.manage',
            'platform.plans.manage',
            'platform.modules.manage',
            'platform.users.manage',
            'platform.audit.view',
        ];

        $tenantPermissions = [
            'tenant.settings.view',
            'tenant.settings.manage',
            'tenant.users.view',
            'tenant.users.manage',
            'tenant.branches.manage',
            'tenant.partners.manage',
            'tenant.edi.view',
            'tenant.edi.manage',
            'tenant.modules.manage',
            'tenant.billing.view',
        ];

        foreach (array_merge($platformPermissions, $tenantPermissions) as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'platform_super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($platformPermissions);

        $platformSupport = Role::query()->firstOrCreate(['name' => 'platform_support', 'guard_name' => 'web']);
        $platformSupport->syncPermissions([
            'platform.tenants.view',
            'platform.audit.view',
        ]);

        $tenantOwner = Role::query()->firstOrCreate(['name' => 'tenant_owner', 'guard_name' => 'web']);
        $tenantOwner->syncPermissions($tenantPermissions);

        $tenantAdmin = Role::query()->firstOrCreate(['name' => 'tenant_admin', 'guard_name' => 'web']);
        $tenantAdmin->syncPermissions(array_values(array_filter(
            $tenantPermissions,
            fn (string $p) => $p !== 'tenant.billing.view',
        )));

        $tenantMember = Role::query()->firstOrCreate(['name' => 'tenant_member', 'guard_name' => 'web']);
        $tenantMember->syncPermissions([
            'tenant.settings.view',
            'tenant.edi.view',
            'tenant.partners.manage',
        ]);

        $partnerUser = Role::query()->firstOrCreate(['name' => 'partner_user', 'guard_name' => 'web']);
        $partnerUser->syncPermissions([
            'tenant.edi.view',
        ]);

        $developer = Role::query()->firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $developer->syncPermissions([
            'tenant.edi.view',
            'tenant.modules.manage',
        ]);
    }
}
