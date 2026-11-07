<?php

namespace App\Filament\App\Resources\ApiClientResource\Pages;

use App\Filament\App\Resources\ApiClientResource;
use App\Services\Api\ApiClientService;
use App\Support\TenantContext;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageApiClients extends ManageRecords
{
    protected static string $resource = ApiClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->using(function (array $data) {
                $data['tenant_id'] = TenantContext::getId();
                $result = app(ApiClientService::class)->createClient($data);
                Notification::make()
                    ->title('Client secret (shown once)')
                    ->body($result['client_secret'])
                    ->success()
                    ->persistent()
                    ->send();

                return $result['client'];
            }),
        ];
    }
}
