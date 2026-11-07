<?php

declare(strict_types=1);

namespace App\Domain\Edi\Canonical;

final class CanonicalShipment
{
    /**
     * @param  list<array<string, mixed>>  $lines
     */
    public function __construct(
        public readonly string $shipmentId,
        public readonly ?string $shipDate,
        public readonly array $lines = [],
        public readonly array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'shipment_id' => $this->shipmentId,
            'ship_date' => $this->shipDate,
            'lines' => $this->lines,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            shipmentId: (string) ($data['shipment_id'] ?? ''),
            shipDate: isset($data['ship_date']) ? (string) $data['ship_date'] : null,
            lines: $data['lines'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }
}
