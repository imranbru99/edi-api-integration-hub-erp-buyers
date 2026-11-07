<?php

namespace Tests\Feature;

use App\Models\EdiTransaction;
use App\Models\Tenant;
use App\Models\TradingPartner;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_edi_transactions_are_scoped_to_active_tenant_context(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'status' => 'active',
        ]);
        $tenantB = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'status' => 'active',
        ]);

        $partnerA = TradingPartner::withoutGlobalScopes()->create([
            'tenant_id' => $tenantA->id,
            'code' => 'A-PARTNER',
            'name' => 'Partner A',
            'status' => 'active',
        ]);
        $partnerB = TradingPartner::withoutGlobalScopes()->create([
            'tenant_id' => $tenantB->id,
            'code' => 'B-PARTNER',
            'name' => 'Partner B',
            'status' => 'active',
        ]);

        TenantContext::set($tenantA);
        EdiTransaction::query()->create([
            'tenant_id' => $tenantA->id,
            'trading_partner_id' => $partnerA->id,
            'direction' => 'inbound',
            'standard' => 'x12',
            'document_type' => '850',
            'status' => 'received',
        ]);

        TenantContext::set($tenantB);
        EdiTransaction::query()->create([
            'tenant_id' => $tenantB->id,
            'trading_partner_id' => $partnerB->id,
            'direction' => 'inbound',
            'standard' => 'x12',
            'document_type' => '850',
            'status' => 'received',
        ]);

        TenantContext::set($tenantA);
        $this->assertSame(1, EdiTransaction::query()->count());

        TenantContext::set($tenantB);
        $this->assertSame(1, EdiTransaction::query()->count());
    }
}
