# Staff Clearance Module — Plan

## Objective
Implement a Clearance flow so staff can request clearance when resigning/transitioning, and admins can validate all organization property is returned/settled. On approval, a signed PDF is generated and sent to the staff member and HR.

## Roles & Permissions
- `Staff` (requester):
  - View their assigned/issued assets (My Assets)
  - Start a clearance request (prefilled with their assets)
  - Upload attachments and add notes
  - View request status and download the final PDF
- `Admin` (approver):
  - View all clearance requests
  - Review/modify item list (mark as returned/waived/missing)
  - Add comments, request changes, approve/reject
  - Trigger PDF generation and notifications
- Permissions (Spatie):
  - `clearances.request` (staff)
  - `clearances.view` (staff can view own; admins all)
  - `clearances.manage` (admins manage requests)
  - `clearances.approve` (admins approve)

## Data Model
- `clearances`
  - id, staff_id (who is being cleared)
  - requested_by (who initiated), status: draft|submitted|in_review|approved|rejected
  - submitted_at, approved_at, approved_by
  - hr_email (optional override), remarks (text), pdf_path (nullable)
  - meta (json) for future fields (e.g., signature blocks)
- `clearance_items`
  - id, clearance_id, asset_id (nullable for “other item” lines)
  - description (cached if not linked), condition_note, action: return|waive|keep|replace|pay
  - result: ok|missing|damaged
  - checked (bool) preselected
  - resolved_at

Relations:
- Staff 1—* Clearances
- Clearance 1—* ClearanceItems
- ClearanceItem *—1 Asset (optional)

Indexes:
- clearances: staff_id, status
- clearance_items: clearance_id, asset_id

## Workflow
1. Staff opens “My Assets”, reviews items, clicks “Request Clearance”.
2. Clearance draft is created, items prefilled from:
   - Assets assigned to staff (`assets.staff_id = user.id`)
   - Open checkouts/leases for staff
3. Staff can:
   - Remove items they never had, add “other” items, attach files, add remarks
   - Submit (status: submitted; notify admins)
4. Admin review:
   - See pending list; open request; mark each item as returned/waived/missing; add notes
   - Optionally trigger/verify check-ins for assets still out
   - Approve or reject
5. On approval:
   - Generate PDF (DomPDF) with organization header, staff details, item list, actions/results, signatures
   - Email PDF to staff and HR; store `pdf_path`
   - Optionally auto-check-in remaining items or create tasks

## UI/UX Pages
- `My Assets` (staff): table of their assets, print action
- `Clearances` (staff): list of own requests; buttons: New, View, Download PDF
- `Clearances` (admin): list + filters by status; actions: Review, Approve, Reject
- `Clearance Show/Edit`:
  - Header: staff info, status, submitted/approved dates
  - Items table: asset tag, description, action selector, result, notes
  - Attachments area
  - Submit/Approve/Reject buttons with confirmation

## PDF Template
- Header: Company, logo, document title “Staff Clearance Form”
- Staff: Name, Job Title, Department, Email, Last working day (optional)
- Items table: Tag/Description/Action/Result/Notes
- Summary: Admin remarks
- Signatures: Staff (optional e-sign or printed name), Admin, Date

## Notifications
- On submit: notify admins (role Admin or permission `clearances.manage`)
- On approve: notify staff + HR (use `hr_email` or company default)
- On reject: notify staff with reason

## Routes (proposed)
- Staff-facing
  - GET `/my/assets` → My assets list (printable)
  - GET `/clearances` (own)
  - POST `/clearances` (create draft + prefill items)
  - GET `/clearances/{clearance}` (own)
  - PUT `/clearances/{clearance}` (edit before submit)
  - POST `/clearances/{clearance}/submit`
  - GET `/clearances/{clearance}/pdf` (download if approved)
- Admin
  - GET `/admin/clearances` (index)
  - GET `/admin/clearances/{clearance}` (review)
  - PUT `/admin/clearances/{clearance}` (update items)
  - POST `/admin/clearances/{clearance}/approve`
  - POST `/admin/clearances/{clearance}/reject`

## Integration Details
- Prefill items via queries:
  - Assets: `assets.staff_id = staff.id`
  - Checkouts/Leases: open records where assignee/lessee is staff
- Optionally force check-in on approval or create check-in tasks
- ActivityLog entries for lifecycle events

## Security & Access
- Staff can only see their own clearances and assets
- Admins can see/manage all
- Validate item edits: staff cannot change after submit
- Rate limit PDFs to avoid abuse

## Implementation Phases
1. Schema & Models
   - Migrations for `clearances`, `clearance_items`
   - Eloquent models + policies + permissions seeding
2. My Assets view (read-only + print)
3. Clearance CRUD (staff): create draft, edit items, submit
4. Admin review UI: review items, approve/reject
5. PDF generation + email notifications
6. Optional automation: auto-check-in tasks on approval
7. Seeders + sample data

---

Note (current build)

- No changes were required in the latest iteration for the clearance flow itself. The surrounding UI improvements (compact buttons, consistent filters) have been applied globally, so the Clearances screens inherit the updated table/button styling where applicable.

## Acceptance Criteria
- Staff can view their assets and print list
- Staff can create a clearance request prefilled with their items
- Admin can review/modify items and approve/reject
- On approval, PDF generated, stored, and emailed to staff + HR
- All actions logged; permissions enforced

## Open Questions
- Do we auto-check-in assets on approval or require explicit check-ins?
- Should staff e-sign in-app, or is typed name sufficient?
- HR email: global default vs per-department?
