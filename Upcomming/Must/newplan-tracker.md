# Production Readiness Tracker

Use this as a live checklist while we implement the plan.

## Database & Model
- [x] Backfill migration: `assigned_to` → `staff_id` (2025_11_04_000001)
- [x] Add Checkout relations and fix model (fillable, casts, methods)
- [x] Add Lease `lessee` accessors / relations
- [ ] Verify indexes (assets.status, assets.category_id, checkouts.status, etc.)

## Backend
- [ ] Review validations across controllers (exists/nullable/rules)
- [x] Wire Console Kernel schedules (alerts, recurring)
- [ ] Harden ReportService queries + pagination
- [ ] File uploads: validation + signed URLs where needed
- [ ] Policies: confirm coverage in Asset/Maintenance/Lease/Reservation/Report
- [ ] Error views: 404/403; exception mapping

## Frontend
- [x] Replace Person with Staff in flows
- [x] `Asset.staff_id` types/forms in Create/Edit
- [x] Lease page dual-mode (staff vs department) validation UX
- [x] Loading/skeleton + empty states for key pages
- [x] Confirm destructive confirms (dispose/return/checkin)
- [x] Align Staff UI with current schema (name/email/phone/status/avatar)


## Security & Ops
- [ ] Roles/permissions final review; protect exports
- [ ] Rate limiting key endpoints
- [ ] Logging format + rotation; activity logs verified
- [ ] .env.example completeness; config docs
- [ ] CI: run tests + build; Dockerfile + docker-compose
- [ ] Production build artifacts; caching (config, routes, views)

## Docs
- [x] Update schema (staff_id)
- [x] Update roadmap (staff alignment)
- [ ] OPERATIONS.md (deploy, queues, schedulers)
- [ ] Update TASK_CHECKLIST.md residual references
