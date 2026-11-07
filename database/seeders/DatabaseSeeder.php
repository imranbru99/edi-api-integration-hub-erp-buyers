<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            ModuleSeeder::class,
            PlanSeeder::class,
        ]);

        if (app()->environment('local', 'testing') || (bool) config('edi-hub.demo_mode')) {
            $password = env('DEMO_PASSWORD', 'DemoPass123!');

            $admin = User::query()->firstOrCreate(
                ['email' => env('DEMO_PLATFORM_ADMIN_EMAIL', 'demo.platform@edi-hub.test')],
                [
                    'name' => 'Demo Platform Administrator',
                    'password' => Hash::make($password),
                    'is_platform_admin' => true,
                    'platform_role' => 'super_admin',
                    'is_active' => true,
                ],
            );

            if (method_exists($admin, 'assignRole')) {
                $admin->assignRole('platform_super_admin');
            }

            $this->call(DemoSeeder::class);
        }
    }
}
