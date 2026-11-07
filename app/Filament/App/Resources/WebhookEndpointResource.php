<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WebhookEndpointResource\Pages;
use App\Models\WebhookEndpoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebhookEndpointResource extends Resource
{
    protected static ?string $model = WebhookEndpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Webhooks';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->visible(fn () => in_array('name', ['url', 'name'], true) || 'WebhookEndpoint' !== 'WebhookEndpoint')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('url'),
                Tables\Columns\TextColumn::make('is_active'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWebhookEndpoints::route('/'),
        ];
    }
}