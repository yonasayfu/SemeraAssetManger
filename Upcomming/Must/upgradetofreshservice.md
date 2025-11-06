# Freshservice-Style Upgrade Plan

## Goals
- Close key feature gaps with a Freshservice-like asset management experience.
- Maintain Laravel + Inertia stack coherence and our existing RBAC, reports, and tools.

## Scope Summary
- Vendors & Products (catalog + warranty/cost metadata)
- Contracts hub (lease, maintenance, license, warranty)
- Purchase Orders (POs) with line items and receiving
- Software/SaaS inventory (seats, assignments)
- Enhanced asset form UX (vendor/product-driven warranty calc)
- Filters & lists (vendor/product/used-by everywhere)
- Reports (Contracts, POs, Software) with CSV export
- Tools import/export for new entities
- Dashboard cards + notifications (expiring contracts, PO ETA)

## Phase Plan

### Phase 1 — Data Models + Seed
- Add models/migrations:
  - `Vendor` (name, contact fields)
  - `Product` (vendor_id, name/sku, warranty_months, unit_cost, category)
  - `Contract` (type enum: lease/maintenance/license/warranty; start/end; amount/status; vendor_id/product_id; notes)
  - `PurchaseOrder` (number, name, vendor_id, expected_delivery_at, status, total_minor, currency)
  - `PurchaseOrderItem` (po_id, product_id, qty, unit_cost_minor, received_qty)
  - `Software` (name, vendor_id, type: saas/on-prem, seats_total, seats_used, status)
- Add optional FKs on `assets`: vendor_id, product_id
- Seeder: coherent samples (vendors/products/contracts/POs/software linked to existing assets)

### Phase 2 — CRUD UI + Routes
- Vendors + Products CRUD (Index/Create/Edit/Delete)
- Contracts CRUD + type tabs and filters
- POs CRUD + line items + receive flow (increments received_qty)
- Software CRUD + seat assignment to staff

Navigation & RBAC
- Web routes (`routes/web.php`) registered for Vendors, Products, Contracts, Purchase Orders, and Software with permission middleware.
- Sidebar (`resources/js/components/AppSidebar.vue`) shows Catalog & Procurement group with links gated by `vendors.*`, `products.*`, `contracts.*`, `purchase-orders.*`, `software.*` permissions.
- Role/permission seeding (`database/seeders/RolePermissionSeeder.php`) includes all new abilities and assigns them to roles; policies mapped in `app/Providers/AuthServiceProvider.php`.

### Phase 3 — Asset Form Enhancements
- Add vendor/product selectors
- Auto-calc warranty expiry = purchase_date + product.warranty_months
- Inline cost hints when product selected
- Optional link to PO line item

Phase 3 implemented — Asset Form Enhancements

What I implemented
- Vendor/Product selectors on Assets Create/Edit with vendor-driven product filtering.
- Warranty expiry hint auto-calculated from purchase_date + product.warranty_months.
- Inline cost hint based on product.unit_cost_minor + currency.
- Backend validation and model updates (Asset fillable + relationships, controller validation, Inertia props) for vendor_id/product_id.

How to test
- Go to `/assets/create` or `/assets/{id}/edit`.
- Pick a Vendor, then select a Product.
- Set Purchase Date; a warranty expiry hint displays if the product has warranty_months.
- A cost suggestion shows based on the product’s price; you can accept or override.

Optional PO link — implemented
- Migration adds `purchase_order_item_id` FK on `assets` to `purchase_order_items`.
- Asset model fillable + relation; controller validates and passes open/unreceived PO items with product/vendor context.
- UI adds a PO Item selector on Create/Edit, filtered by selected Vendor/Product; displays PO number, product name, remaining/qty, and unit price.

### Phase 4 — Filters & Lists
- Add vendor/product/used-by filters to Assets, Contracts, POs, Software
- Presets akin to Freshservice (All assets, By type, Used By)

Phase 4 implemented — Filters & Lists

