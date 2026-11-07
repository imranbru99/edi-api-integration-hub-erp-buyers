<?php

namespace App\Filament\App\Resources\ShipmentResource\Pages;

use App\Filament\App\Resources\ShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageShipments extends ManageRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}