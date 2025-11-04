# Freshservice Upgrade Tracker

Use this checklist to track progress. Mark items as completed and link PRs.

Legend: [ ] pending · [~] in progress · [x] done

## Phase 1 — Data Models + Seed
- [ ] Create `Vendor` model/migration
- [ ] Create `Product` model/migration
- [ ] Create `Contract` model/migration (enum: lease/maintenance/license/warranty)
- [ ] Create `PurchaseOrder` model/migration
- [ ] Create `PurchaseOrderItem` model/migration
- [ ] Create `Software` model/migration
- [ ] Add nullable `vendor_id`, `product_id` to `assets`
- [ ] Seeder for vendors/products/contracts/pos/software
- Notes/PR:

## Phase 2 — CRUD UI + Routes
- [ ] Vendors CRUD (Index/Create/Edit/Delete)
- [ ] Products CRUD (Index/Create/Edit/Delete)
- [ ] Contracts CRUD (with type tabs & filters)
- [ ] Purchase Orders CRUD + line items + receive endpoint
- [ ] Software CRUD + seat assignment to staff
- Notes/PR:

## Phase 3 — Asset Form Enhancements
- [ ] Vendor/Product selectors on asset form
- [ ] Warranty auto-calc from product.warranty_months
- [ ] Inline cost hints
- [ ] Optional link to PO line item
- Notes/PR:

## Phase 4 — Filters & Lists
- [ ] Add vendor/product filters to Assets
- [ ] Add used-by filters across relevant lists
- [ ] Add filters to Contracts, POs, Software lists
- Notes/PR:

## Phase 5 — Reports
- [ ] ReportService: Contracts
- [ ] ReportService: Purchase Orders
- [ ] ReportService: Software
- [ ] Report pages (ReportBuilder) + CSV export
- Notes/PR:

## Phase 6 — Tools Import/Export
- [ ] Import endpoints/templates: vendors, products, contracts, POs (+ items), software
- [ ] Export mappings to Download Center
- Notes/PR:

## Phase 7 — Dashboard Cards
- [ ] Contracts expiring (30/60/90)
- [ ] POs open and due this month
- [ ] Software seats utilized vs total
- Notes/PR:

## Phase 8 — Alerts & Notifications
- [ ] Daily cron for expiring contracts
- [ ] Daily cron for PO ETA/overdue
- [ ] Email/in-app notifications
- Notes/PR:

## Phase 9 — Permissions & Policies
- [ ] Abilities: vendors.*, products.*, contracts.*, purchase_orders.*, software.*
- [ ] Seed roles/permissions
- [ ] Policy mappings
- Notes/PR:

## Phase 10 — Optional Enhancements
- [ ] Custom fields per asset type/category
- [ ] Unified Contracts Board
- Notes/PR:

---

## Cross-Cutting
- [ ] Database indexes on vendor_id/product_id
- [ ] Validation rules and error states
- [ ] E2E smoke: create vendor→product→po→receive→asset→contract

## Links
- Plan: `Upcomming/Must/upgradetofreshservice.md`
- Owner: (assign)
- Target dates: (fill)

