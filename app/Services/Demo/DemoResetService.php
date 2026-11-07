<?php

namespace App\Services\Demo;

use App\Models\EdiTransaction;
use App\Models\EdiTransactionEvent;
use App\Models\IntegrationConnection;
use App\Models\MappingProfile;
use App\Models\PurchaseOrder;
use App\Models\Tenant;
use App\Models\TradingPartner;
use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\Artisan;

class DemoResetService
{
    public function enabled(): bool
    {
        return (bool) config('edi-hub.demo.enabled');
    }

    public function reset(): void
    {
        if (! $this->enabled()) {
            throw new \RuntimeException('Demo reset is only allowed when APP_DEMO_MODE is enabled.');
        }

        $tenant = Tenant::query()->where('slug', 'global-apparel-manufacturing-ltd')->first();

        if ($tenant) {
            EdiTransactionEvent::query()
                ->whereIn('edi_transaction_id', EdiTransaction::withoutGlobalScopes()->where('tenant_id', $tenant->id)->pluck('id'))
                ->delete();
            PurchaseOrder::withoutGlobalScopes()->where('tenant_id', $tenant->id)->delete();
            EdiTransaction::withoutGlobalScopes()->where('tenant_id', $tenant->id)->delete();
            MappingProfile::withoutGlobalScopes()->where('tenant_id', $tenant->id)->delete();
            IntegrationConnection::withoutGlobalScopes()->where('tenant_id', $tenant->id)->delete();
            TradingPartner::withoutGlobalScopes()->where('tenant_id', $tenant->id)->delete();
            $tenant->delete();
        }

        Artisan::call('db:seed', ['--class' => DemoSeeder::class, '--force' => true]);
    }
}
