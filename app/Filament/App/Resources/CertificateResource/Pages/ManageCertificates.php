<?php

namespace App\Filament\App\Resources\CertificateResource\Pages;

use App\Filament\App\Resources\CertificateResource;
use App\Models\Certificate;
use App\Services\Certificates\CertificateService;
use App\Support\TenantContext;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Actions\EditAction;

class ManageCertificates extends ManageRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->using(function (array $data) {
                $data['tenant_id'] = TenantContext::getId();
                $private = $data['private_key_plain'] ?? null;
                unset($data['private_key_plain'], $data['public_cert_upload']);

                return app(CertificateService::class)->store($data, null, $private);
            }),
        ];
    }

    protected function configureEditAction(EditAction $action): void
    {
        $action->mutateRecordDataUsing(function (array $data): array {
            unset($data['private_key_encrypted']);

            return $data;
        })->using(function (Certificate $record, array $data) {
            unset($data['private_key_plain'], $data['public_cert_upload'], $data['private_key_encrypted']);

            $record->update($data);

            return $record;
        });
    }
}
