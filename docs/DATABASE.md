# Database

SQLite is supported for local/testing; MySQL 8 is recommended for Docker/production (`docker-compose.yml`).

## Core tenancy

- `tenants`, `tenant_members`, `tenant_modules`, `branches`, `departments`
- `users` with `current_tenant_id`, Spatie `roles` / `permissions`

## EDI

- `trading_partners` — partner identifiers (ISA, AS2 IDs, standards)
- `edi_transactions` — raw, parsed, canonical JSON columns (`*_content` primary; legacy `*_payload` still readable in UI)
- `edi_transaction_events` — audit trail per transaction
- `edi_acknowledgments` — functional ack payload (`ack_type`, `standard`, `document_type`)
- `edi_control_numbers` — interchange/ST counters per partner
- `mapping_profiles`, `mapping_rules`
- `transaction_errors`, `transaction_retries`

## Integrations

- `integration_connections`, `api_clients`, `api_keys`, `api_request_logs`
- `webhook_endpoints`, `webhook_deliveries`
- `certificates` — `private_key_encrypted` hidden from API/Filament after save

## Commerce (canonical mirrors)

- `purchase_orders`, `shipments`, `invoices`, `products`, `workflows`

## Platform admin

- `plans`, `modules`, `module_categories`, `subscriptions`, `usage_records`

## Migrations

All under `database/migrations/`. Alignments (e.g. `2026_11_07_190021_align_mapping_and_events_schema.php`) add columns without breaking existing seed data.

## Seeders

- `ModuleSeeder`, `PlanSeeder` — always
- `DemoSeeder` — when `APP_DEMO_MODE` or local/testing (`DatabaseSeeder`)
