ASLM Procurement Module Plan
============================

Purpose
- Centralize procurement from request → quotes → approvals → PO → receiving → handover/registration → invoicing/payment → funder reporting.
- Support staff onboarding (IT + Procurement) and program purchases (NGO context with multiple funders and receiving orgs).

Key Stakeholders
- HR, IT, Procurement Officer, Finance/Admin, CEO/Approver, Program Manager, Vendor, Funder, Receiving Organization.

High‑Level Flows
- Staff onboarding: HR triggers → IT checks store → if not available, raise procurement request → quotes → approvals → PO → receive → handover → auto‑register assets → assign to staff.
- Program purchase: Program Manager/Asset Officer raises request for project → quotes/approval → PO → receive → asset or stock → funder reporting.

Phase Plan (Tasks)
1) Procurement Requests (Requisitions)
   - Request form (type: onboarding|project), requester, department, needed_by, justification, urgency.
   - Line items (description/category/product, qty, est cost, funding source/project/budget line, site/location).
   - Status: draft → submitted.

2) Quotations & Vendor Selection
   - Collect multiple quotes per request; upload PDFs; enter line pricing; currency/validity.
   - Compare quotes; mark selected vendor/quote with rationale.

3) Approvals
   - Configurable approval chain (by amount/category/funding). Status: pending|approved|rejected.
   - Notifications to approvers; audit trail of decisions and comments.

4) Purchase Orders (reuse existing PO models)
   - Convert approved request/quote → PurchaseOrder + items (link request_id, quote_id).
   - PO PDF; status: open|submitted|partially_received|closed|cancelled.

5) Receiving (GRN) & Handover
   - Goods Receipt (partial allowed) per PO item; capture serials where applicable.
   - Auto‑create Asset records for asset‑tracked items (link purchase_order_item_id), or update stock for consumables.
   - Handover: assign asset to staff/department/location with signed acknowledgment (attachment).

6) Invoicing & Payments
   - Record vendor invoice(s) linked to PO; approvals as needed; payment records (date/ref/amount).

7) Funders & Reporting
   - Track funder/project/budget line per request/line/PO line.
   - Reports: spend by funder/project/period, lead time, vendor performance, outstanding POs.

8) Vendor Management
   - Maintain vendor scorecards, SLA flags, categories, preferred vendor lists.

9) Governance & Audit
   - Threshold policies (quote count by amount, approvals required), change history, export.


Data Model (DB Schema)
- procurement_requests
  - id, number, type (onboarding|project), requester_id, department_id, program_manager_id nullable,
    justification text, urgency enum, needed_by date, status enum (draft|submitted|approved|rejected|po_issued|cancelled),
    created_by, approved_at/by nullable, attachments json nullable, timestamps.

- procurement_request_items
  - id, request_id, product_id nullable, category_id nullable, description, qty numeric,
    est_unit_cost_minor int, currency char(10), site_id nullable, location_id nullable,
    funding_source_id nullable, project_id nullable, budget_line_id nullable, timestamps.

- quotations
  - id, request_id, vendor_id, received_at, valid_until nullable, currency, total_minor int,
    status enum (received|selected|rejected), attachment_path nullable, notes, timestamps.

- quotation_items
  - id, quotation_id, request_item_id, unit_cost_minor int, qty numeric, total_minor int, timestamps.

- approvals (generic, reusable)
  - id, approvable_type, approvable_id, step smallint, approver_id nullable, role_key nullable,
    threshold_minor nullable, status enum (pending|approved|rejected|skipped),
    decided_at nullable, comment nullable, timestamps.

- goods_receipts
  - id, purchase_order_id, received_by, received_at, reference nullable, notes, timestamps.

- goods_receipt_items
  - id, goods_receipt_id, purchase_order_item_id, qty_received numeric,
    serials json nullable, condition enum nullable, timestamps.

- invoices
  - id, purchase_order_id, vendor_id, number, invoice_date, amount_minor int, currency,
    status (submitted|approved|paid|rejected), attachment_path nullable, timestamps.