What I implemented
- Assets list (`/assets`):
  - Filters: Vendor, Product (filtered by Vendor), Used By (specific staff or preset Assigned/Unassigned).
  - Backend supports `vendor_id`, `product_id`, `staff_id`, `used_by`, plus sort/direction/per_page.
  - Export honors current filters in URL (server export remains global for now).
- Contracts list (`/contracts`):
  - Filters: Vendor, Product, Used By preset (Assigned/Unassigned via linked asset).
- Purchase Orders list (`/purchase-orders`):
  - Filters: Vendor and Product (via PO items relation), Search and per-page.
- Software list (`/software`):
  - Filters: Vendor and Type (SaaS/On‑prem), Search and per-page.

Notes
- Product dropdowns filter based on selected Vendor for a clean UX.
- “Used By” preset options provide quick All/Assigned/Unassigned views.

### Phase 5 — Reports
- Add ReportService queries: `getContractReportQuery`, `getPurchaseOrderReportQuery`, `getSoftwareReportQuery`
- Pages: Reports/Contracts, Reports/PurchaseOrders, Reports/Software (ReportBuilder)
- Chunked CSV streaming for large exports

Phase 5 implemented — Reports for Contracts, POs, and Software

What I implemented
- ReportService methods: `getContractReportQuery`, `getPurchaseOrderReportQuery`, `getSoftwareReportQuery` with pragmatic filters and relations.
- Controllers: `ContractReportController`, `PurchaseOrderReportController`, `SoftwareReportController` (Inertia single-action).
- Pages: `resources/js/Pages/Reports/Contracts.vue`, `Reports/PurchaseOrders.vue`, `Reports/Software.vue` using `ReportBuilder` with sensible filter definitions and columns.
- Routes: added under `/reports` with `reports.view` permission.
- CSV: uses existing `RunReportController@export` streaming; `ReportBuilder` integrates via `family`.

Navigation & RBAC
- Sidebar shows links to Contract, Purchase Order, and Software reports (gated by `reports.view`).

How to test
- Visit `/reports/contracts`, `/reports/purchase-orders`, `/reports/software`.
- Adjust filters, preview updates; Export CSV via ReportBuilder (Admin).

### Phase 6 — Tools Import/Export
- Import templates + endpoints:
  - vendors.csv, products.csv, contracts.csv, purchase_orders.csv (+ items), software.csv
- Export mappings to Download Center for new entities

Phase 6 implemented — Import endpoints for new entities

What I implemented
- Import endpoints (routes + controllers) under `/tools/import/*` with Admin guard:
  - Vendors → `VendorImportController` → `ImportVendorsJob`
  - Products → `ProductImportController` → `ImportProductsJob`
  - Contracts → `ContractImportController` → `ImportContractsJob`
  - Purchase Orders (+ items) → `PurchaseOrderImportController` → `ImportPurchaseOrdersJob`
  - Software → `SoftwareImportController` → `ImportSoftwareJob`
- Updated Tools Import UI to include Vendors, Products, Contracts, Purchase Orders, Software entities.
- Updated Tools Export UI to list Vendors/Products/Contracts/POs/Software (export wiring remains a stub consistent with pre-existing pattern).

Notes
- Jobs are scaffolded and ready for parsing logic; they currently queue from uploaded file path.
- All routes gated by `permission:tools.view` and `role:Admin` for import actions.

### Phase 7 — Dashboard Cards
- Contracts expiring soon (30/60/90)
- POs due this month (open)
- Software: Discovered/Managed counts + seat usage

Phase 7 implemented — Dashboard Cards

What I implemented
- DashboardController aggregates:
  - Contracts expiring buckets: 0–30d, 31–60d, 61–90d.
  - Open POs due this month.
  - Software seat utilization (used/total, %).
- Dashboard UI (`resources/js/Pages/Dashboard.vue`) adds a Catalog & Procurement summary card with the above metrics.

Navigation & RBAC
- Uses the existing dashboard route and `dashboard.view` permission. No new routes added.

### Phase 8 — Alerts & Notifications
- Daily cron: expiring contracts; overdue/near-due POs
- Mail + in-app notifications

Phase 8 implemented — Alerts & Notifications

