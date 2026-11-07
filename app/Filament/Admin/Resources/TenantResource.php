<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Platform';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('slug')->required()->maxLength(255),
                Forms\Components\TextInput::make('legal_name')->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->tel()->maxLength(255),
                Forms\Components\TextInput::make('website')->url()->maxLength(255),
                Forms\Components\TextInput::make('country')->maxLength(2),
                Forms\Components\TextInput::make('timezone')->default('UTC'),
                Forms\Components\TextInput::make('currency')->default('USD')->maxLength(3),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_demo'),
                Forms\Components\DateTimePicker::make('trial_ends_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\IconColumn::make('is_demo')->boolean(),
                Tables\Columns\TextColumn::make('trial_ends_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
