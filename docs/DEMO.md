# Demo environment

## Enable

```env
APP_DEMO_MODE=true
DEMO_PASSWORD=demo-change-me
```

## Seed

```bash
php artisan edi-hub:demo-setup
php artisan edi-hub:demo-setup --reset   # requires demo mode; wipes demo tenant EDI data
```

Creates tenant **Global Apparel Manufacturing Ltd** with fictional partners and sample interchanges.

## Public UI

- `/demo` — overview, login, transactions, inspect layers
- Demo user emails: `DEMO_EMAIL_*` in `.env.example`

## Filament

Log in as demo EDI manager (`demo.edi@edi-hub.test` in local) → `/app` dashboard widgets show live counts from seeded data.

## Safety

`DemoResetService` refuses reset unless `config('edi-hub.demo.enabled')` is true.
