<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('type')->options(['x509' => 'X.509'])->default('x509'),
            Forms\Components\FileUpload::make('public_cert_upload')->label('Public certificate')->disk('local'),
            Forms\Components\Placeholder::make('private_key_stored')
                ->label('Private key')
                ->content('Stored encrypted. Rotate by creating a new certificate record.')
                ->visibleOn('edit'),
            Forms\Components\Textarea::make('private_key_plain')
                ->label('Private key')
                ->password()
                ->revealable()
                ->visibleOn('create')
                ->dehydrated(fn (string $operation): bool => $operation === 'create'),
            Forms\Components\Select::make('status')->options(['active' => 'Active', 'revoked' => 'Revoked']),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('fingerprint')->limit(16),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('not_after')->dateTime(),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageCertificates::route('/')];
    }
}
