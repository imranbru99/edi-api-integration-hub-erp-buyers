# Deployment

## Docker Compose

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

Services: `app` (PHP 8.3 FPM), `nginx` (port 8080), `mysql:8.4`, `redis:7`, `queue` worker.

Sample host nginx: `deploy/nginx.conf` (proxy to `app:9000`).

## Environment

Copy `.env.example`. Production checklist:

- `APP_ENV=production`, `APP_DEBUG=false`
- MySQL credentials (`DB_*`)
- `QUEUE_CONNECTION=redis` + running `php artisan queue:work`
- `EDI_HUB_DELIVERY_DRIVER` appropriate for environment (use `mock` or sandbox `as2` in staging)

## Queue workers

Run separate workers per queue name from `config/edi-hub.queues` for scale (optional single worker for small installs).

## Filament

Panels: `/app`, `/admin`, `/partner`, `/developer` — ensure `APP_URL` and session domain are correct behind TLS.
