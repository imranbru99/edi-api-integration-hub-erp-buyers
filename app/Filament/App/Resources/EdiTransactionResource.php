<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\EdiTransactionResource\Pages;
use App\Models\EdiTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EdiTransactionResource extends Resource
{
    protected static ?string $model = EdiTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'EDI Transactions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('document_type'),
            Forms\Components\Select::make('direction')->options(['inbound' => 'Inbound', 'outbound' => 'Outbound']),
            Forms\Components\Select::make('status')->options([
                'received' => 'Received',
                'processed' => 'Processed',
                'failed' => 'Failed',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('control_number')->searchable(),
                Tables\Columns\TextColumn::make('document_type'),
                Tables\Columns\TextColumn::make('direction')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('received_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $formatJson = fn ($state): string => match (true) {
            is_array($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '',
            is_string($state) => $state,
            default => '',
        };

        return $infolist->schema([
            Infolists\Components\Section::make('Summary')->schema([
                Infolists\Components\TextEntry::make('document_type'),
                Infolists\Components\TextEntry::make('standard'),
                Infolists\Components\TextEntry::make('direction'),
                Infolists\Components\TextEntry::make('status'),
                Infolists\Components\TextEntry::make('control_number'),
                Infolists\Components\TextEntry::make('tradingPartner.name')->label('Trading partner'),
            ])->columns(3),
            Infolists\Components\Tabs::make('Payload layers')
                ->tabs([
                    Infolists\Components\Tabs\Tab::make('Raw')
                        ->schema([
                            Infolists\Components\TextEntry::make('raw_content')
                                ->label('Raw interchange')
                                ->formatStateUsing(fn ($record) => $record->raw_content ?: $record->raw_payload)
                                ->columnSpanFull()
                                ->copyable(),
                        ]),
                    Infolists\Components\Tabs\Tab::make('Parsed')
                        ->schema([
                            Infolists\Components\TextEntry::make('parsed_content')
                                ->label('Parsed structure')
                                ->formatStateUsing(fn ($record) => $formatJson($record->parsed_content ?: $record->parsed_payload))
                                ->columnSpanFull()
                                ->copyable(),
                        ]),
                    Infolists\Components\Tabs\Tab::make('Canonical')
                        ->schema([
                            Infolists\Components\TextEntry::make('canonical_content')
                                ->label('Canonical document')
                                ->formatStateUsing(fn ($record) => $formatJson($record->canonical_content ?: $record->canonical_payload))
                                ->columnSpanFull()
                                ->copyable(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEdiTransactions::route('/'),
            'view' => Pages\ViewEdiTransaction::route('/{record}'),
        ];
    }
}
