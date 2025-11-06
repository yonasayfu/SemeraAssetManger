# Operations Guide

## Prerequisites
- PHP 8.2+
- Composer
- Node 20+
- Database (MySQL 8+/PostgreSQL 14+)
- Redis (optional for queues/cache)

## Environment
Copy `.env.example` to `.env` and set values:

- APP_ENV, APP_KEY, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- QUEUE_CONNECTION (database/redis), CACHE_STORE, SESSION_DRIVER
- MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS, MAIL_FROM_NAME
- FILESYSTEM_DISK (public/s3); S3_* if using S3

## Setup
composer install
php artisan key:generate
php artisan migrate --seed
npm ci && npm run build

## Queues & Scheduler
- Run worker: `php artisan queue:work --tries=3`
- Scheduler (cron): `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`
- Key scheduled jobs:
  - reports.run-scheduled (saved reports)
  - maintenance.generate-recurring (recurring work orders)
  - warranties.check-expiry (daily)
  - alerts.generate (daily; builds alert rows for contracts/POs/maintenance/warranty)
  - alerts.send-pending (daily; sends notifications and marks alerts as sent)

## Caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

## Backups & Logs
- Configure log rotation via OS or container.
- Database backups per your ops tooling.

## Deploy
- Zero-downtime strategy: build assets off-box, run migrations, cache config/routes/views, restart queue workers.
- After deploy: `php artisan migrate --force && php artisan optimize:clear && php artisan optimize`
