# Freshservice Upgrade Tracker

Use this checklist to track progress. Mark items as completed and link PRs.

Legend: [ ] pending · [~] in progress · [x] done

## Phase 1 — Data Models + Seed
- [x] Create `Vendor` model/migration
- [x] Create `Product` model/migration
- [x] Create `Contract` model/migration (enum: lease/maintenance/license/warranty)
- [x] Create `PurchaseOrder` model/migration
- [x] Create `PurchaseOrderItem` model/migration
- [x] Create `Software` model/migration
- [x] Add nullable `vendor_id`, `product_id` to `assets`
- [x] Seeder for vendors/products/contracts/pos/software
- Notes/PR:

## Phase 2 — CRUD UI + Routes
- [x] Vendors CRUD (Index/Create/Edit/Delete)
- [x] Products CRUD (Index/Create/Edit/Delete)
- [x] Contracts CRUD (with filters)
- [x] Purchase Orders CRUD + line items + receive endpoint
- [x] Software CRUD + seat counts
- Notes/PR:

## Phase 3 — Asset Form Enhancements
- [x] Vendor/Product selectors on asset form
- [x] Warranty auto-calc from product.warranty_months
- [x] Inline cost hints
- [x] Optional link to PO line item
- Notes/PR:

## Phase 4 — Filters & Lists
- [x] Add vendor/product filters to Assets
- [x] Add used-by filters across relevant lists
- [x] Add filters to Contracts, POs, Software lists
- Notes/PR:

## Phase 5 — Reports
- [x] ReportService: Contracts
- [x] ReportService: Purchase Orders
- [x] ReportService: Software
- [x] Report pages (ReportBuilder) + CSV export
- Notes/PR:

## Phase 6 — Tools Import/Export
- [x] Import endpoints/templates: vendors, products, contracts, POs (+ items), software
- [x] Export CSV on lists (Admin)
- Notes/PR:

## Phase 7 — Dashboard Cards
- [x] Contracts expiring (30/60/90)
- [x] POs open and due this month
- [x] Software seats utilized vs total
- Notes/PR:

## Phase 8 — Alerts & Notifications
- [x] Daily cron for expiring contracts
- [x] Daily cron for PO ETA/overdue
- [x] Email/in-app notifications
- Notes/PR:

## Phase 9 — Permissions & Policies
- [x] Abilities: vendors.*, products.*, contracts.*, purchase_orders.*, software.*
- [x] Seed roles/permissions
- [x] Policy mappings
- Notes/PR:

## Phase 10 — Optional Enhancements
- [x] Custom fields per asset type/category (JSON on assets)
- [x] Unified Contracts Board
- Notes/PR:

---

## Cross-Cutting
- [x] Database indexes on vendor_id/product_id
- [x] Validation rules and error states (baseline)
- [x] E2E smoke: vendor→product→po→receive→asset→contract

## Links
- Plan: `Upcomming/Must/upgradetofreshservice.md`
- Owner: (assign)
- Target dates: (fill)
