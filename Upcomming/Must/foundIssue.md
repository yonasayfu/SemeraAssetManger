Issue Tracker — Manual Test Findings (ASLM)

- [x] ASLM-1 Alerts UI parity — Done
  - Area: Alerts module
  - Description: Pages for Assets Due, Assets Past Due, Leases Expiring, Maintenance Due/Overdue should match “Warranties Expiring” UX.
  - Expected: Consistent table/list UI with filters and exports.
  - Actual: Mixed/legacy styling.
  - Severity: Medium

- [x] ASLM-2 Asset edit vs create UI inconsistency — Done
  - Area: Assets/Edit
  - Description: Edit UI differs from Create; after create, the “photo” is not displayed on single view; Photos tab upload succeeds but image not visible.
  - Expected: Edit matches Create; asset hero photo visible; uploaded photos render.
  - Notes: Resolved by storage symlink + UI checks.
  - Severity: High

- [x] ASLM-3 Asset update method not allowed — Done
  - Area: Assets/Edit submit
  - Description: POST to /assets/{id} failed; switched to PUT.
  - Severity: High

- [x] ASLM-4 Upload document fails — Done
  - Area: Assets/Photos/Documents tabs
  - Description: Document upload not working (verify after symlink).
  - Expected: POST /assets/{id}/tabs/documents stores and refreshes list.
  - Severity: High

- [x] ASLM-5 Warranty add fails — Done
  - Area: Assets/Warranty tab
  - Description: “Failed to add warranty” (permissions adjusted; verify).
  - Severity: High

- [x] ASLM-6 Maintenance morph map error — Done
  - Area: Asset Maintenance create/update
  - Fix: Added morph map.
  - Severity: Critical

- [x] ASLM-7 Reserve action route method — Done
  - Area: Asset Reserve
  - Fix: Route uses invokable controller.
  - Severity: High

- [x] ASLM-8 Checkout/Checkin/Lease/Return/Dispose/Move routes — Done
  - Area: Asset operations
  - Fix: Routes updated to invokable controllers.
  - Severity: High

- [x] ASLM-9 Warranty morph map error — Done
  - Area: Warranties
  - Fix: Added morph map.
  - Severity: Critical

- [x] ASLM-10 Setup redirects route not found — Done
  - Area: Setup → Sites/Locations/Categories/Departments
  - Fix: Controllers redirect to setup.* routes.
  - Severity: High

- [x] ASLM-11 Vendors/Products/Contracts/POs/Software “Add” — Done
  - Area: Catalog/Procurement
  - Description: Ensure Add buttons show when user has create permission.
  - Severity: Medium

- [x] ASLM-12 Reports clarity — Done (see docs/REPORTS_GUIDE.md)
  - Area: Reports
  - Description: Provide guide and validate endpoints.
  - Severity: Medium

- [x] ASLM-13 Staff import job missing — Done
  - Area: Tools → Import → Staff
  - Fix: Renamed to ImportStaffJob and updated controller.
  - Severity: High

- [x] ASLM-14 Gallery single view permissions/UX — Done (permit with assets.view)
  - Area: Tools → Images/Documents
  - Description: 403 and poor layout on single view.
  - Severity: Medium

- [x] ASLM-15 Audits creation entry point — Done (Create Audit button + permissions)
  - Area: Audits
  - Description: Add Create button/flow.
  - Severity: Low

- [x] ASLM-16 Company info 404 — Done (fixed redirects/authorization)
  - Area: Setup → Companies
  - Description: Page not found.
  - Severity: Medium

- [x] ASLM-17 Help/Support pages content — Done (dynamic via SupportPage + admin editor at /help/pages)
  - Area: Help & Support
  - Description: Make content dynamic/editable.
  - Severity: Low




Asset Photo → asset_photo
Asset Tag ID → asset_tag
Description → description
Purchase Date → purchase_date
Cost → cost
Status → status
Purchased from → purchased_from
Serial No → serial_no
Site → site
Location → location
Category → category
Department → department
Assigned to → assigned_to
Project code → project_code
Asset Type → asset_type
Manufacturer → manufacturer
Model → model
Model No → model_no
Model Year → model_year
Model Description → model_description
Model Serial No → model_serial_no
Model Cost → model_cost
Model Purchase Date → model_purchase_date
Model Purchased from → model_purchased_from
Model Status → model_status
Model Assigned to → model_assigned_to
Model Project code → model_project_code
Model Category → model_category
Model Department → model_department
Model Site → model_site
Model Location → model_location
Model Manufacturer → model_manufacturer
Model Model → model_model
Model Model No → model_model_no
Model Model Year → model_model_year
Model Model Description → model_model_description
Model Model Serial No → model_model_serial_no
Model Model Cost → model_model_cost
Model Model Purchase Date → model_model_purchase_date
Model Model Purchased from → model_model_purchased_from
Model Model Status → model_model_status
Model Model Assigned to → model_model_assigned_to
Model Model Project code → model_model_project_code
Model Model Category → model_model_category
Model Model Department → model_model_department
Model Model Site → model_model_site
Model Model Location → model_model_location
Model Model Manufacturer → model_model_manufacturer
Model Model Model → model_model_model
Model Model Model No → model_model_model_no
Model Model Model Year → model_model_model_year
Model Model Model Description → model_model_model_description
Model Model Model Serial No → model_model_model_serial_no
Model Model Model Cost → model_model_model_cost
Model Model Model Purchase Date → model_model_model_purchase_date
Model Model Model Purchased from → model_model_model_purchased_from
Model Model Model Status → model_model_model_status
Model Model Model Assigned to → model_model_model_assigned_to
Model Model Model Project code → model_model_model_project_code
Model Model Model Category → model_model_model_category
Model Model Model Department → model_model_model_department
Model Model Model Site → model_model_model_site
Model Model Model Location → model_model_model_location
Model Model Model Manufacturer → model_model_model_manufacturer
Model Model Model Model → model_model_model_model
Model Model Model Model No → model_model_model_model_no
Model Model Model Model Year → model_model_model_model_year
Model Model Model Model Description → model_model_model_model_description
Model Model Model Model Serial No → model_model_model_model_serial_no
Model Model Model Model Cost → model_model_model_model_cost
Model Model Model Model Purchase Date → model_model_model_model_purchase_date
Model Model Model Model Purchased from → model_model_model_model_purchased_from
Model Model Model Model Status → model_model_model_model_status
Model Model Model Model Assigned to → model_model_model_model_assigned_to
Model Model Model Model Project code → model_model_model_model_project_code
Model Model Model Model Category → model_model_model_model_category
Model Model Model Model Department → model_model_model_model_department
Model Model Model Model Site → model_model_model_model_site



