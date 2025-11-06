# Deploying to Laravel Forge (Nginx + PHP-FPM)

This guide covers a production‑grade deployment on a VPS provisioned by Laravel Forge.

## Prerequisites
- A supported VPS provider (DigitalOcean/Linode/AWS/etc) with an Ubuntu LTS image
- Laravel Forge account connected to your Git provider
- Domain/DNS under your control

## 1) Provision Server
1. In Forge: Create Server → Choose provider/region → PHP 8.2 → Database (MySQL 8)
2. Wait for provisioning; note the server name and IP

## 2) Create Site (Nginx)
1. Forge → Your Server → Create Site → Domain (e.g., app.example.com)
2. Project type: PHP/Laravel; Web directory: `public`
3. Add SSL: Click SSL → Let’s Encrypt → Request Certificate

## 3) Connect Repository
1. Site → Application → Git Repository → select repo + branch (e.g., main)
2. Enable Quick Deploy if you want automatic deploys on push

## 4) Environment (.env)
1. Site → Environment → paste a production `.env` (start with `.env.example`)
2. Required keys:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://app.example.com`
   - DB_* (from Forge server’s MySQL section)
   - `QUEUE_CONNECTION=redis` (preferred) or `database`
   - `CACHE_STORE=redis` or `file`, `SESSION_DRIVER=redis|database`
   - `MAIL_MAILER=smtp` and SMTP/SES credentials
   - `FILESYSTEM_DISK=public` (or s3 with S3_* vars)
   - Broadcasting/WebSockets if used
3. Click Save; Forge will write it to the server

## 5) Deploy Script
Site → Deployment → set script:
```
#!/usr/bin/env bash
set -euxo pipefail

cd /home/forge/{{domain}}

# Install dependencies
composer install --no-dev --prefer-dist --optimize-autoloader

# Build assets (optional: build in CI and upload artifacts)
# npm ci && npm run build

# Optimize
php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```
Save and click Deploy Now.

## 6) Queue Workers
- Forge → Daemons → Add Daemon:
  - Command: `php artisan queue:work --sleep=3 --tries=3 --max-time=3600`
  - User: forge; Directory: `/home/forge/{{domain}}`
- Optionally use Horizon; install `laravel/horizon` and add a daemon for `php artisan horizon`

## 7) Scheduler
- Forge → Scheduler → Add Job: `php artisan schedule:run`
- Frequency: Every minute; User: forge; Directory: `/home/forge/{{domain}}`
- This runs alerts, warranty checks, recurring maintenance, and saved reports

## 8) Database
- Forge → MySQL → Create Database & User
- Ensure your `.env` matches the generated credentials

## 9) Files, Storage & Permissions
- Ensure `storage` and `bootstrap/cache` are writable (Forge sets correct permissions)
- Run `php artisan storage:link` (deploy script includes this)

## 10) SSL, Redirects & Security
- Force HTTPS via Nginx config: Site → Meta → Enable “Force HTTPS”
- Add security headers if desired (Forge → Files → Nginx Configuration)

## 11) Logs & Monitoring
- Tail app logs: `forge@server: ~/logs/…/laravel.log`
- Enable Forge’s server and site monitoring; consider external uptime monitoring

## 12) Post‑Deploy Checks
- Verify dashboard, imports/exports, reports, scheduled alerts (watch queue worker output)
- Test emails (password reset, alerts)
- Confirm roles/permissions applied; Admin sidebar shows Catalog & Procurement

## Rollback Strategy
- Keep DB backups; use tagged releases
- Disable Quick Deploy during incidents; redeploy last known good tag

