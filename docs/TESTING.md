# Testing

```bash
php artisan test
composer test   # if defined in composer.json
```

## Feature tests (`tests/Feature/`)

| Test | Covers |
|------|--------|
| `TenantIsolationTest` | `BelongsToTenant` scope |
| `EdiInboundFlowTest` | 850 fixture → completed + ack |
| `ApiAuthenticationTest` | API key middleware |
| `WebhookSignatureTest` | HMAC signing |
| `ModuleActivationTest` | Module dependencies |

## Unit tests (`tests/Unit/Edi/`)

X12/EDIFACT parsers, mapping engine, X12 generator.

## CI recommendations

- SQLite in-memory (default `phpunit.xml`)
- Run migrations fresh per suite (`RefreshDatabase`)
- Do not commit `.env`; use `APP_KEY` generated in CI

## Manual EDI CLI

```bash
php artisan edi-hub:process-inbound tests/Fixtures/edi/x12_850_sample.edi
```
