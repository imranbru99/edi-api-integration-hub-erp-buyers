<?php

declare(strict_types=1);

namespace App\Domain\Edi\Services;

use App\Models\EdiTransaction;

final class IdempotencyService
{
    public function hash(string $rawContent, int $tradingPartnerId, string $documentType): string
    {
        return hash('sha256', implode('|', [
            $tradingPartnerId,
            strtoupper($documentType),
            $rawContent,
        ]));
    }

    public function isDuplicate(string $idempotencyKey, ?int $excludeTransactionId = null): bool
    {
        $query = EdiTransaction::query()->where('idempotency_key', $idempotencyKey);

        if ($excludeTransactionId !== null) {
            $query->where('id', '!=', $excludeTransactionId);
        }

        return $query->exists();
    }

    public function findExisting(string $idempotencyKey): ?EdiTransaction
    {
        return EdiTransaction::query()->where('idempotency_key', $idempotencyKey)->first();
    }
}
