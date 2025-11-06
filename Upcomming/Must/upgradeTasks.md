


# UI/UX Upgrade Tasks

This document outlines the remaining tasks for finalizing the UI/UX of the Semera Asset Manager project, with a focus on consistency, staff experience, and overall polish. We will refer to the existing Staff, Staff, and Roles modules as a boilerplate for best practices.

## Current Focus

- Finalizing UI/UX based on Staff/Staff/Roles modules.
- Applying consistent design patterns across all modules.
- Addressing any remaining polish and testing items.

## Tasks

### 1. Analyze Boilerplate Modules (Staff, Staff, Roles) - **Completed**
- [x] Identified key UI/UX elements, patterns, and components used in Staff, Staff, and Roles modules.
- [x] Documented consistent styling, interactions, and responsiveness.
- [x] Noted areas for improvement within these modules.

**Summary of Findings:**
*   **Consistent Elements:** `AppLayout.vue`, `GlassCard.vue`, `Pagination.vue`, table structures, search/filter inputs, `confirmDialog`, `useToast`.
*   **Icon-based Actions:** `Staff/Index.vue` and `Staff/Index.vue` use icons for actions (View, Edit, Delete, Impersonate).
*   **Form Structure:** Consistent use of `GlassCard` for sections, standard inputs, and `InputError.vue`.
*   **Permissions Display:** Consistent badge styling for roles/permissions.
*   **Print Styles:** Dedicated print styles for Staff and Staff index pages.

**Inconsistencies/Areas for Improvement:**
*   **Roles Index Page Toolbar:** Does not use `ResourceToolbar.vue` (should be refactored).
*   **Roles Index Page Action Buttons:** Uses text buttons instead of icons (should be converted to icons).
*   **`route()` helper usage:** Needs a final sweep to replace with explicit paths.
*   **Empty states for lists:** Could be more visually engaging.

### 2. General UI/UX Improvements (Based on TASK_CHECKLIST.md - Phase 10)
- [ ] Implement consistent styling across all pages.
- [ ] Add loading states for all asynchronous operations.
- [ ] Enhance error handling and staff-friendly error messages.
- [ ] Design and implement empty states for lists and data tables.
- [ ] Integrate success toasts/notifications for staff actions.
- [ ] Implement confirmation dialogs for destructive actions.

### 3. Module-Specific UI/UX Refinements
- [x] **Roles Module (Refinement based on boilerplate analysis)**
    - [x] Refactor `Roles/Index.vue` to use `ResourceToolbar.vue` for page title and create button.
    - [x] Convert "Edit" and "Delete" buttons in `Roles/Index.vue` to icon-based actions (e.g., `Edit3`, `Trash2`) for consistency.
- [x] **Setup CRUD pages (Company)**
    - [x] Refactor `Company/Index.vue` to use `AppLayout`, `ResourceToolbar`, `GlassCard`, and icon-based actions.
    - [x] Refactor `Company/Create.vue` and `Company/Edit.vue` to use `AppLayout`, `GlassCard`, `GlassButton`, `InputError`, and `useToast` for consistent forms and feedback.
- [x] **Setup CRUD pages (Sites)**
    - [x] Refactor `Site/Index.vue` to use `AppLayout`, `ResourceToolbar`, `GlassCard`, search, pagination, and icon-based actions.
    - [x] Refactor `Site/Create.vue` and `Site/Edit.vue` to use `AppLayout`, `GlassCard`, `GlassButton`, `InputError`, and `useToast` for consistent forms and feedback.
- [x] **Setup CRUD pages (Locations)**
    - [x] Refactor `Location/Index.vue` to use `AppLayout`, `ResourceToolbar`, `GlassCard`, search, pagination, and icon-based actions.
    - [x] Refactor `Location/Create.vue` and `Location/Edit.vue` to use `AppLayout`, `GlassCard`, `GlassButton`, `InputError`, and `useToast` for consistent forms and feedback.
- [x] **Setup CRUD pages (Departments)**
    - [x] Refactor `Department/Index.vue` to use `AppLayout`, `ResourceToolbar`, `GlassCard`, search, pagination, and icon-based actions.
    - [x] Refactor `Department/Create.vue` and `Department/Edit.vue` to use `AppLayout`, `GlassCard`, `GlassButton`, `InputError`, and `useToast` for consistent forms and feedback.
- [x] **Setup CRUD pages (Manage Dashboard)**
  - [x] Ensure consistent icon usage for actions (Eye, Edit3, Trash2) with confirm dialogs and toasts.
  - [x] Verify AppLayout and breadcrumbs are correctly applied.
  - [x] Confirm forms use explicit REST endpoints.

