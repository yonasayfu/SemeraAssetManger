# ✅ IMPLEMENTATION TASK CHECKLIST

> **Quick Reference** - Mark items as you complete them. See `IMPLEMENTATION_ROADMAP.md` for detailed specs.

---

## ✅ PHASE 0: Sidebar Asset Operations Fix

### Backend

- [x] Create `app/Http/Controllers/AssetOperationController.php`
- [x] Add new routes for asset operation selection pages in `routes/web.php`

### Frontend

- [x] Create `resources/js/Pages/Assets/AssetSelect.vue`
- [x] Update `resources/js/components/AppSidebar.vue` with new hrefs for asset operations

---

## 🟠 PHASE 1: Alert System (Week 1-2) - Mostly Complete (Scheduler config and alerts table migration skipped for now)

### Backend

- [x] Create `app/Services/AlertService.php`
    - [x] `checkOverdueCheckouts()` method
    - [x] `checkExpiringLeases()` method
    - [x] `checkMaintenanceDue()` method
    - [x] `checkMaintenanceOverdue()` method
    - [x] `checkWarrantiesExpiring()` method
    - [x] `checkAssetsDue()` method

- [x] Create `app/Jobs/GenerateAlertsJob.php`

- [ ] Configure scheduler in `app/Console/Kernel.php` (SKIPPED - Kernel.php missing)
    - [ ] Add hourly alert generation
    - [ ] Add daily alert checks

- [x] Create Alert Controllers
    - [x] `app/Http/Controllers/Alert/AssetsDueController.php`
    - [x] `app/Http/Controllers/Alert/AssetsPastDueController.php`
    - [x] `app/Http/Controllers/Alert/LeasesExpiringController.php`
    - [x] `app/Http/Controllers/Alert/MaintenanceDueController.php`
    - [x] `app/Http/Controllers/Alert/MaintenanceOverdueController.php`
    - [x] `app/Http/Controllers/Alert/WarrantiesExpiringController.php`

- [x] Create Notifications
    - [x] `app/Notifications/Alert/AssetOverdueNotification.php`
    - [x] `app/Notifications/Alert/LeaseExpiringNotification.php`
    - [x] `app/Notifications/Alert/MaintenanceDueNotification.php`
    - [x] `app/Notifications/Alert/WarrantyExpiringNotification.php`

### Frontend

- [x] Create Vue Pages
    - [x] `resources/js/Pages/Alerts/AssetsDue.vue`
    - [x] `resources/js/Pages/Alerts/AssetsPastDue.vue`
    - [x] `resources/js/Pages/Alerts/LeasesExpiring.vue`
    - [x] `resources/js/Pages/Alerts/MaintenanceDue.vue`
    - [x] `resources/js/Pages/Alerts/MaintenanceOverdue.vue`
    - [x] `resources/js/Pages/Alerts/WarrantiesExpiring.vue`

### Routes

- [x] Update `routes/web.php` with alert routes

---

## 🟠 PHASE 2: Reports Module (Week 3-4) (Priority: P0)

**Status:** Backend automation 2/2 | Frontend pages 11/11 | Builder components 4/4

### Database

- [x] Create `saved_reports` migration

- [x] Create `SavedReport` model

### Backend

- [x] Create `app/Services/ReportService.php`
    - [x] Asset report queries

    - [x] Maintenance report queries

    - [x] Checkout report queries

    - [x] Lease report queries

    - [x] Audit report queries

    - [x] Status report queries

    - [x] Transaction report queries

- [x] Create Report Controllers
    - [x] `app/Http/Controllers/Report/AutomatedReportController.php`
    - [x] `app/Http/Controllers/Report/CustomReportController.php`
    - [x] `app/Http/Controllers/Report/AssetReportController.php`
    - [x] `app/Http/Controllers/Report/AuditReportController.php`
    - [x] `app/Http/Controllers/Report/CheckoutReportController.php`
    - [x] `app/Http/Controllers/Report/LeasedAssetReportController.php`
    - [x] `app/Http/Controllers/Report/MaintenanceReportController.php`
    - [x] `app/Http/Controllers/Report/ReservationReportController.php`
    - [x] `app/Http/Controllers/Report/StatusReportController.php`
    - [x] `app/Http/Controllers/Report/TransactionReportController.php`
- [x] Create `app/Jobs/RunScheduledReportJob.php`
- [x] Add report scheduler to `app/Console/Kernel.php`

### Frontend

