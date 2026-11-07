<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleCategory;
use App\Services\Modules\ModuleCatalog;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $categoryMap = [];

        foreach (ModuleCatalog::categories() as $categoryData) {
            $category = ModuleCategory::query()->updateOrCreate(
                ['code' => $categoryData['code']],
                [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description'] ?? null,
                    'icon' => $categoryData['icon'] ?? null,
                    'sort_order' => $categoryData['sort_order'] ?? 0,
                ],
            );
            $categoryMap[$categoryData['code']] = $category->id;
        }

        foreach (ModuleCatalog::modules() as $moduleData) {
            Module::query()->updateOrCreate(
                ['code' => $moduleData['code']],
                [
                    'name' => $moduleData['name'],
                    'description' => $moduleData['description'] ?? null,
                    'category_id' => $categoryMap[$moduleData['category']] ?? null,
                    'icon' => $moduleData['icon'] ?? null,
                    'is_core' => $moduleData['is_core'] ?? false,
                    'default_enabled' => $moduleData['default_enabled'] ?? false,
                    'dependencies' => $moduleData['dependencies'] ?? [],
                    'sort_order' => $moduleData['sort_order'] ?? 0,
                    'is_available' => true,
                ],
            );
        }
    }
}
