<?php

namespace App\Filament\App\Resources\IntegrationConnectionResource\Pages;

use App\Filament\App\Resources\IntegrationConnectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIntegrationConnections extends ManageRecords
{
    protected static string $resource = IntegrationConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}