<?php

declare(strict_types=1);

namespace App\Domain\Edi\Contracts;

interface DeliveryGatewayInterface
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function deliver(
        string $payload,
        int $tenantId,
        int $tradingPartnerId,
        string $documentType,
        array $metadata = [],
    ): array;
}
