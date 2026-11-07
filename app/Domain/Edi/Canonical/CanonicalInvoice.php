<?php

declare(strict_types=1);

namespace App\Domain\Edi\Canonical;

final class CanonicalInvoice
{
    /**
     * @param  list<array<string, mixed>>  $lines
     */
    public function __construct(
        public readonly string $invoiceNumber,
        public readonly ?string $poNumber,
        public readonly ?string $invoiceDate,
        public readonly ?string $totalAmount,
        public readonly array $lines = [],
        public readonly array $metadata = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'invoice_number' => $this->invoiceNumber,
            'po_number' => $this->poNumber,
            'invoice_date' => $this->invoiceDate,
            'total_amount' => $this->totalAmount,
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
            invoiceNumber: (string) ($data['invoice_number'] ?? ''),
            poNumber: isset($data['po_number']) ? (string) $data['po_number'] : null,
            invoiceDate: isset($data['invoice_date']) ? (string) $data['invoice_date'] : null,
            totalAmount: isset($data['total_amount']) ? (string) $data['total_amount'] : null,
            lines: $data['lines'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }
}
