# Modules

Module catalog: `App\Services\Modules\ModuleCatalog` seeded by `ModuleSeeder`.

## Activation

Tenants enable modules in Filament **Module settings** (`ModuleSettings` page) via `ModuleRegistryService`:

- Dependencies enforced (e.g. `webhooks` requires `api_hub`).
- Core modules cannot be deactivated.
- Activity logged to `module_activity_logs`.

## Categories

Core, EDI, Integrations, ERP, Commerce, Documents, Analytics, Automation, Security, Admin — see catalog for full list (~30 modules).

## Middleware

`EnsureTenantModule` gates routes/features by active module code (API and web).

## Feature flags

Many modules are catalog entries for billing/planning; not every module has a dedicated UI yet. See `IMPLEMENTATION-REPORT.md` for truth table.
