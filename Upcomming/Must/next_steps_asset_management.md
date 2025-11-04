# Next Steps: Asset Management Project Roadmap

This document summarizes the current implementation status of the Asset Management project, based on `MyNewProjectRoadmap.md` and `TASK_CHECKLIST.md`, and outlines a comprehensive roadmap for all remaining tasks from backend to frontend.

## 1. Project Overview & Current Status

The project is built on Laravel 12 + Inertia + Vue 3 and is a single-tenant asset management system. Significant progress has been made, particularly in establishing the core boilerplate and refactoring the "person" module to "staff".

**Key Implemented Areas (as of last review):**

*   **Core Boilerplate**: Initial setup, authentication, Spatie Permission (RBAC), and Spatie Activity Log are integrated.
*   **"Staff" Module Refactoring**: All instances of "person" and "people" have been replaced with "staff" across documentation, database schema references, backend code (models, controllers, requests, traits), and initial frontend components.
*   **Asset Operations Sidebar Fix**: The sidebar for asset operation selection is implemented.
*   **Alert System (Partially Complete)**: Backend services, controllers, and notifications for alerts are mostly in place. Frontend Vue pages for various alerts exist.
*   **Reports Module (Mostly Complete)**: Database migrations, backend services, and controllers for various report types are implemented. Frontend report pages and a basic report builder are also present.
*   **Asset Detail Tabs (Mostly Complete)**: Backend tab endpoints and frontend UI components for displaying asset details, events, photos, documents, warranty, maintenance, reservations, audit history, and activity logs are largely implemented.
*   **Lists Module (Complete)**: Asset, Audit, Maintenance, and Warranty lists are implemented with shared UX and export functionality.
*   **Maintenance & Warranty Modules (Complete)**: Backend controllers, services, and jobs, along with frontend CRUD pages, are implemented for Maintenance and Warranty.
*   **Audit Workflow (Complete)**: Backend controllers, services, and frontend wizard/scan/report pages for audits are implemented.
*   **Dashboard Enhancements (Complete)**: Backend extensions for calendar/chart data and dashboard preference management, along with frontend calendar, charts, KPIs, and widget management, are implemented.
*   **Import/Export Multi-Entity (Complete)**: Backend controllers and jobs for importing various entities (Sites, Locations, Categories, Departments, Maintenance, Warranties) are implemented. Frontend import/export pages with entity selectors, column mapping, and validation are present.
*   **Permissions & Policies (Partially Complete)**: Policies for various modules (Asset, Maintenance, Warranty, Audit, Report, Site, Location, Category, Department, Staff, Customer) are created, and authorize checks are added in many controllers. Role-based sidebar filtering and permission checks in views are also implemented.

## 2. Detailed Remaining Tasks / Roadmap

This roadmap consolidates all outstanding items, prioritizing foundational backend cleanup before moving to frontend refinements and comprehensive testing.

### Phase 1: Backend File Refactoring and Cleanup (High Priority)

These tasks are crucial for a clean and consistent backend.

1.  **Rename Request Files**:
    *   Rename `app/Http/Requests/UserStoreRequest.php` to `app/Http/Requests/StaffStoreRequest.php`.
    *   Rename `app/Http/Requests/UserUpdateRequest.php` to `app/Http/Requests/StaffUpdateRequest.php`.
    *   **Action**: Update their contents to reflect the new class names and ensure they correctly validate for the `App\Models\Staff` model. *(This was partially completed in previous interactions, but a full review is needed).* 
2.  **Rename Policy File**:
    *   Rename `app/Policies/PersonPolicy.php` to `app/Policies/StaffPolicy.php`.
    *   **Action**: Update its contents to reflect the new class name and operate on the `App\Models\Staff` model.
3.  **Rename Job File**:
    *   Rename `app/Jobs/ImportPersonsJob.php` to `app/Jobs/ImportStaffJob.php`.
    *   **Action**: Update its contents to reflect the new class name and import `App\Models\Staff`.
4.  **Rename Controller File**:
    *   Rename `app/Http/Controllers/PersonImportController.php` to `app/Http/Controllers/StaffImportController.php`.
    *   **Action**: Update its contents to reflect the new class name and handle importing `App\Models\Staff`.
5.  **Remove Old Model**:
    *   **Action**: Delete `app/Models/Person.php` (if it still exists and is not used).
6.  **Update Database Migrations (Review)**:
    *   **Action**: Thoroughly review all migration files to ensure:
        *   Any migration for creating a `people` table is removed or has been correctly renamed to `staff`.
        *   All foreign key constraints in other tables (e.g., `assets`, `checkouts`, `reservations`, `personal_access_tokens`) that previously referred to `people.id` or `users.id` now correctly refer to `staff.id`. This requires inspecting each relevant migration.
