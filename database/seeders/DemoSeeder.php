<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\EdiTransaction;
use App\Models\EdiTransactionEvent;
use App\Models\IntegrationConnection;
use App\Models\MappingProfile;
use App\Models\Plan;
use App\Models\PurchaseOrder;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\TenantMember;
use App\Models\TradingPartner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make(
            app()->environment('local')
                ? (string) config('edi-hub.demo.password')
                : str()->random(32)
        );

        foreach ([
            'platform_admin', 'tenant_owner', 'edi_manager', 'integrator', 'operations', 'buyer_user', 'supplier_user', 'auditor',
        ] as $role) {
            Role::findOrCreate($role);
        }

        $tenant = Tenant::query()->updateOrCreate(
            ['slug' => 'global-apparel-manufacturing-ltd'],
            [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Global Apparel Manufacturing Ltd',
                'legal_name' => 'Global Apparel Manufacturing Ltd (Demo)',
                'email' => 'ops@global-apparel-demo.test',
                'country' => 'BD',
                'timezone' => 'Asia/Dhaka',
                'currency' => 'USD',
                'status' => 'active',
                'is_demo' => true,
                'organization_types' => ['manufacturer', 'exporter'],
                'settings' => ['demo_banner' => true, 'ai' => ['authorized' => false]],
            ]
        );

        $partners = collect([
            ['code' => 'RETAIL-BUYER', 'name' => 'Global Retail Buyer', 'partner_type' => 'buyer'],
            ['code' => 'EU-FASHION', 'name' => 'European Fashion Group', 'partner_type' => 'buyer'],
            ['code' => 'ASIA-TEXTILE', 'name' => 'Asia Textile Supplier', 'partner_type' => 'supplier'],
            ['code' => 'INTL-LOGISTICS', 'name' => 'International Logistics Partner', 'partner_type' => 'logistics'],
        ])->mapWithKeys(function (array $data) use ($tenant) {
            $partner = TradingPartner::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $data['code']],
                array_merge($data, [
                    'status' => 'active',
                    'default_standard' => str_starts_with($data['code'], 'EU') ? 'edifact' : 'x12',
                    'is_demo' => true,
                    'isa_id' => 'DEMO'.substr($data['code'], 0, 8),
                ])
            );

            return [$data['code'] => $partner];
        });

        $emails = config('edi-hub.demo.emails');
        $users = [
            ['key' => 'platform', 'name' => 'Demo Platform Admin', 'role' => 'platform_admin', 'admin' => true],
            ['key' => 'owner', 'name' => 'Demo Tenant Owner', 'role' => 'tenant_owner', 'owner' => true],
            ['key' => 'edi', 'name' => 'Demo EDI Manager', 'role' => 'edi_manager'],
            ['key' => 'integrator', 'name' => 'Demo Integrator', 'role' => 'integrator'],
            ['key' => 'ops', 'name' => 'Demo Operations', 'role' => 'operations'],
            ['key' => 'buyer', 'name' => 'Demo Buyer User', 'role' => 'buyer_user'],
            ['key' => 'supplier', 'name' => 'Demo Supplier User', 'role' => 'supplier_user'],
            ['key' => 'auditor', 'name' => 'Demo Auditor', 'role' => 'auditor'],
        ];

        foreach ($users as $spec) {
            $email = $emails[$spec['key']] ?? "demo.{$spec['key']}@edi-hub.test";
            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $spec['name'],
                    'password' => $password,
                    'is_platform_admin' => $spec['admin'] ?? false,
                    'is_active' => true,
                    'current_tenant_id' => $tenant->id,
                ]
            );
            $user->syncRoles([$spec['role']]);
            TenantMember::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $user->id],
                [
                    'role_code' => $spec['role'],
                    'is_owner' => $spec['owner'] ?? false,
                    'status' => 'active',
                    'joined_at' => now(),
                ]
            );
        }

        $plan = Plan::query()->where('code', 'growth')->first();
        if ($plan) {
            Subscription::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'plan_id' => $plan->id],
                ['status' => 'active', 'starts_at' => now()->subMonth()]
            );
        }

        IntegrationConnection::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Mock ERP (Demo)'],
            [
                'connector_type' => 'mock',
                'status' => 'active',
                'health_status' => 'healthy',
                'last_sync_at' => now()->subMinutes(12),
            ]
        );

        MappingProfile::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Retail Buyer 850 → Canonical PO'],
            [
                'document_type' => '850',
                'standard' => 'x12',
                'direction' => 'inbound',
                'status' => 'active',
            ]
        );

        $fixtures = [
            ['file' => 'x12_850_sample.edi', 'type' => '850', 'standard' => 'x12', 'ref' => 'PO-987654', 'partner' => 'RETAIL-BUYER', 'dir' => 'inbound', 'status' => 'delivered'],
            ['file' => 'x12_855.edi', 'type' => '855', 'standard' => 'x12', 'ref' => 'ACK-PO-987654', 'partner' => 'RETAIL-BUYER', 'dir' => 'outbound', 'status' => 'acknowledged'],
            ['file' => 'x12_856.edi', 'type' => '856', 'standard' => 'x12', 'ref' => 'SHIP-445566', 'partner' => 'RETAIL-BUYER', 'dir' => 'outbound', 'status' => 'in_transit'],
            ['file' => 'x12_810.edi', 'type' => '810', 'standard' => 'x12', 'ref' => 'INV-778899', 'partner' => 'RETAIL-BUYER', 'dir' => 'outbound', 'status' => 'pending_invoice_match'],
            ['file' => 'x12_997.edi', 'type' => '997', 'standard' => 'x12', 'ref' => 'FA-905', 'partner' => 'RETAIL-BUYER', 'dir' => 'inbound', 'status' => 'accepted'],
            ['file' => 'edifact_orders_sample.edi', 'type' => 'ORDERS', 'standard' => 'edifact', 'ref' => 'PO-EDIFACT-001', 'partner' => 'EU-FASHION', 'dir' => 'inbound', 'status' => 'parsed'],
            ['file' => 'edifact_ordrsp.edi', 'type' => 'ORDRSP', 'standard' => 'edifact', 'ref' => 'RSP-PO-EDIFACT-001', 'partner' => 'EU-FASHION', 'dir' => 'outbound', 'status' => 'delivered'],
        ];

        foreach ($fixtures as $index => $fixture) {
            $path = str_contains($fixture['file'], 'sample')
                ? base_path('tests/Fixtures/edi/'.$fixture['file'])
                : database_path('seeders/data/'.$fixture['file']);
            $raw = file_exists($path) ? file_get_contents($path) : '';
            $partner = $partners[$fixture['partner']];

            $txn = EdiTransaction::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'control_number' => $fixture['ref'],
                ],
                [
                    'trading_partner_id' => $partner->id,
                    'document_type' => $fixture['type'],
                    'standard' => $fixture['standard'],
                    'direction' => $fixture['dir'],
                    'status' => $fixture['status'],
                    'interchange_control_number' => 'DEMO-'.(900 + $index),
                    'raw_payload' => $raw,
                    'raw_content' => $raw,
                    'parsed_payload' => ['segments' => substr_count($raw, $fixture['standard'] === 'x12' ? '~' : "'")],
                    'canonical_payload' => [
                        'document_type' => $fixture['type'],
                        'reference' => $fixture['ref'],
                        'partner' => $partner->name,
                    ],
                    'delivered_at' => $fixture['status'] === 'delivered' ? now()->subHours(2) : null,
                    'received_at' => now()->subHours(8),
                ]
            );

            $this->seedTimeline($txn, $fixture['status']);

            if ($fixture['type'] === '850') {
                PurchaseOrder::withoutGlobalScopes()->updateOrCreate(
                    ['tenant_id' => $tenant->id, 'po_number' => 'PO-987654'],
                    [
                        'edi_transaction_id' => $txn->id,
                        'trading_partner_id' => $partner->id,
                        'status' => 'open',
                        'total_amount' => 312.50,
                        'currency' => 'USD',
                        'meta' => ['lines' => 2, 'buyer' => $partner->name, 'demo' => true],
                    ]
                );
            }
        }

        BlogPost::withoutGlobalScopes()->updateOrCreate(
            ['slug' => 'why-canonical-edi-matters'],
            [
                'title' => 'Why Canonical EDI Matters for Apparel ERP Teams',
                'excerpt' => 'Stop rewriting mappings per buyer—normalize once, deliver everywhere.',
                'body' => "## The problem\n\nEach retail buyer ships slightly different 850 layouts.\n\n## The approach\n\nParse to canonical purchase orders, then map once into your ERP connector.",
                'published_at' => now()->subDays(3),
            ]
        );
    }

    protected function seedTimeline(EdiTransaction $txn, string $finalStatus): void
    {
        EdiTransactionEvent::query()->where('edi_transaction_id', $txn->id)->delete();

        $steps = [
            ['received', 'received', 'Interchange accepted at gateway edge'],
            ['validated', 'validated', 'Syntax and partner envelope checks passed'],
            ['parsed', 'parsed', 'Segments parsed into structured model'],
            ['mapped', 'mapped', 'Canonical document produced'],
        ];
        if (in_array($finalStatus, ['delivered', 'acknowledged', 'in_transit', 'pending_invoice_match', 'accepted'], true)) {
            $steps[] = ['delivered', $finalStatus, 'Downstream delivery or acknowledgment recorded'];
        }

        foreach ($steps as $i => [$type, $status, $message]) {
            EdiTransactionEvent::query()->create([
                'edi_transaction_id' => $txn->id,
                'event' => $type,
                'level' => 'info',
                'message' => $message,
                'context' => ['status' => $status],
            ]);
        }
    }
}
