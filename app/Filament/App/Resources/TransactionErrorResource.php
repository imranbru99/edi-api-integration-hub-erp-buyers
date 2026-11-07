<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TransactionErrorResource\Pages;
use App\Models\TransactionError;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionErrorResource extends Resource
{
    protected static ?string $model = TransactionError::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'Error workbench';

    protected static ?string $modelLabel = 'transaction error';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('edi_transaction_id')
                ->relationship('ediTransaction', 'control_number')
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('error_code')->maxLength(64),
            Forms\Components\Select::make('severity')
                ->options([
                    'info' => 'Info',
                    'warning' => 'Warning',
                    'error' => 'Error',
                    'critical' => 'Critical',
                ])
                ->default('error')
                ->required(),
            Forms\Components\Textarea::make('message')->required()->columnSpanFull(),
            Forms\Components\KeyValue::make('context')->columnSpanFull(),
            Forms\Components\Toggle::make('resolved')->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ediTransaction.control_number')->label('Control #')->searchable(),
                Tables\Columns\TextColumn::make('error_code')->searchable(),
                Tables\Columns\TextColumn::make('severity')->badge(),
                Tables\Columns\TextColumn::make('message')->limit(60)->tooltip(fn ($record) => $record->message),
                Tables\Columns\IconColumn::make('resolved')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('resolved'),
                Tables\Filters\SelectFilter::make('severity')
                    ->options([
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'error' => 'Error',
                        'critical' => 'Critical',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('resolve')
                    ->visible(fn (TransactionError $record) => ! $record->resolved)
                    ->action(fn (TransactionError $record) => $record->update(['resolved' => true]))
                    ->color('success')
                    ->icon('heroicon-o-check'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactionErrors::route('/'),
        ];
    }
}
