<?php

declare(strict_types=1);

namespace App\Domain\Edi\Delivery;

use App\Domain\Edi\Contracts\DeliveryGatewayInterface;
use Illuminate\Support\Facades\Http;

final class RestDeliveryGateway implements DeliveryGatewayInterface
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
        $url = (string) ($metadata['endpoint'] ?? config('services.edi_delivery.endpoint', ''));
        if ($url === '') {
            return [
                'success' => false,
                'message' => 'REST delivery endpoint not configured.',
            ];
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/edi-x12',
                'X-EDI-Document-Type' => $documentType,
                'X-Tenant-Id' => (string) $tenantId,
                'X-Trading-Partner-Id' => (string) $tradingPartnerId,
            ])
            ->withBody($payload, 'application/edi-x12')
            ->post($url);

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }
}
