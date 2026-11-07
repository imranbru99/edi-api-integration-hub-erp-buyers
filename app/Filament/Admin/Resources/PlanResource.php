<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Platform';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')->required()->maxLength(255),
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->columnSpanFull(),
                Forms\Components\KeyValue::make('limits')->columnSpanFull(),
                Forms\Components\KeyValue::make('modules')->columnSpanFull(),
                Forms\Components\TextInput::make('price_monthly')->numeric()->prefix('$'),
                Forms\Components\TextInput::make('price_yearly')->numeric()->prefix('$'),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('price_monthly')->money('usd')->placeholder('—'),
                Tables\Columns\TextColumn::make('price_yearly')->money('usd')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