- payments
  - id, invoice_id, paid_at, amount_minor int, method nullable, reference nullable, attachment_path nullable, timestamps.

- funders
  - id, name, contact_email nullable, notes nullable, timestamps.

- projects (grants)
  - id, funder_id, name, code, start_date, end_date, budget_minor int nullable, notes, timestamps.

- budget_lines
  - id, project_id, code, name, ceiling_minor int nullable, spent_minor int default 0, notes, timestamps.

- receiving_organizations
  - id, name, contact_name, email nullable, phone nullable, notes, timestamps.

Augment existing tables
- purchase_orders: add request_id nullable, quotation_id nullable.
- purchase_order_items: add funding_source_id nullable, project_id nullable, budget_line_id nullable, asset_track boolean default true.
- assets: already has purchase_order_item_id; leverage for auto‑registration.

Permissions (RBAC)
- procurement.view, procurement.create, procurement.update, procurement.delete
- procurement.request, procurement.approve, procurement.receive, procurement.report
- finance.view, finance.approve, finance.pay
- po.manage (existing PurchaseOrder policies remain)

UI & Routes (Inertia)
- /procurement/requests (index/create/show)
- /procurement/requests/{req}/quotes (collect/select)
- /procurement/requests/{req}/approve (workflow actions)
- Convert to PO → reuses /purchase-orders with linkage shown
- /purchase-orders/{po}/receive (GRN create/partial)
- /purchase-orders/{po}/invoices (index/create) and /invoices/{invoice}/pay
- Reports: /reports/procurement (filters: date range, funder, project, vendor, status)

Integrations & Automation
- Staff onboarding: from Staff profile or HR checklist, prefill a procurement request with role kit (laptop + accessories) templates.
- Auto asset registration on GRN for asset_track items (n assets = qty), with default category/site/location from PO item, and link to POI; optional auto‑assign to requester upon “handover”.
- Notification hooks: request submitted, approval needed, PO issued, items received, invoice due.

Roadmap (Milestones)
- M1: Requests + Items + basic listing, permissions, notifications
- M2: Quotations (entry/upload) + vendor selection screen
- M3: Approval workflow (static chain by thresholds) + audit trail
- M4: PO conversion (link to existing PurchaseOrders) + PDF
- M5: Receiving (GRN) + partial receipts + auto asset creation
- M6: Handover flow + assignment to staff + docs
- M7: Invoices + payments + finance approvals
- M8: Funder/Project/Budget reporting (exports + dashboards)
- M9: Vendor performance + SLA metrics

Reporting Catalogue
- Spend by funder/project/period
- Lead time (request → PO, PO → GRN, GRN → handover)
- Open requests by status; overdue POs; partially received POs
- Asset capitalization list from POs (for auditors)
- Vendor performance (on‑time delivery %, invoice discrepancies, defect rates)

Data Migration & Risks
- Introduce new tables with additive migrations; backfill links from existing POs when converting from request.
- Ensure idempotent seeders for reference data (funders/projects/budget lines where applicable).
- Guardrails: enforce quote count by threshold; approvals cannot be bypassed; full audit logs.

Dependencies on Current Codebase
- Reuse existing Vendors, Products, Contracts, PurchaseOrders, PurchaseOrderItems.
- Use Assets.purchase_order_item_id to bind received assets to purchasing source.
- Use Notifications and Policies already in app; add new permission keys.

Next Steps (Implementation Tasks)
- Define migrations for: procurement_requests, procurement_request_items, quotations, quotation_items, approvals, goods_receipts, goods_receipt_items, invoices, payments, funders, projects, budget_lines, receiving_organizations; PO/POI columns.
- Add policies/permissions + seed RolePermissionSeeder.
- Add controllers + Vue pages for Requests, Quotes, Approvals, GRN, Invoices.
- Add auto‑asset creation service invoked on GRN.
- Add procurement reports (Eloquent queries + export service using existing export framework).

