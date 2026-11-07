<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApiClientResource\Pages;
use App\Models\ApiClient;
use App\Services\Api\ApiClientService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiClientResource extends Resource
{
    protected static ?string $model = ApiClient::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('client_id'),
            Forms\Components\TextInput::make('rate_limit')->numeric()->default(60),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('client_id'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('new_key')
                    ->label('Generate API key')
                    ->action(function (ApiClient $record) {
                        $result = app(ApiClientService::class)->generateKey($record);
                        Notification::make()
                            ->title('API key generated (shown once)')
                            ->body($result['plaintext'])
                            ->success()
                            ->persistent()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageApiClients::route('/')];
    }
}
