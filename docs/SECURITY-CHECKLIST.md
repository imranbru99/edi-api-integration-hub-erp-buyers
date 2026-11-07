# Security Testing Checklist

Use this checklist before production go-live. Do **not** claim SOC 2, ISO 27001, or GDPR certification unless independently verified.

## Authentication & sessions
- [ ] Passwords hashed with bcrypt/argon (never plaintext)
- [ ] Platform admin panel (`/admin`) restricted to `is_platform_admin`
- [ ] Tenant workspace (`/app`) requires membership
- [ ] Partner portal only shows shared partner data
- [ ] Demo passwords never seeded when `APP_ENV=production`
- [ ] 2FA fields present; enforce for privileged roles when enabled
- [ ] Login history recorded without storing credentials

## Tenant isolation
- [ ] `BelongsToTenant` global scope applied to tenant-owned models
- [ ] Cross-tenant IDOR attempts return 404/403 (API + Filament)
- [ ] Files stored under tenant-scoped paths
- [ ] Queue jobs carry and restore tenant context
- [ ] Reports never mix tenant metrics

## API & webhooks
- [ ] API keys stored hashed; plaintext shown once
- [ ] Rate limiting enabled on API routes
- [ ] Webhook HMAC signatures verified
- [ ] Idempotency keys prevent duplicate EDI processing
- [ ] Secrets never written to application logs

## EDI & documents
- [ ] Raw EDI not publicly routable
- [ ] Certificate private keys encrypted at rest and never re-displayed
- [ ] AS2/SFTP credentials encrypted
- [ ] Failed transactions quarantined (not silently dropped)

## AI governance
- [ ] Tenant must opt in before sending content to external AI
- [ ] AI cannot auto-publish production mappings
- [ ] Sensitive fields redacted when configured

## Infrastructure
- [ ] HTTPS/TLS terminated correctly
- [ ] `APP_DEBUG=false` in production
- [ ] Drutogoti sampling restricted to authorized admins
- [ ] Backups encrypted; retention policies documented
