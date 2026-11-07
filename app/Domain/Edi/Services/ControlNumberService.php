<?php

declare(strict_types=1);

namespace App\Domain\Edi\Services;

use App\Models\EdiControlNumber;
use Illuminate\Support\Facades\DB;

final class ControlNumberService
{
    public function nextIsa(int $tenantId, int $tradingPartnerId): string
    {
        return $this->next($tenantId, $tradingPartnerId, 'isa', 9);
    }

    public function nextGs(int $tenantId, int $tradingPartnerId): string
    {
        return $this->next($tenantId, $tradingPartnerId, 'gs', 9);
    }

    public function nextSt(int $tenantId, int $tradingPartnerId): string
    {
        return $this->next($tenantId, $tradingPartnerId, 'st', 4);
    }

    public function nextInterchange(int $tenantId, int $tradingPartnerId): string
    {
        return $this->next($tenantId, $tradingPartnerId, 'interchange', 14);
    }

    private function next(int $tenantId, int $tradingPartnerId, string $type, int $padLength): string
    {
        return DB::transaction(function () use ($tenantId, $tradingPartnerId, $type, $padLength): string {
            $record = EdiControlNumber::query()
                ->where('tenant_id', $tenantId)
                ->where('trading_partner_id', $tradingPartnerId)
                ->where('number_type', $type)
                ->lockForUpdate()
                ->first();

            if ($record === null) {
                $record = EdiControlNumber::query()->create([
                    'tenant_id' => $tenantId,
                    'trading_partner_id' => $tradingPartnerId,
                    'standard' => 'x12',
                    'number_type' => $type,
                    'last_value' => 0,
                ]);
            }

            $next = (int) $record->last_value + 1;
            $record->update(['last_value' => $next]);

            return str_pad((string) $next, $padLength, '0', STR_PAD_LEFT);
        });
    }
}
