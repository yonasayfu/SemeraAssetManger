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
- Assets domain: CRUD, check‑out/in, lease/return, reserve, move, dispose, maintenance, warranty.
- Realtime dashboard: live metrics, donut chart by category, calendar of upcoming events.
- Import/Export: Admin‑only; export headers match AssetTiger legacy file; import resolves names/IDs, creates taxonomy.
- Global search: unified search across assets and staff.
- Access control: spatie/permission with admin‑gated import/export.
- UX polish: card widgets, empty states, confirmation dialogs; Inertia‑based 403/404/500 pages.

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

## Roadmap (near‑term)
- Validation hardening across all CRUD endpoints.
- More dashboard insights (trends per card, inline sparklines).
- CMDB relationships + software licensing.
- Ticketing/ITSM: incidents, requests, automation (Freshdesk‑inspired).

## Risks & Mitigations
- Hosting limits: choose appropriate GoDaddy tier; prefer cPanel + SSH access.
- Backups: automated DB + storage backups.
- Performance: DB indexes added; further optimize as data grows.

## Call to Action
- Approve pilot rollout to staging → GoDaddy hosting.
- Green‑light phase 2 (ITSM features) after pilot feedback.