- [x] Create Report Pages
    - [x] `resources/js/Pages/Reports/Automated.vue`
    - [x] `resources/js/Pages/Reports/Custom.vue`
    - [x] `resources/js/Pages/Reports/Assets.vue`
    - [x] `resources/js/Pages/Reports/Audits.vue`
    - [x] `resources/js/Pages/Reports/Checkout.vue`
    - [x] `resources/js/Pages/Reports/LeasedAssets.vue`
    - [x] `resources/js/Pages/Reports/Maintenance.vue`
    - [x] `resources/js/Pages/Reports/Reservations.vue`
    - [x] `resources/js/Pages/Reports/Status.vue`
    - [x] `resources/js/Pages/Reports/Transactions.vue`
    - [x] `resources/js/Pages/Reports/Others.vue`

- [x] Create Report Builder Component
    - [x] Filter selection
    - [x] Column selection
    - [x] Date range picker
    - [x] Export format selector

### Routes

- [x] Update `routes/web.php` with report routes

---

## 🟠 PHASE 3: Asset Detail Tabs (Week 5) (Priority: P1)

**Status:** Backend tab endpoints 8/8 | Tab UI components 9/9

### Backend

- [x] Extend `AssetController@show` to support tabs
- [ ] Create tab-specific methods or endpoints:
    - [x] Events/History data
    - [x] Photos data
    - [x] Documents data
    - [x] Warranty data
    - [x] Maintenance history data
    - [x] Reservations data
    - [x] Audit history data
    - [x] Activity log data

### Frontend

- [x] Refactor `resources/js/Pages/Assets/Show.vue`
    - [x] Add tab navigation
    - [x] Lazy load tab content

- [ ] Create Tab Components
    - [x] `AssetDetailsTab.vue`
    - [x] `AssetEventsTab.vue`
    - [x] `AssetPhotosTab.vue`
    - [x] `AssetDocumentsTab.vue`
    - [x] `AssetWarrantyTab.vue`
    - [x] `AssetMaintenanceTab.vue`
    - [x] `AssetReservationsTab.vue`
    - [x] `AssetAuditHistoryTab.vue`
    - [x] `AssetActivityLogTab.vue`

## ✅ PHASE 4: Lists Module (Week 6) (Priority: Complete)

**Status:** Complete (12/12) - shared list UX + exports shipped

### Backend

- [x] Create `app/Http/Controllers/AssetListController.php`
- [x] Create `app/Http/Controllers/AuditListController.php`
- [x] Enhance existing list controllers with:
    - [x] Advanced filters
    - [x] Column selection
    - [x] Sorting
    - [x] Export

### Database

- [ ] Create `user_list_preferences` migration (optional)
- [ ] Or add JSON field to users table

### Frontend

- [x] Create/Update List Pages
    - [x] `resources/js/Pages/Lists/Assets/Index.vue`
    - [x] `resources/js/Pages/Lists/Audits/Index.vue`
    - [x] Update `resources/js/Pages/Lists/Maintenances/Index.vue`
    - [x] Update `resources/js/Pages/Lists/Warranties/Index.vue`

- [x] Create Reusable Components
    - [x] Advanced filter component
    - [x] Column picker component
    - [x] Bulk action component

### Routes

- [x] Update `routes/web.php` with list routes

---

## ✅ PHASE 5: Maintenance & Warranty (Week 7-8)

### Database

- [x] Add `recurring` fields to maintenances table (migration)
- [x] Add cost tracking fields if missing

### Backend - Maintenance

- [x] Create `app/Http/Controllers/MaintenanceController.php`
- [x] Create `app/Services/MaintenanceService.php`
- [x] Create `app/Jobs/CreateRecurringMaintenanceJob.php`
- [x] Add maintenance scheduler

### Backend - Warranty

- [x] Create `app/Http/Controllers/WarrantyController.php`
- [x] Add auto-expiry calculation logic
- [x] Create `app/Jobs/CheckWarrantyExpiryJob.php`

### Frontend - Maintenance

- [x] Create `resources/js/Pages/Maintenance/Index.vue`
- [x] Create `resources/js/Pages/Maintenance/Create.vue`
- [x] Create `resources/js/Pages/Maintenance/Edit.vue`
- [x] Create `resources/js/Pages/Maintenance/Show.vue`

### Frontend - Warranty

- [x] Create `resources/js/Pages/Warranty/Index.vue`
- [x] Create `resources/js/Pages/Warranty/Create.vue`
- [x] Create `resources/js/Pages/Warranty/Edit.vue`
- [x] Create `resources/js/Pages/Warranty/Show.vue`

### Routes

- [x] Add maintenance routes
- [x] Add warranty routes

---

## ✅ PHASE 6: Audit Workflow (Week 9)

### Backend

- [x] Create `app/Http/Controllers/AuditWizardController.php`
- [x] Create `app/Http/Controllers/AuditScanController.php`
- [x] Create `app/Services/AuditService.php`
- [x] Generate variance report logic

### Frontend