- [x] **Assets Auxiliary Pages (Lease)**
  - [x] Replace `route()` helper usages with explicit paths.
  - [x] Apply AppLayout and breadcrumbs consistently.

- [x] **Assets Auxiliary Pages (LeaseReturn)**
  - [x] Replace `route()` helper usages with explicit paths.
  - [x] Apply AppLayout and breadcrumbs consistently.

- [x] **Assets Auxiliary Pages (Move)**
  - [x] Replace `route()` helper usages with explicit paths.
  - [x] Apply AppLayout and breadcrumbs consistently.

- [x] **Assets Auxiliary Pages (Reserve)**
  - [x] Replace `route()` helper usages with explicit paths.
  - [x] Apply AppLayout and breadcrumbs consistently.

- [x] **Settings Module**
  - [x] Fix `TwoFactor.vue` breadcrumb to `/settings/two-factor`.
  - [x] Implement null guards or optional chaining for `Profile.vue` staff data.

- [x] **Other Modules**
  - [x] Update `Audits/Wizard.vue` to use explicit endpoints instead of `route()` for consistency.
  - [x] Update `Profile/NotificationPreferences.vue` to use explicit endpoints instead of `route()` for consistency.

### 4. Codebase Cleanup & Refinements
- [x] Conduct a final sweep to remove all remaining `route()` helper usages.
    - [x] `resources/js/pages/Maintenance/Create.vue`
    - [x] `resources/js/pages/Maintenance/Edit.vue`
    - [x] `resources/js/components/TwoFactorRecoveryCodes.vue`
    - [ ] `resources/js/components/reports/ReportBuilder.vue`
    - [ ] `resources/js/pages/Setup/Category/Edit.vue`
- [ ] Ensure all action buttons are converted to icons with proper confirmation and toasts.

### 5. Documentation Updates
- [ ] Update `TASK_CHECKLIST.md` to reflect the new `upgradeTasks.md` for UI/UX related items.
- [ ] Document any new UI/UX patterns or components implemented.

---

## Phase 3 — Asset Form Enhancements (Freshservice Upgrade)

- [x] Add Vendor selector on Assets Create/Edit
- [x] Add Product selector filtered by Vendor
- [x] Auto-calc warranty expiry hint from purchase_date + warranty_months
- [x] Show inline cost hint from product.unit_cost_minor + currency
- [x] Optional: Link Asset to PO line item (add `purchase_order_item_id` FK, UI selector)

---

**Last Updated:** 2025-11-05
**Status:** In Progress (Phase 3 nearly complete)

## Phase 4 — Filters & Lists

- [x] Assets: add filters for Vendor, Product, Used By (staff) + presets (Assigned/Unassigned)
- [x] Contracts: add filters for Vendor, Product, Used By (via asset)
- [x] Purchase Orders: add filters for Vendor, Product (via line items)
- [x] Software: add filters for Vendor, Type (SaaS/On‑prem)
- [ ] Optional: extend export to honor applied filters server-side

## Phase 5 — Reports (Contracts/POs/Software)

- [x] Add ReportService queries for Contracts, POs, Software
- [x] Add Report pages using ReportBuilder
- [x] Add controllers + routes under `/reports`
- [x] Wire sidebar links under Reports (RBAC: `reports.view`)
- [x] CSV export via RunReportController streaming

## Phase 6 — Tools Import/Export

- [x] Add import endpoints for Vendors, Products, Contracts, Purchase Orders (+ items), Software
- [x] Update Tools Import UI to include new entities
- [x] Update Tools Export UI to include new entities
- [ ] Implement parsing logic in import jobs (map columns, validations, upsert)
- [ ] Wire Download Center exports for new entities (or extend ReportService exports)

## Phase 7 — Dashboard Cards

- [x] Contracts expiring soon (30/60/90)
- [x] POs due this month (open)
- [x] Software seat usage (used/total, %)

## Phase 8 — Alerts & Notifications

- [x] Add alert generation for contracts expiring and POs due/overdue
- [x] Add Mail + in-app notifications for new alerts
- [x] Schedule send job daily; mark alerts as sent
- [ ] Optional: add alert-specific pages/filters if needed

## Phase 9 — Permissions & Policies

- [x] Verify seeding for vendors/products/contracts/purchase-orders/software abilities
- [x] Map policies in AuthServiceProvider
- [x] Guard routes with permission middleware
- [x] Sidebar entries RBAC-gated

## Phase 10 — Optional Enhancements

- [x] Add asset custom_fields (JSON) + UI repeater on Create/Edit
- [x] Add Contracts Board page with grouped columns
- [ ] Optional: category-level custom schema for dynamic fields
