<?php

namespace Tests\Feature;

use App\Domain\Edi\Services\EdiInboundProcessor;
use App\Models\Tenant;
use App\Models\TradingPartner;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EdiInboundFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_inbound_850_parses_validates_and_acknowledges(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Inbound Test Tenant',
            'slug' => 'inbound-test',
            'status' => 'active',
        ]);

        $partner = TradingPartner::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'code' => 'BUYER-850',
            'name' => '850 Buyer',
            'status' => 'active',
            'default_standard' => 'x12',
        ]);

        TenantContext::set($tenant);

        $raw = file_get_contents(base_path('tests/Fixtures/edi/x12_850_sample.edi'));
        $this->assertNotFalse($raw);

        $transaction = app(EdiInboundProcessor::class)->process(
            $raw,
            $tenant->id,
            $partner->id,
            '850',
        );

        $this->assertSame(EdiInboundProcessor::STATUS_COMPLETED, $transaction->status);
        $this->assertSame('850', $transaction->document_type);
        $this->assertNotNull($transaction->parsed_content);
        $this->assertNotNull($transaction->canonical_content);
        $this->assertNotNull($transaction->acknowledgment_id);
    }
}
