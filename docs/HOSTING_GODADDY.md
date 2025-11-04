# Hosting Laravel 12 on GoDaddy (cPanel)

This guide assumes a cPanel hosting plan with SSH enabled. For Managed VPS, steps are similar but you control the OS packages.

## 1) Requirements
- PHP 8.2 (or newer) with required extensions (mbstring, pdo_mysql, fileinfo, tokenizer, dom).
- Composer available on account (install via SSH if not present).
- Node.js 20+ locally to build assets (or use CI to build artifacts).

## 2) Prepare the app locally
- Copy `.env.example` → `.env` and set DB, APP_URL, MAIL, etc.
- Build assets: `npm ci && npm run build`.
- Generate key: `php artisan key:generate`.
- Commit vendor and node_modules? No — upload code without them; run composer install on server.

## 3) Deploy to GoDaddy via SSH or cPanel File Manager
- Upload project to `~/laravel_app` (outside `public_html`).
- Create a symlink or point the domain’s document root to `~/laravel_app/public`.
  - In cPanel: Domains → Document Root → set to `laravel_app/public`.

## 4) Composer install on server
```
cd ~/laravel_app
php -v
php -m | grep -E 'mbstring|pdo|fileinfo'
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan key:generate --force
```

## 5) Environment and storage
- Create `.env` on server (copy from local), update DB creds (MySQL in cPanel), MAIL, FILESYSTEM_DISK, APP_URL.
- Set correct permissions on `storage` and `bootstrap/cache`.
```
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6) Database
- Create DB + user in cPanel → MySQL® Databases.
- Update `.env` with host, db, user, pass.
- Run migrations + seed (optional):
```
php artisan migrate --force
# optional demo
php artisan db:seed --force
```

## 7) Queues & Scheduler
- For queues on shared hosting, use `database` driver and a cron running every minute:
```
* * * * * php /home/<user>/laravel_app/artisan schedule:run >> /dev/null 2>&1
```
- For long‑running workers (preferred), use VPS with Supervisor.

## 8) File uploads
- Ensure `FILESYSTEM_DISK=public`.
- Verify `storage:link` created `public/storage` symlink.

## 9) HTTPS and performance
- Enable SSL in cPanel (AutoSSL/Let’s Encrypt).
- Set `APP_URL=https://yourdomain.tld`.
- Consider Cloudflare for caching/SSL.

## 10) Zero‑downtime (optional)
- Use a separate `releases` folder; update the `public` symlink after composer install, migrations, and caches are warmed.

## Troubleshooting
- 500 errors: check `storage/logs/laravel.log`.
- 404 to `/public`: ensure document root points to `public/`.
- Missing extensions: switch PHP version in cPanel → select extensions.
