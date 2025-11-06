# ASLM Asset Manager — Executive Presentation

## Overview
- Purpose: Replace subscription to AssetTiger with an in‑house, feature‑rich asset management app tailored to ASLM.
- Stack: Laravel 12, Inertia, Vue 3, Tailwind.
- Status: Refactor complete, dashboard live, import/export aligned, admin controls in place, UI/UX unified.

## Why Build Our Own
- Cost: Eliminate recurring licensing/subscription fees.
- Control: Customize features, data ownership, and integrations.
- Extensibility: Add ITSM‑style features (Freshdesk/Freshworks inspirations) not offered in our current plan.

## Highlights Delivered
- Assets: CRUD + operations (checkout/in, lease/return, reserve, move, dispose), maintenance, warranty.
- Catalog & Procurement: Vendors, Products, Contracts, Purchase Orders (items + receive), Software inventory.
- Reports: Contracts, Purchase Orders, Software via ReportBuilder, CSV export (chunked streaming).
- Dashboard: Realtime metrics + Catalog summary (contracts expiring, POs due, software seats).
- Alerts & Notifications: Daily alerts for maintenance/warranty; contracts expiring; POs due/overdue; mail + in‑app notifications.
- Import/Export: Admin‑only; templates for vendors/products/contracts/POs/software; CSV export on lists.
- Global search & access control: Sanctum + spatie/permission; Admin visibility tuned for Catalog.
- Clearance: Staff “My Assets” with selection → request; Admin review (remarks, approve/reject) with optional auto check‑in; PDF with header/footer; emails to staff + HR (Company hr_email fallback).

## Comparison: AssetTiger vs ASLM App
- Core Inventory
  - AssetTiger: Assets + basic categories, sites, locations.
  - ASLM: Same, with extended maintenance, leases, and reservations out of the box.
- Import/Export
  - AssetTiger: Standard CSV/XLSX; format‑dependent.
  - ASLM: Accepts AssetTiger headers; resolves names → IDs automatically; admin‑only.
- Dashboard
  - AssetTiger: Prebuilt widgets.
  - ASLM: Realtime metrics, donut chart, upcoming maintenance/leases/warranties, extensible.
- Permissions
  - AssetTiger: Built‑in roles.
  - ASLM: Fine‑grained permissions; import/export enforced for Admin.
- Extensibility
  - AssetTiger: Limited customization.
  - ASLM: Full code ownership; roadmap to ITSM features (tickets, CMDB, software licensing, discovery).

## Demo Walkthrough
- Dashboard: Overview cards → Donut chart → Upcoming events → Activity/export widgets.
- Assets: create/edit; operations (checkout/in, lease/return, reserve, move, dispose).
- Lists: asset/maintenance/warranty/audit lists with export (Admin‑only).
- Tools: import/export center (Admin‑only).
- Team: staff management; roles & permissions.
- Clearance: Staff → My Assets (select) → Request; Staff submits draft; Admin reviews/remarks → Approve (auto check‑in optional) → PDF generated & emailed; Staff downloads PDF from Clearances.

## Roadmap (near‑term)
- Validation hardening across CRUD + import jobs.
- Download Center for batched exports; XLSX output where needed.
- CMDB relationships + licensing; discovery import.
- Clearance: e‑signature capture, per‑item inline edit and auto check‑in of open checkouts/leases.
- Ticketing/ITSM: incidents, requests, automation (Freshdesk‑inspired).

## Risks & Mitigations
- Hosting limits: choose appropriate GoDaddy tier; prefer cPanel + SSH access.
- Backups: automated DB + storage backups.
- Performance: DB indexes added; further optimize as data grows.

## Call to Action
- Approve pilot rollout to staging → GoDaddy hosting.
- Green‑light phase 2 (ITSM features) after pilot feedback.