What I implemented
- Alert generation (daily):
  - Contracts expiring (next 60 days) → type: "Contract Expiring".
  - Purchase Orders due soon (next 14 days) → type: "PO Due Soon"; overdue → type: "PO Overdue".
  - Wired into existing `GenerateAlertsJob` scheduled daily.
- Notifications:
  - New notification classes send Mail + Database entries:
    - `ContractExpiringNotification`, `PurchaseOrderDueNotification`.
  - New `SendPendingAlertsJob` runs daily 08:00; sends unsent alerts of the above types to Admin + Manager roles and marks them sent.
- Scheduling:
  - `app/Console/Kernel.php` schedules `GenerateAlertsJob` (existing) and `SendPendingAlertsJob` daily.

Navigation & RBAC
- Uses existing Alerts permission `alerts.view` and pages; no new routes required.

How to test
- Seed or create a Contract ending within next 60 days and an open PO with expected delivery within 14 days or in the past.
- Run: `php artisan schedule:run` (or `php artisan tinker` to dispatch jobs) and check `alerts` table and Notifications (bell/inbox) for Admin/Manager.

### Phase 9 — Permissions & Policies
- New abilities: vendors.*, products.*, contracts.*, purchase_orders.*, software.*
- Seed to Admin/Manager; map in AuthServiceProvider

Phase 9 implemented — Permissions & Policies

What I implemented/verified
- Seeding: `database/seeders/RolePermissionSeeder.php` includes `vendors.*`, `products.*`, `contracts.*`, `purchase-orders.*`, `software.*` and assigns to roles (Admin full; Manager view/create/update for new modules).
- Policies: mapped in `app/Providers/AuthServiceProvider.php` for Vendor, Product, Contract, PurchaseOrder, Software.
- Routes: all new module routes in `routes/web.php` are guarded with the corresponding `*.view` middleware.
- Sidebar: links are RBAC-gated and only shown when the user has appropriate permissions.

### Phase 10 — Optional Enhancements
- Custom fields per Asset Type/category (CI-type variance)
- Unified Contracts Board (all contract types view)

Phase 10 implemented — Custom Fields + Contracts Board

What I implemented
- Custom Fields (assets):
  - Migration adds `assets.custom_fields` (JSON, nullable).
  - Model casts/fillable updated; controller validates `custom_fields` as array.
  - UI: Assets Create/Edit includes a simple key/value repeater to capture extra fields stored in `custom_fields`.
- Contracts Board:
  - New route `/contracts/board` (permission: `contracts.view`).
  - Controller aggregates all contracts and groups by type.
  - Page `resources/js/Pages/Contracts/Board.vue` shows columns per type with small contract cards.
  - Sidebar link under Catalog & Procurement.

## Sequenced MVP Path
1) Phase 1 + 9 (models/seed/permissions)
2) Phase 2 (Vendors/Products + Asset form)
3) Phase 2 (Contracts) + Phase 8 (contract alerts)
4) Phase 2 (POs + receive)
5) Phase 2 (Software + seat assignment)
6) Phase 5 (Reports for Contracts/POs/Software)
7) Phase 6 (Import/Export)
8) Phase 7 (Dashboard cards)

## Acceptance Criteria (MVP)
- Create/edit Vendors, Products, Contracts, POs, Software
- Asset form drives warranty from Product
- Receive PO items adjusts received_qty and PO status
- ReportBuilder pages for Contracts/POs/Software with CSV export
- Tools import/export templates operational for new entities
- Dashboard shows expiring contracts + open POs

## Risks & Mitigations
- Data migration complexity: start with optional FKs, keep legacy fields intact
- Scope creep: phase-gate non-critical features (custom fields, boards)
- Performance: use chunked exports, indexes on vendor_id/product_id

## Dependencies
- Existing Staff/Assets/Categories/Sites/Departments
- Notifications + scheduler (cron)

## Rough Timeline
- Week 1: Phase 1–2 (Vendors/Products + Asset integration)
- Week 2: Contracts + alerts; start POs
- Week 3: POs receive + Software; Reports
- Week 4: Tools import/export; Dashboard cards; polish
