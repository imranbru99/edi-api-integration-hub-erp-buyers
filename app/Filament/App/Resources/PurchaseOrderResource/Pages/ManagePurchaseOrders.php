<?php

namespace App\Filament\App\Resources\PurchaseOrderResource\Pages;

use App\Filament\App\Resources\PurchaseOrderResource;
use App\Services\Business\PurchaseOrderService;
use App\Support\TenantContext;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePurchaseOrders extends ManageRecords
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->using(function (array $data) {
                $data['tenant_id'] = TenantContext::getId();

                return app(PurchaseOrderService::class)->createFromCanonical($data);
            }),
        ];
    }
}
