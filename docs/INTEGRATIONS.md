# Integrations

## Filament resources

| Resource | Purpose |
|----------|---------|
| `IntegrationConnectionResource` | ERP/SFTP/REST connection metadata |
| `MappingProfileResource` | Partner document maps |
| `ApiClientResource` | REST API clients + key rotation |
| `WebhookEndpointResource` | Outbound event subscriptions |
| `CertificateResource` | X.509 for signing (AS2 sandbox) |

## Delivery gateways

Configure via `EDI_HUB_DELIVERY_DRIVER`. See `ARCHITECTURE.md`.

## ERP connectors (marketing vs code)

Public `/erp-integrations` pages describe SAP, Oracle, Dynamics, etc. Runtime connector classes under `app/Domain/Integrations/` are **mock or stub** unless marked otherwise in `IMPLEMENTATION-REPORT.md`.

## Optional packages

- **AI Hub** (`AI_HUB_*`) — mapping suggestions via `EdiAiAssistant`
- **Drutogoti** (`DRUTOGOTI_*`) — query/observability hooks
- **Attendance Hub** — disabled by default (`ATTENDANCE_HUB_ENABLED`)

## Package conflict note

`laravel-admin-panel` is **not** integrated; use Filament for all operator CRUD.
