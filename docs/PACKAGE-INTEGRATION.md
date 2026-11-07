# Package integration

Packages referenced in `config/edi-hub.php` → `packages` array.

| Package | Env / config | Status |
|---------|----------------|--------|
| Filament 3 | default | **Canonical admin UI** |
| Spatie Permission | — | Roles for panels and demo |
| Sanctum | — | API user tokens |
| ImranDev AI Hub | `AI_HUB_*` | Optional mapping assistant |
| Drutogoti | `DRUTOGOTI_*` | Optional observability |
| Universal Slug | — | Tenant/blog slugs |
| Attendance Hub | `ATTENDANCE_HUB_ENABLED` | Off by default |
| **laravel-admin-panel** | — | **Not installed** (Filament conflict) |

## Composer path repos

Vendor packages under `vendor/imrandevbd/*` ship with their own tests; hub feature tests do not duplicate them.

## Adding a new package

1. Require via Composer.
2. Register service provider if needed.
3. Add flag to `config/edi-hub.packages`.
4. Document in this file and `IMPLEMENTATION-REPORT.md`.
