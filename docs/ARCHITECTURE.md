# Architecture

## Layers

1. **Public web** — Blade marketing, demo playground, docs renderer (`routes/web.php`).
2. **Filament panels** — Operator UI: `/app` (tenant), `/admin` (platform), `/partner`, `/developer`.
3. **HTTP API** — `/api/v1/*` with API keys and Sanctum for user tokens.
4. **Domain (EDI)** — `app/Domain/Edi/*`: parsers, generators, mapping, inbound/outbound processors, delivery gateways.
5. **Services** — Tenancy, modules, webhooks, certificates, billing meters, demo reset.
6. **Queues** — Named queues in `config/edi-hub.php` (`edi-inbound`, `edi-delivery`, `webhook-delivery`, etc.).

## Inbound flow

```
Raw file → EdiInboundProcessor
  → parse (X12Parser / EdifactParser)
  → validate (EdiValidationService)
  → optional MappingProfile rules
  → canonical document (850/856/810)
  → AcknowledgmentService (997 / CONTRL)
```

Async path: `ProcessInboundEdiJob` on queue `edi-inbound`.

## Outbound flow

`EdiOutboundProcessor` builds interchange from canonical JSON, validates, delivers via `DeliveryGatewayInterface`.

| Driver | Class | Notes |
|--------|-------|-------|
| `mock` (default) | `MockDeliveryGateway` | Logs only |
| `as2` | `As2DeliveryGateway` | **Sandbox** — writes `storage/app/as2-out/` |
| `sftp` | `SftpDeliveryGateway` | Requires connection config |
| `rest` | `RestDeliveryGateway` | HTTP POST to partner endpoint |

Set `EDI_HUB_DELIVERY_DRIVER` in `.env`.

## Multi-tenancy

- `TenantContext` set from authenticated user (`SetTenantFromUser`) or API key middleware.
- Models using `BelongsToTenant` apply a global scope on `tenant_id`.

## Admin UI choice

**Filament 3** is the canonical admin. The `laravel-admin-panel` package is intentionally **not** installed (conflicts with Filament resource discovery and UX).

## Related docs

See `DATABASE.md`, `EDI-WORKFLOWS.md`, `DEPLOYMENT.md`, `IMPLEMENTATION-REPORT.md`.
