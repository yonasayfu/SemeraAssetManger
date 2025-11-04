# Production Readiness Plan

Goal: bring the app to production quality, aligned with roadmap and schema, with clear deliverables and owners.

## Database & Data Model
- Migrate legacy `assigned_to` → `staff_id` safely (done; backfill migration added).
- Align relationships and eager loads: use `assignee` on `Asset`; remove stale aliases.
- Define Checkout and Lease relationships explicitly:
  - Checkout: `asset()`; `assignee` (non-morph: map to Staff for now); indexes on `asset_id`, `assignee_id`, `status`.
  - Lease: `asset()`; `lessee` accessors by `lessee_type` (`department|staff`).
- Add/verify constraints and indexes across tables (FKs, common filters, uniqueness).

## Backend (Laravel)
- Controllers: audit for validation consistency (ids use `exists:*`), nullable semantics, and authorization checks.
- Policies: ensure Asset, Maintenance, Lease, Reservation, Reports policies cover create/update/delete/view/operate.
- Services: finalize `ReportService` relations; replace placeholders with tested queries; paginate where needed.
- Jobs & Schedules: wire alert/recurring jobs in Console Kernel; add config for frequencies.
- File handling: validate uploads (MIME, size), storage disks; generate signed URLs where applicable.
- Notifications: verify mail config for alerts; unify notifiers.
- Error handling: consistent JSON Inertia responses; 404/403 pages; exception mapping.

## Frontend (Inertia + Vue 3)
- Types: ensure parity with backend DTOs (`Asset.staff_id`, etc.).
- Forms: use correct props/fields (`staff_id`); show helpful validation errors.
- Pages: Checkout/Lease flows reflect Staff vs Department choices; remove legacy Person references.
- Components: add loading/skeleton states; empty-state messages; confirm dialogs for destructive actions.
- Accessibility: labels-for inputs; keyboard nav; color contrast.


## Security & Ops
- AuthN/AuthZ: finalize roles/permissions; protect routes; restrict exports.
- Rate limiting for critical endpoints (login, exports, reports).
- Logging & audit: activity logs verified; structured logs to file.
- Config: `.env.example` complete; mail/storage/db/cache/queue; production cache config.
- Build: CI for lint/test/build; Dockerfile + docker-compose for local/prod; healthcheck endpoint.
- Assets: versioning, production vite build, CSP headers (optional).

## Documentation
- Update schema docs to reflect `staff_id` and related FKs (done).
- Update roadmap where staff/person diverged (done).
- Write OPERATIONS.md (deploy, queues, schedulers, storage, mail, backups).
- Update TASK_CHECKLIST.md for remaining cleanup items.

## Rollout
- Staging environment with seeded data.
- Migration + backup plan; fallback/rollback strategy.
- Monitoring: basic uptime check; error reporting (Sentry or logs).

