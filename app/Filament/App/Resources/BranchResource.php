<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('code')->maxLength(255),
                Forms\Components\TextInput::make('address_line_1')->maxLength(255),
                Forms\Components\TextInput::make('address_line_2')->maxLength(255),
                Forms\Components\TextInput::make('city')->maxLength(255),
                Forms\Components\TextInput::make('state')->maxLength(255),
                Forms\Components\TextInput::make('postal_code')->maxLength(255),
                Forms\Components\TextInput::make('country')->maxLength(2),
                Forms\Components\Toggle::make('is_headquarters'),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\IconColumn::make('is_headquarters')->boolean(),
                Tables\Columns\TextColumn::make('status')->badge(),
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
            'index' => Pages\ManageBranches::route('/'),
        ];
    }
}
