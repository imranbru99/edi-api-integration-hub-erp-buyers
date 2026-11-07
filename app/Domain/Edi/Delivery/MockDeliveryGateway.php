<?php

declare(strict_types=1);

namespace App\Domain\Edi\Delivery;

use App\Domain\Edi\Contracts\DeliveryGatewayInterface;
use Illuminate\Support\Facades\Log;

final class MockDeliveryGateway implements DeliveryGatewayInterface
{
    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    public function deliver(
        string $payload,
        int $tenantId,
        int $tradingPartnerId,
        string $documentType,
        array $metadata = [],
    ): array {
        Log::info('EDI mock delivery', [
            'tenant_id' => $tenantId,
            'trading_partner_id' => $tradingPartnerId,
            'document_type' => $documentType,
            'bytes' => strlen($payload),
            'metadata' => $metadata,
        ]);

        return [
            'success' => true,
            'delivery_id' => 'mock-'.bin2hex(random_bytes(8)),
            'message' => 'Payload accepted by mock gateway.',
        ];
    }
}
