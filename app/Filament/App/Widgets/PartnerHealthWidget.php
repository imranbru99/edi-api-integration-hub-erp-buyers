<?php

namespace App\Filament\App\Widgets;

use App\Models\TradingPartner;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
class PartnerHealthWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Trading partner health';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TradingPartner::query()
                    ->withCount([
                        'ediTransactions',
                        'ediTransactions as failed_transactions_count' => fn ($q) => $q->whereIn('status', ['failed', 'quarantined']),
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('edi_transactions_count')->label('Transactions'),
                Tables\Columns\TextColumn::make('failed_transactions_count')->label('Failed')
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success'),
            ])
            ->paginated([5, 10]);
    }
}
