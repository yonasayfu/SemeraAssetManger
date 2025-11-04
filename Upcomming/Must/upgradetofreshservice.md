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

### Phase 3 — Asset Form Enhancements
- Add vendor/product selectors
- Auto-calc warranty expiry = purchase_date + product.warranty_months
- Inline cost hints when product selected
- Optional link to PO line item

### Phase 4 — Filters & Lists
- Add vendor/product/used-by filters to Assets, Contracts, POs, Software
- Presets akin to Freshservice (All assets, By type, Used By)

### Phase 5 — Reports
- Add ReportService queries: `getContractReportQuery`, `getPurchaseOrderReportQuery`, `getSoftwareReportQuery`
- Pages: Reports/Contracts, Reports/PurchaseOrders, Reports/Software (ReportBuilder)
- Chunked CSV streaming for large exports

### Phase 6 — Tools Import/Export
- Import templates + endpoints:
  - vendors.csv, products.csv, contracts.csv, purchase_orders.csv (+ items), software.csv
- Export mappings to Download Center for new entities

### Phase 7 — Dashboard Cards
- Contracts expiring soon (30/60/90)
- POs due this month (open)
- Software: Discovered/Managed counts + seat usage

### Phase 8 — Alerts & Notifications
- Daily cron: expiring contracts; overdue/near-due POs
- Mail + in-app notifications

### Phase 9 — Permissions & Policies
- New abilities: vendors.*, products.*, contracts.*, purchase_orders.*, software.*
- Seed to Admin/Manager; map in AuthServiceProvider

### Phase 10 — Optional Enhancements
- Custom fields per Asset Type/category (CI-type variance)
- Unified Contracts Board (all contract types view)

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

