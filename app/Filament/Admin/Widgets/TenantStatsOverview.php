<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Tenant;
use App\Models\EdiTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenants = Tenant::query()->count();
        $active = Tenant::query()->where('status', 'active')->count();
        $demo = Tenant::query()->where('is_demo', true)->count();
        $ediVolume = EdiTransaction::withoutGlobalScopes()->count();

        return [
            Stat::make('Tenants', (string) $tenants)
                ->description("{$active} active · {$demo} demo"),
            Stat::make('Platform EDI volume', (string) $ediVolume)
                ->description('All tenants (unscoped)'),
        ];
    }
}
