<?php

namespace App\Filament\App\Resources\WebhookEndpointResource\Pages;

use App\Filament\App\Resources\WebhookEndpointResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWebhookEndpoints extends ManageRecords
{
    protected static string $resource = WebhookEndpointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}