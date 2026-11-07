<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ModuleResource\Pages;
use App\Models\Module;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Platform';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')->required()->maxLength(255),
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('icon')->maxLength(255),
                Forms\Components\TextInput::make('version')->default('1.0.0'),
                Forms\Components\Toggle::make('is_core'),
                Forms\Components\Toggle::make('default_enabled'),
                Forms\Components\Toggle::make('is_available')->default(true),
                Forms\Components\TagsInput::make('dependencies'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\IconColumn::make('is_core')->boolean(),
                Tables\Columns\IconColumn::make('default_enabled')->boolean(),
                Tables\Columns\IconColumn::make('is_available')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
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
            'index' => Pages\ListModules::route('/'),
            'create' => Pages\CreateModule::route('/create'),
            'edit' => Pages\EditModule::route('/{record}/edit'),
        ];
    }
}
