<?php

namespace App\Filament\App\Resources\TransactionErrorResource\Pages;

use App\Filament\App\Resources\TransactionErrorResource;
use App\Support\TenantContext;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransactionErrors extends ManageRecords
{
    protected static string $resource = TransactionErrorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                $data['tenant_id'] = TenantContext::getId();

                return $data;
            }),
        ];
    }
}
