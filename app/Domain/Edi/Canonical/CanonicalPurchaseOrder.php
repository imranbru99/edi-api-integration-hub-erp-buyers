<?php

declare(strict_types=1);

namespace App\Domain\Edi\Canonical;

final class CanonicalPurchaseOrder
{
    /**
     * @param  list<array<string, mixed>>  $lines
     */
    public function __construct(
        public readonly string $poNumber,
        public readonly ?string $orderDate,
        public readonly ?string $purposeCode,
        public readonly array $lines = [],
        public readonly array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'po_number' => $this->poNumber,
            'order_date' => $this->orderDate,
            'purpose_code' => $this->purposeCode,
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
            poNumber: (string) ($data['po_number'] ?? ''),
            orderDate: isset($data['order_date']) ? (string) $data['order_date'] : null,
            purposeCode: isset($data['purpose_code']) ? (string) $data['purpose_code'] : null,
            lines: $data['lines'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }
}
