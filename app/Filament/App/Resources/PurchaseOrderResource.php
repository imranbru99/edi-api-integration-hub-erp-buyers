<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PurchaseOrderResource\Pages;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('po_number')->required(),
            Forms\Components\Select::make('status')->options(['open' => 'Open', 'acknowledged' => 'Acknowledged', 'closed' => 'Closed']),
            Forms\Components\DatePicker::make('order_date'),
            Forms\Components\TextInput::make('total_amount')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('total_amount'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManagePurchaseOrders::route('/')];
    }
}