- [x] Create `resources/js/Pages/Audits/Wizard.vue`
    - [x] Step 1: Audit info (name, site, location)
    - [x] Step 2: Asset selection (category filter, manual IDs)
    - [x] Step 3: Review & start

- [x] Create `resources/js/Pages/Audits/Scan.vue`
    - [x] Asset search/scan
    - [x] Mark found/missing
    - [x] Add notes
    - [x] Photo upload (optional)

- [x] Create `resources/js/Pages/Audits/Report.vue`
    - [x] Variance summary
    - [x] Export options

### Routes

- [x] Add audit wizard routes
- [x] Add audit scan routes
- [x] Add audit report routes

---

## ✅ PHASE 7: Dashboard Enhancements (Week 10)

### Backend

- [x] Extend `DashboardController` with:
    - [x] Calendar events data
    - [x] Chart data (asset value by category)
    - [x] Fiscal year calculations

- [x] Create `app/Http/Controllers/ManageDashboardController.php` (enhance existing)
- [x] Store dashboard preferences

### Frontend

- [x] Enhance `resources/js/Pages/Dashboard.vue`
    - [x] Add calendar component
    - [x] Add charts (Chart.js or ApexCharts)
    - [x] Fiscal year KPIs

- [x] Create/Enhance `resources/js/Pages/Setup/ManageDashboard.vue`
    - [x] Widget selection
    - [x] Layout configuration (2, 3, 4 columns)
    - [x] Save preferences

- [x] Create Components
    - [x] `DashboardCalendar.vue`
    - [x] `DashboardChart.vue`
    - [x] `DashboardWidget.vue`

---

## ✅ PHASE 8: Import/Export Multi-Entity (Week 11)

### Backend

- [x] Create `app/Http/Controllers/ToolsController.php`
    - [x] Import landing page
    - [x] Export landing page

- [x] Create Import Controllers
    - [x] `PersonImportController.php`
    - [x] `SiteImportController.php`
    - [x] `LocationImportController.php`
    - [x] `CategoryImportController.php`
    - [x] `DepartmentImportController.php`
    - [x] `MaintenanceImportController.php`
    - [x] `WarrantyImportController.php`

- [x] Create Import Jobs
    - [x] `ImportPersonsJob.php`
    - [x] `ImportSitesJob.php`
    - [x] etc.

### Frontend

- [x] Create `resources/js/Pages/Tools/Import.vue`
    - [x] Entity type selector
    - [x] Template download
    - [x] File upload
    - [x] Column mapping
    - [x] Validation preview
    - [x] Import progress
- [x] Create `resources/js/Pages/Tools/Export.vue`
    - [x] Entity selector
    - [x] Filter selection
    - [x] Column selection
    - [x] Format selector

### Routes

- [x] Add import/export routes for each entity

---

## 🟠 PHASE 9: Permissions & Policies (Week 12)

### Backend

- [x] Create Policies
    - [x] `app/Policies/AssetPolicy.php`
    - [x] `app/Policies/MaintenancePolicy.php`
    - [x] `app/Policies/WarrantyPolicy.php`
    - [x] `app/Policies/AuditPolicy.php`
    - [x] `app/Policies/ReportPolicy.php`
    - [x] `app/Policies/SitePolicy.php`
    - [x] `app/Policies/LocationPolicy.php`
    - [x] `app/Policies/CategoryPolicy.php`
    - [x] `app/Policies/DepartmentPolicy.php`
    - [x] `app/Policies/PersonPolicy.php`
    - [x] `app/Policies/CustomerPolicy.php`

- [x] Add authorize checks in controllers (for `AssetController`)
- [x] Add authorize checks in controllers (for `MaintenanceController`)
- [x] Add authorize checks in controllers (for `WarrantyController`)
- [x] Add authorize checks in controllers (for `AuditWizardController`)
- [x] Add authorize checks in controllers (for `AuditScanController`)
- [x] Add authorize checks in controllers (for `ReportController`)
- [x] Add authorize checks in controllers (for `SiteController`)
- [x] Add authorize checks in controllers (for `LocationController`)
- [x] Add authorize checks in controllers
- [x] Update `PermissionSeeder` with all permissions

### Frontend

- [x] Add role-based sidebar filtering in `AppSidebar.vue`
- [x] Add permission checks in views (`@can` equivalents)

---

## ⚪ PHASE 10: Polish & Testing (Week 13-14)

### UI/UX

- [ ] Consistent styling across all pages
- [ ] Loading states on all async operations
- [ ] Error handling and messages
- [ ] Empty states for lists
- [ ] Success toasts/notifications
- [ ] Confirmation dialogs for destructive actions

### Performance

- [x] Add database indexes
    - [x] Assets table (asset_tag, status, site_id, location_id, category_id)
    - [x] Checkouts table (asset_id, due_at, status)
    - [x] Leases table (asset_id, end_at, status)
    - [x] Maintenances table (asset_id, scheduled_for, status)
    - [x] Alerts table (type, due_date, sent)
