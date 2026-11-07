# Security

## Tenant isolation

Global Eloquent scope on tenant-owned models. API keys bind requests to a single `tenant_id` via `TenantContext`.

## Secrets

- API keys stored hashed; plaintext shown once on generation.
- Certificate private keys: `private_key_encrypted` (Laravel `Crypt`); never shown in Filament after save.
- Webhook endpoint secrets stored hashed; HMAC for outbound delivery.

## Config

`config/edi-hub.php` → `security.encrypt_credentials_at_rest`, `never_log_secrets`, webhook signature header name.

## Roles

Spatie permissions; platform admin vs tenant roles (`RolePermissionSeeder`, demo users in `DemoSeeder`).

## Planned hardening

- Field-level encryption for integration connection passwords
- IP allow lists per API client
- SOC2-style audit export
