<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TradingPartnerResource\Pages;
use App\Models\TradingPartner;
use App\Services\Partners\TradingPartnerService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TradingPartnerResource extends Resource
{
    protected static ?string $model = TradingPartner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('code')->required(),
            Forms\Components\Select::make('partner_type')->options([
                'buyer' => 'Buyer',
                'supplier' => 'Supplier',
                '3pl' => '3PL',
            ]),
            Forms\Components\Select::make('status')->options([
                'draft' => 'Draft',
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]),
            Forms\Components\TextInput::make('isa_id')->label('ISA ID'),
            Forms\Components\TextInput::make('default_standard')->default('x12'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('onboarding_step'),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->action(fn (TradingPartner $record) => app(TradingPartnerService::class)->activate($record))
                    ->visible(fn (TradingPartner $record) => $record->status !== 'active'),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTradingPartners::route('/'),
        ];
    }

}