- [ ] Fix N+1 queries
- [ ] Add eager loading where needed
- [ ] Add caching for dashboard/reports

### Documentation

- [ ] Update README.md
- [ ] API documentation (if needed)
- [ ] User guide
- [ ] Admin setup guide
- [ ] Deployment instructions

### Bug Fixes

- [ ] Fix route duplications in web.php
- [ ] Handle edge cases
- [ ] Validation consistency
- [ ] Error logging

---

## 📊 PROGRESS TRACKER

### Overall Status

- [ ] Phase 1: Alert System (0/10)
- [ ] Phase 2: Reports Module (Priority P0 — backend base ready; automation + UI outstanding)
- [ ] Phase 3: Asset Detail Tabs (Priority P1 — ready to kick off)
- [x] Phase 4: Lists Module (Priority Complete - delivered)
- [x] Phase 5: Maintenance & Warranty (18/18)
- [x] Phase 6: Audit Workflow (10/10)
- [x] Phase 7: Dashboard Enhancements (8/8)
- [x] Phase 8: Import/Export Multi-Entity (15/15)
- [ ] Phase 9: Permissions & Policies (0/15)
- [ ] Phase 10: Polish & Testing (0/20)

### Total Tasks: 133

### Completed: 18

### Progress: 13%

---

## Quick Wins (Do These First)

1. ~~**Maintenance Module - Backend foundations** (P1, 2-3 days)~~ ✅ Completed Week 8
2. ~~**Maintenance Module - Vue screens** (P1, 3-4 days)~~ ✅ Completed Week 8

3. **Report Builder - Dynamic filter options** (P2, 2 days)
    - Populate select inputs from APIs (statuses, categories, sites, departments)
    - Cache lookup lists client-side for a smoother user experience

4. **Report Export Enhancements** (P2, 2-3 days)
    - Add XLSX/PDF formats alongside CSV using queued exports
    - Persist export metadata via `DataExport` for download tracking

---

## 📝 NOTES

- Check off items as you complete them
- Update progress tracker weekly
- Prioritize based on business needs
- Test thoroughly after each phase
- Deploy incrementally if possible

---

**Last Updated:** 2025-10-27  
**Status:** Ready for Implementation








# Executing final sweep now

I’ll finish the remaining conversions in one go:
- Setup CRUD pages (Company, Sites, Locations, Departments, Manage Dashboard)
- Assets aux pages (Lease, LeaseReturn, Move, Reserve)
- Tools/Import (done)
- Settings/Profile null-guard, Settings/TwoFactor breadcrumb URL
- Audits/Wizard, Profile/NotificationPreferences
- Convert any remaining “Actions” to icons + confirm + toasts
- Remove all remaining route() helper usages

# What’s already done in this phase

- **Sidebar**
  - Team + Account Settings groups added, with correct icons and RBAC gates.

- **Advanced/Persons**
  - Actions converted to icons (Eye, Edit3, Trash2) with confirm + toasts.

- **Settings/Auth**
  - Profile submits to `/settings/profile`.
  - Password (breadcrumb + submit) uses explicit paths.
  - TwoFactorChallenge uses explicit paths.
  - ForgotPassword uses `/forgot-password`.
  - TwoFactor page pending breadcrumb fix to `/settings/two-factor` (will apply now).

- **Assets**
  - Import wrapped with AppLayout, breadcrumbs; `POST /assets/import` with forceFormData.

- **Tools**
  - Import wrapped with AppLayout, breadcrumbs; `POST /tools/import/{entity}` with forceFormData.

- **Setup**
  - Categories/Create wrapped with AppLayout; `POST /setup/categories`.

# Next actions being applied

- Setup CRUD (Company, Sites, Locations, Departments, Manage Dashboard)
  - Replace route() in Create/Edit/Index pages.
  - AppLayout + breadcrumbs.
  - Index action buttons -> icons (Eye/Edit3/Trash2) with confirm delete + toasts.
  - Forms -> explicit REST endpoints.

- Assets aux (Lease, LeaseReturn, Move, Reserve)
  - Replace route() and add AppLayout + breadcrumbs consistently.

- Settings
  - TwoFactor.vue breadcrumb to `/settings/two-factor`.
  - Profile.vue guard `user` null (use safe defaults or optional chaining).

- Others
  - Audits/Wizard.vue and Profile/NotificationPreferences.vue: replace route() with explicit endpoints.

# Tracking

- Setup CRUD: in progress
- Final route() sweep: in progress

I’ll report back once all these are pushed and the route() usage count is zero, and CRUD pages show consistent icon actions and AppLayout/breadcrumbs.