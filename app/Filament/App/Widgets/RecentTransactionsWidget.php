<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\EdiTransactionResource;
use App\Models\EdiTransaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent EDI transactions';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EdiTransaction::query()->latest('id')->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('control_number')->label('Control #'),
                Tables\Columns\TextColumn::make('document_type')->badge(),
                Tables\Columns\TextColumn::make('direction')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('tradingPartner.name')->label('Partner'),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (EdiTransaction $record): string => EdiTransactionResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
