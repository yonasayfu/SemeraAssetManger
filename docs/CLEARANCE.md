# Clearance Module

The Clearance module streamlines staff off‑boarding by tracking the return/waiver of organization property and issuing a formal PDF clearance.

## Features
- Staff “My Assets” list with multi‑select and landscape print
- Request a clearance (pre‑filled from selected assets)
- Save draft and submit for review
- Admin review with remarks, Approve/Reject, optional auto check‑in of returned assets
- PDF generation with header/logo/footer and clear status (APPROVED/REJECTED)
- Notifications: submit → approvers; approve → staff + HR (Company hr_email fallback); reject → staff

## Routes
Staff
- GET `/my/assets` — list assigned assets (printable)
- GET `/clearances` — list own clearance requests
- POST `/clearances` — create draft (optionally pass `asset_ids[]`)
- GET `/clearances/{clearance}` — view/update draft
- PUT `/clearances/{clearance}` — update draft items/remarks
- POST `/clearances/{clearance}/submit` — submit
- GET `/clearances/{clearance}/pdf` — download PDF (when approved)

Admin
- GET `/admin/clearances` — list all requests
- GET `/admin/clearances/{clearance}` — review
- PUT `/admin/clearances/{clearance}` — update remarks/items
- POST `/admin/clearances/{clearance}/approve` — approve (optional `auto_checkin=true`)
- POST `/admin/clearances/{clearance}/reject` — reject
- GET `/admin/clearances/{clearance}/pdf` — download PDF

## Permissions
- `clearances.view` — view own clearances; access My Assets, My Clearances
- `clearances.request` — create/submit own clearances
- `clearances.manage` — admin review/update
- `clearances.approve` — approve/reject + generate PDF

Seeded in `database/seeders/RolePermissionSeeder.php`. Admin gets all; Staff gets view/request; Managers can be granted manage/approve as needed.

## Schema
- Tables: `clearances`, `clearance_items` — see `docs/DATABASE_SCHEMA.md` for fields
- Company: `companies.hr_email` as default HR destination

## Implementation Notes
- Auto check‑in updates the asset to `status=Available` and clears `staff_id` for items marked return
- PDF: `resources/views/clearances/pdf.blade.php`; logo auto‑falls back to `public/images/asset-logo.svg`
- Notifications are standard queued mail notifications; ensure a queue worker is running

