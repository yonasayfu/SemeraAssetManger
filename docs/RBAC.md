# RBAC: Roles, Permissions, and Access Model

This document describes who can access what, how access is enforced (routes + policies), and how the UI respects permissions.

## Roles
The app uses spatie/permission with the following roles:

- Admin: full superuser. Receives all permissions via seeder and bypasses sidebar gating.
- Manager: can view + create + update most modules; cannot delete by default; can export reports; can use tools export (not import).
- Technician: can view most modules; can use Tools Import (permission), but see note on route guard below.
- Staff: view‑only for most modules and reports.
- Auditor: view + export on lists/reports; read‑only.
- ReadOnly: basic view‑only.
- External: minimal (dashboard only).

Source: `database/seeders/RolePermissionSeeder.php`.

## Permission Taxonomy
Core and new Catalog & Procurement permissions seeded:

- dashboard.view
- alerts.view
- assets.view|create|update|delete
- maintenance.view, warranty.view, audits.view
- lists.view, lists.export
- reports.view, reports.export
- tools.view, tools.import, tools.export
- setup.manage, activity-logs.view, mailbox.view|process, roles.manage
- vendors.view|create|update|delete
- products.view|create|update|delete
- contracts.view|create|update|delete
- purchase-orders.view|create|update|delete
- software.view|create|update|delete

Admin gets all. Managers get view/create/update for new modules. Other roles get subsets.

## Enforcement Layers

1) Route middleware (web.php)
- Each module is grouped with `permission:*.view` for Index/Create/Edit routes.
- Exports (CSV) on lists and ReportBuilder are additionally guarded with `role:Admin`.
- Tools Import/Export endpoints require `permission:tools.view` at the group level, with per‑route `role:Admin` for import/export.

2) Policies (AuthServiceProvider)
- Explicit policy mapping for Vendor, Product, Contract, PurchaseOrder, Software.
- Laravel authorizes per model/action when controllers call `$this->authorize()` (CRUD controllers across the app).
- `Gate::before` grants Admin a global bypass.

3) UI gating (Inertia + Vue)
- Sidebar: items are hidden if the user lacks permission; Admin sees all (bypass in `AppSidebar.vue`).
- Lists: toolbar ‘Add’ buttons are visible only if the user has `*.create`.
- Actions column shows Edit/Delete only if the user has `*.update`/`*.delete` respectively.
- Export buttons on lists are visible for Admin only (UI checks role; routes also enforce Admin).

## Module Access Matrix (summary)

- Vendors / Products / Contracts / Purchase Orders / Software
  - View: Admin, Manager, Technician, Staff, Auditor, ReadOnly
  - Create/Update: Admin, Manager
  - Delete: Admin (Manager has no delete by default)
  - Export CSV (list pages): Admin only

- Reports (incl. Contracts/POs/Software)
  - View: Admin, Manager, Staff, Auditor
  - Export CSV: Admin only

- Tools
  - View: Admin, Manager, Technician
  - Import (endpoints): Admin only (route level)
  - Export (Tools center): Admin only (route level)

- Alerts/Dashboard/Lists
  - View: most roles as seeded; actions limited by underlying module permissions.

## Notable Details

- Admin sidebar visibility: `resources/js/components/AppSidebar.vue` grants Admin visibility for all items regardless of permission.
- Route guards on list CSV exports and Tools Import/Export require `role:Admin`, even if a role has `tools.import` / `tools.export` permissions — route wins. Adjust if you want Managers/Technicians to run these endpoints by replacing `->middleware('role:Admin')` with `->middleware('permission:tools.import')` or similar.
- Controllers also call `$this->authorize()` (e.g., Assets) to enforce policies per model.

## How To Change Access

- Change seeding: edit `database/seeders/RolePermissionSeeder.php`, then run `php artisan db:seed --class=RolePermissionSeeder` (in production, run once and manage via UI or direct DB updates/services).
- Change route guards: edit `routes/web.php` to replace `permission:*` or `role:Admin` middleware as needed.
- Change policy logic: add granular checks in `app/Policies/*Policy.php`.
- Change UI gating: In the page’s Vue file, update `can('*.create')` etc., or add role checks when needed.

## Testing Access

- Create a user per role and verify:
  - Sidebar visibility matches role capabilities.
  - Index pages load; Add buttons appear based on `*.create`.
  - Actions (Edit/Delete) appear based on `*.update`/`*.delete`.
  - Export buttons: visible only for Admin; routes reject non‑Admin.
  - Tools Import/Export endpoints: reject non‑Admin at route level.

- Logs and errors: 403 pages are rendered via Inertia if authorization fails.