7.  **Update Database Seeder (Review)**:
    *   **Action**: Review `database/seeders/DatabaseSeeder.php` and any other relevant seeders (e.g., `RolePermissionSeeder.php`, `SampleDataSeeder.php`) to ensure they correctly create and interact with `Staff` records instead of `User` or `Person` models, and that permissions are aligned (`staff.manage`, etc.).

### Phase 2: Frontend (Vue) Component Refactoring (High Priority)

Ensuring the frontend fully reflects the "staff" changes and adheres to UI/UX consistency.

1.  **Rename Vue Pages**:
    *   Rename `resources/js/pages/Users/Index.vue` to `resources/js/pages/Staff/Index.vue`.
    *   Rename `resources/js/pages/Users/Create.vue` to `resources/js/pages/Staff/Create.vue`.
    *   Rename `resources/js/pages/Users/Show.vue` to `resources/js/pages/Staff/Show.vue`.
    *   Rename `resources/js/pages/Users/Edit.vue` to `resources/js/pages/Staff/Edit.vue`.
2.  **Update Vue Page Content**:
    *   **Action**: Modify all renamed Vue components to:
        *   Adjust prop names (e.g., from `user` to `staff`, `users` to `staff`).
        *   Update display logic and data iteration (e.g., `v-for="user in users"` to `v-for="staffMember in staff"`).
        *   Ensure all UI elements, labels, and text reflect "Staff" terminology.
        *   Update Inertia links and route names (e.g., `route('users.index')` to `route('staff.index')`).
3.  **Update Other Vue Components (Review)**:
    *   **Action**: Thoroughly review `resources/js/pages/Tools/Import.vue`, `resources/js/pages/Tools/Export.vue`, and any other Vue files that might still reference "person" or "user" as assignees, requesters, or in any other context. Update them to use "staff" terminology and data structures consistently. This includes any components handling asset checkouts, leases, or reservations.

### Phase 3: Codebase Review, Consistency & Testing (Ongoing/High Priority)

This phase ensures overall project quality, completeness, and adherence to requirements.

1.  **Global Codebase Search (Final Sweep)**:
    *   **Action**: Perform a comprehensive, case-insensitive search across *all* `.php`, `.vue`, `.js`, `.json`, and `.md` files for any remaining instances of "person", "people", or "user" (specifically where "user" refers to the `App\Models\Staff` concept, *not* the `auth()->user()` helper which refers to the currently authenticated user). Any remaining instances must be addressed.
2.  **Configuration Files (Review)**:
    *   **Action**: Review `config/auth.php` and any other relevant configuration files (e.g., broadcasting, services, session) to ensure they correctly reference the `App\Models\Staff` model for authentication, providers, and guards.
3.  **Tests (Update and Create)**:
    *   **Action**: Update any existing backend (PHPUnit/Pest) or frontend (Vitest/Jest) tests that reference `User` or `Person` models, controllers, policies, or data to use their `Staff` counterparts.
    *   **Action**: Create new tests for the `Staff` module's CRUD operations, permissions, and relationships to ensure full test coverage.
4.  **UI/UX Consistency Check (Manual)**:
    *   **Action**: Conduct a thorough manual review of the entire application's UI/UX. Ensure that the "Staff" module (and other modules after refactoring) aligns with the established patterns in the existing Staff/User/Roles modules for CRUD, print, export, and notifications, as requested in the original prompt. This includes:
        *   Consistent use of iconography for actions (View, Edit, Delete, Impersonate).
        *   Proper implementation of confirmation modals and success/error toasts for user actions.
        *   Dedicated print styles for index pages.
        *   Adherence to consistent form layouts, input styles, and data display.
5.  **Unfinished Boilerplate Tasks from `TASK_CHECKLIST.md`**:
    *   **Alert System**:
        *   Configure scheduler in `app/Console/Kernel.php` (Add hourly alert generation, Add daily alert checks).
    *   **Reports Module**:
        *   Implement dynamic filter options for the Report Builder (Populate select inputs from APIs, Cache lookup lists client-side).
        *   Implement Report Export Enhancements (Add XLSX/PDF formats, Persist export metadata via `DataExport`).
    *   **Lists Module**:
        *   Create `user_list_preferences` migration (optional) or add JSON field to users table for persistent column setup.
    *   **Polish & Testing**:
        *   Fix N+1 queries.
        *   Add eager loading where needed.
        *   Add caching for dashboard/reports.
        *   Update `README.md`, API documentation, User guide, Admin setup guide, Deployment instructions.
        *   Fix route duplications in `web.php`.
        *   Handle edge cases.
        *   Validation consistency.
        *   Error logging.

This comprehensive roadmap will guide the remaining development to bring the Asset Management project to full completion.
