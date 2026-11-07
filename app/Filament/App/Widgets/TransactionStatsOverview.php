<?php

namespace App\Filament\App\Widgets;

use App\Models\EdiTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $base = EdiTransaction::query();

        $total = (clone $base)->count();
        $inbound = (clone $base)->where('direction', 'inbound')->count();
        $outbound = (clone $base)->where('direction', 'outbound')->count();
        $failed = (clone $base)->whereIn('status', ['failed', 'quarantined'])->count();
        $completed = (clone $base)->where('status', 'completed')->count();

        return [
            Stat::make('EDI transactions', (string) $total)
                ->description("{$inbound} inbound · {$outbound} outbound"),
            Stat::make('Completed', (string) $completed)
                ->description('Successfully processed')
                ->color('success'),
            Stat::make('Failed / quarantined', (string) $failed)
                ->description('Needs attention')
                ->color($failed > 0 ? 'danger' : 'gray'),
        ];
    }
}
