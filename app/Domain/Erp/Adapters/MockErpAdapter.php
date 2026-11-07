<?php

namespace App\Domain\Erp\Adapters;

use App\Domain\Erp\Contracts\ErpConnectorInterface;

class MockErpAdapter implements ErpConnectorInterface
{
    protected array $config = [];

    /** @var array<int, array<string, mixed>> */
    protected array $purchaseOrders = [];

    /** @var array<int, array<string, mixed>> */
    protected array $shipments = [];

    public function connect(array $config): void
    {
        $this->config = $config;
    }

    public function testConnection(): bool
    {
        return true;
    }

    public function authenticate(): bool
    {
        return true;
    }

    public function fetchData(string $entity, array $filters = []): array
    {
        return match ($entity) {
            'purchase_orders' => array_values($this->purchaseOrders),
            'shipments' => array_values($this->shipments),
            default => [],
        };
    }

    public function sendData(string $entity, array $payload): array
    {
        if ($entity === 'purchase_orders') {
            $payload['id'] = count($this->purchaseOrders) + 1;
            $payload['status'] = 'accepted';
            $this->purchaseOrders[] = $payload;

            $shipment = [
                'id' => count($this->shipments) + 1,
                'po_number' => $payload['po_number'] ?? null,
                'status' => 'shipped',
                'items' => $payload['items'] ?? [],
            ];
            $this->shipments[] = $shipment;

            return ['purchase_order' => $payload, 'shipment' => $shipment];
        }

        return ['entity' => $entity, 'accepted' => true, 'payload' => $payload];
    }

    public function transformInbound(array $payload): array
    {
        return $payload;
    }

    public function transformOutbound(array $canonical): array
    {
        return $canonical;
    }

    public function getCapabilities(): array
    {
        return [
            'entities' => ['purchase_orders', 'shipments', 'invoices', 'inventory'],
            'directions' => ['inbound', 'outbound'],
        ];
    }

    public function getHealth(): array
    {
        return ['status' => 'healthy', 'latency_ms' => 5];
    }

    public function disconnect(): void
    {
        $this->config = [];
    }
}
