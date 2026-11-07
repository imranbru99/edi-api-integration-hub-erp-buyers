<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => 'starter',
                'name' => 'Starter',
                'description' => 'Single facility, 3 trading partners, core X12 document set.',
                'price_monthly' => 499,
                'price_yearly' => 4990,
                'sort_order' => 10,
                'limits' => ['partners' => 3, 'transactions_monthly' => 5000],
                'modules' => ['edi-gateway', 'partner-mgmt'],
            ],
            [
                'code' => 'growth',
                'name' => 'Growth',
                'description' => 'Multi-branch operations with EDIFACT and API ingress.',
                'price_monthly' => 1299,
                'price_yearly' => 12990,
                'sort_order' => 20,
                'limits' => ['partners' => 15, 'transactions_monthly' => 50000],
                'modules' => ['edi-gateway', 'partner-mgmt', 'mapping-studio', 'api-hub'],
            ],
            [
                'code' => 'enterprise',
                'name' => 'Enterprise',
                'description' => 'Dedicated queues, custom SLA, and on-premise bridge options.',
                'price_monthly' => null,
                'price_yearly' => null,
                'sort_order' => 30,
                'limits' => ['partners' => null, 'transactions_monthly' => null],
                'modules' => ['edi-gateway', 'partner-mgmt', 'mapping-studio', 'erp-sync', 'api-hub', 'compliance'],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
