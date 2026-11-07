# HTTP API (v1)

Base path: `/api/v1`. Defined in `routes/api.php`.

## Authentication

- **API keys** — `Authorization: Bearer {key}` or `X-Api-Key`. Managed in Filament **API Clients** (`ApiClientResource` + generate key action).
- **User token** — `POST /api/v1/auth/token` (Sanctum) for `auth/me`.

Middleware: `AuthenticateApiKey`, `ApiRateLimit`, `EnsureTenantContext`.

## Resources (implemented)

| Method | Path | Notes |
|--------|------|-------|
| GET/POST | `/trading-partners` | Tenant-scoped partners |
| GET | `/edi-transactions` | List/show transactions |
| GET/POST | `/purchase-orders`, `/shipments`, `/invoices` | Canonical commerce |
| GET | `/inventory` | Stub/list |
| GET/POST/DELETE | `/webhooks` | Endpoint registration |
| GET | `/mappings` | Mapping profiles |

## Webhooks (outbound from hub)

Deliveries sign body with HMAC-SHA256 header `X-EDI-Hub-Signature` (see `config/edi-hub.security.webhook_signature_header`).

## Planned

- OpenAPI 3 export
- Pagination cursor standardization
- Partner-scoped API tokens with fine-grained scopes enforcement
