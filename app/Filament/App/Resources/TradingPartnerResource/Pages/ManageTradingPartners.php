<?php

namespace App\Filament\App\Resources\TradingPartnerResource\Pages;

use App\Filament\App\Resources\TradingPartnerResource;
use App\Services\Partners\TradingPartnerService;
use App\Support\TenantContext;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTradingPartners extends ManageRecords
{
    protected static string $resource = TradingPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data) {
                    $data['tenant_id'] = TenantContext::getId();

                    return app(TradingPartnerService::class)->create($data);
                }),
        ];
    }
}
