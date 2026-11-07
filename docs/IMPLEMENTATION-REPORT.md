# Implementation report

Last updated: 2026-11-07. This document is honest about what is **production-ready**, **sandbox/mock**, or **planned**.

## Summary

| Area | Status |
|------|--------|
| X12 / EDIFACT parsers & generators (850, 856, 810, 997, ORDERS, etc.) | **Implemented** — unit tests in `tests/Unit/Edi/` |
| Inbound pipeline (parse → validate → map → canonical → 997/CONTRL ack) | **Implemented** — `EdiInboundProcessor` |
| Outbound pipeline | **Implemented** — `EdiOutboundProcessor` with pluggable delivery |
| Delivery drivers | **mock** (default), **sftp/rest** stubs, **as2** sandbox (filesystem) |
| Filament panels (`/app`, `/admin`, `/partner`, `/developer`) | **Implemented** |
| App Filament resources (11 commerce/integration resources) | **Implemented** |
| REST API v1 + API keys | **Implemented** |
| Webhooks (HMAC SHA-256) | **Implemented** dispatch; HTTP delivery via queue |
| Multi-tenant scoping | **Implemented** — `BelongsToTenant` + `TenantContext` |
| Demo site `/demo` + `edi-hub:demo-setup` | **Implemented** |
| Docker Compose (PHP 8.3, nginx, MySQL 8, Redis, queue worker) | **Implemented** |
| `laravel-admin-panel` package | **Not installed** — conflicts with Filament (canonical admin) |
| Production AS2 HTTP/MDN | **Planned** — current `As2DeliveryGateway` is sandbox only |
| Live ERP connectors (SAP, Oracle, etc.) | **Planned / mock** on marketing pages |

## Artisan commands

- `php artisan edi-hub:demo-setup` — seeds plans + demo tenant (`DemoSeeder`)
- `php artisan edi-hub:demo-setup --reset` — `DemoResetService` (requires `APP_DEMO_MODE=true`)
- `php artisan edi-hub:process-inbound {file}` — CLI harness for inbound testing

## Configuration

- `config/edi-hub.php` — hub name, demo, queues, `delivery_driver` (`mock`|`as2`|`sftp`|`rest`)
- `.env.example` — `EDI_HUB_*`, `DEMO_*`, `AI_HUB_*`, `DRUTOGOTI_*`

## Tests

Feature: tenant isolation, inbound 850 flow, API auth, webhook signatures, module activation. Unit: parsers, mapping, generators.
