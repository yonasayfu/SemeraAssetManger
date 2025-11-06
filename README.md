# Semera Asset Manager

Laravel 12 + Inertia + Vue application for asset management with Freshservice‑style Catalog & Procurement features.

## Quick Start
1. cp .env.example .env
2. composer install && npm install
3. php artisan key:generate
4. php artisan migrate --seed
5. composer dev (or run php artisan serve, php artisan queue:listen, npm run dev separately)

## Handy Commands
- php artisan make:module Inventory scaffolds a clean-architecture module (controller/service/DTO/request/resource). Adjust stubs in stubs/module/.
- composer lint / 
pm run lint (see GitHub Actions pipelines .github/workflows/*.yml).
- php artisan test (Pest + PHPUnit). API smoke tests live under 	ests/Feature/Api.

## WebSockets / Laravel Reverb
- Set BROADCAST_DRIVER=reverb and populate the REVERB_* variables in .env.
- Run php artisan reverb:start alongside your dev processes to boot the websocket server.
- Front-end clients can connect through Laravel Echo pointing at REVERB_HOST/REVERB_PORT.

## Modules
- Assets: CRUD + operations (checkout/in, lease/return, reserve, move, dispose), maintenance, warranty.
- Catalog & Procurement: Vendors, Products, Contracts, Purchase Orders (items + receive), Software inventory.
- Reports: Assets, Audits, Checkout, Leased Assets, Maintenance, Reservations, Status, Transactions, plus Contracts/POs/Software (ReportBuilder + CSV).
- Tools: Import (Admin) for core taxonomies + Catalog (vendors/products/contracts/POs/software). Export CSV for most lists.
- Alerts & Notifications: Daily alerts for maintenance/warranty, contracts expiring, and POs due/overdue; mail + in‑app notifications.
- Dashboard: Overview widgets, calendar, charts, and Catalog & Procurement summary (expiring contracts, POs due, software seats).

## API Clients
- Sanctum-protected endpoints are documented in Upcomming/Must/ApiIntegrationGuide.md with an OpenAPI spec under docs/api/openapi.yaml.

## Containers & Devcontainer
- Copy docker-compose.example.yml to docker-compose.yml to spin up pp + db services for local development.
- VS Code users can open the project in a container via .devcontainer/devcontainer.json.

## CI
GitHub Actions provide lint + test pipelines (.github/workflows/lint.yml and 	ests.yml). Mirror these steps in your own CI to keep code style and tests green.

## Operations (Queues & Scheduler)
- Queue driver: database or redis. Run a worker in development: `php artisan queue:work`.
- Scheduler: cron every minute: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`.
- Scheduled jobs include:
  - reports.run-scheduled
  - maintenance.generate-recurring
  - warranties.check-expiry
  - alerts.generate (builds alert rows)
  - alerts.send-pending (sends notifications for unsent alerts)

## Documentation Hub
- Database Schema: `docs/DATABASE_SCHEMA.md`
- Hosting on GoDaddy (cPanel): `docs/HOSTING_GODADDY.md`
- Hosting on Laravel Forge: `docs/HOSTING_FORGE.md`
- Hosting on Laravel Vapor (AWS): `docs/HOSTING_VAPOR.md`
- Executive Presentation: `docs/PRESENTATION.md`
- Freshdesk/ITSM Roadmap: `docs/ROADMAP_FRESHDESK_FEATURES.md`
- RBAC matrix: `docs/RBAC.md`
- Freshservice‑style Upgrade plan & status: `Upcomming/Must/upgradetofreshservice.md`, `Upcomming/Must/PhaseSummary.md`
