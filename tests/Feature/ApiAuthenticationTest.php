<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\Tenant;
use App\Models\TradingPartner;
use App\Services\Api\ApiClientService;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_api_key_can_list_trading_partners(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'API Tenant',
            'slug' => 'api-tenant',
            'status' => 'active',
        ]);

        TenantContext::set($tenant);

        TradingPartner::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'code' => 'API-BUYER',
            'name' => 'API Buyer',
            'status' => 'active',
        ]);

        $client = ApiClient::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Test Client',
            'client_id' => 'cli_test',
            'client_secret_hash' => Hash::make('unused-secret'),
            'is_active' => true,
            'rate_limit' => 120,
        ]);

        $key = app(ApiClientService::class)->generateKey($client);

        $response = $this->withHeader('Authorization', 'Bearer '.$key['plaintext'])
            ->getJson('/api/v1/trading-partners');

        $response->assertOk();
        $response->assertJsonFragment(['code' => 'API-BUYER']);
    }

    public function test_missing_api_key_is_rejected(): void
    {
        $this->getJson('/api/v1/trading-partners')->assertUnauthorized();
    }
}
