<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Edi\Contracts\DeliveryGatewayInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeliverEdiJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly string $payload,
        public readonly int $tenantId,
        public readonly int $tradingPartnerId,
        public readonly string $documentType,
        public readonly array $metadata = [],
    ) {
        $this->onQueue('edi-delivery');
    }

    public function handle(DeliveryGatewayInterface $gateway): void
    {
        $gateway->deliver(
            $this->payload,
            $this->tenantId,
            $this->tradingPartnerId,
            $this->documentType,
            $this->metadata,
        );
    }
}
