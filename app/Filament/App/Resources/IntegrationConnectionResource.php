<?php

namespace App\Filament\App\Resources;

use App\Domain\Erp\ErpConnectorManager;
use App\Filament\App\Resources\IntegrationConnectionResource\Pages;
use App\Models\IntegrationConnection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IntegrationConnectionResource extends Resource
{
    protected static ?string $model = IntegrationConnection::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationLabel = 'ERP Connections';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('connector_type')->options([
                'mock' => 'Mock ERP',
                'json_api' => 'JSON API',
                'sap' => 'SAP',
                'netsuite' => 'NetSuite',
            ])->required(),
            Forms\Components\Select::make('status')->options(['inactive' => 'Inactive', 'active' => 'Active']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('connector_type'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('health_status'),
            ])
            ->actions([
                Tables\Actions\Action::make('test')
                    ->action(function (IntegrationConnection $record) {
                        $ok = app(ErpConnectorManager::class)->resolve($record)->testConnection();
                        Notification::make()
                            ->title($ok ? 'Connection healthy' : 'Connection failed')
                            ->success($ok)
                            ->danger(! $ok)
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageIntegrationConnections::route('/')];
    }
}