Totally fair question. Here’s how headers, mapping, and the database relate — and what you can (and don’t need to) change.

How mapping works vs. the database

You do NOT need to rename database columns. The importer maps your CSV headers to “target fields”, then writes to the real DB columns/relations.
Targets → database columns/relations:
asset_tag → assets.asset_tag
description → assets.description
purchase_date → assets.purchase_date
cost → assets.cost
status → assets.status
purchased_from → assets.purchased_from
serial_no → assets.serial_no
project_code → assets.project_code
site → resolves by name to Sites, stores assets.site_id
location → resolves by name to Locations, stores assets.location_id
category → resolves by name to Categories, stores assets.category_id
department → resolves by name to Departments, stores assets.department_id
assigned_to → resolves to Staff.id, stores assets.staff_id
asset_photo → stored to assets.photo (path string)
Where those columns live

assets table has the columns the importer writes to:
site_id, location_id, category_id, department_id, staff_id, and the scalar fields (asset_tag, description, etc.).
database/migrations/2025_10_27_121813_create_assets_table.php
So, should you rename your CSV headers?

Optional. You don’t have to. The mapping step lets you connect any header to the correct target.
If you rename to match our common labels (e.g., “Asset Tag ID” → “asset_tag”), the auto‑map will prefill more for you — but it’s not required.
About Site and Location confusion

Your file shows “South Africa” and “Office 006 Open Space”. Typically:
Site is the larger entity (e.g., South Africa)
Location is the sub-area (e.g., Office 006 Open Space)
You do not need to change DB names. Just map those two headers to the correct targets in the UI:
Map the “Site” column to target “site” (values: South Africa)
Map the “Location” column to target “location” (values: Office 006 Open Space)
If the file’s labels are reversed, you can swap mapping in the UI (no DB changes necessary). If you prefer, you can also rename the CSV headers themselves — but it’s optional.
Why Dry Run shows “would be created”

The importer resolves by name. If a Site/Location/Category/Department doesn’t exist and “Create missing …” is checked, Dry Run warns it will create them (once per unique name). These are non‑blocking warnings.
If you want it even smarter

I can add a one‑click “Swap Site/Location” toggle to the mapping screen (useful if those two are frequently flipped).
I can also extend the auto‑map synonyms so your exact labels auto‑map every time.
Tell me which you prefer:

Add a “Swap Site/Location” button now
Or extend auto‑map synonyms for your headers
Or both

---

Recent Fixes and Enhancements (current build)

- ASLM-18 Vite alias/components resolution — Fixed
  - Issue: `@/Components/*` imports failed due to alias resolving to filesystem root.
  - Fix: `vite.config.ts` now resolves `@` to project `resources/js`; missing Breeze components were replaced with existing UI components.

- ASLM-19 Import controller job classes — Fixed
  - Issue: Several import controllers contained templated job names (parse errors on load).
  - Fix: Replaced with the correct job classes (e.g., `ImportProductsJob`, `ImportDepartmentsJob`, `ImportSitesJob`, etc.).

- ASLM-20 Galleries UX — Improved
  - Lightbox previews on click; dedicated detail pages for images/documents.
  - For placeholder images, “Open original” links to the Asset page instead of the placeholder file.

- ASLM-21 Asset list UX — Improved
  - Compact actions, single-line button groups, continuous row numbering across pages.
  - Export Columns and Table Columns buttons moved next to the “Dense rows/cards” toggle.
  - Added Clear Filters; filter dropdowns aligned to consistent height.

- ASLM-22 Audits module — Stabilized + seeded
  - Tools → Audits list with Create Audit CTA and filters.
  - Scan/Report pages corrected (removed template `route()` errors), restyled to match assets.
  - Seed data creates Ongoing + Completed audits for demo. See `auditflow.md`.
