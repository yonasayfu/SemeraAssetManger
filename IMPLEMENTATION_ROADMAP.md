     18|- - ✅ Advanced modules (Person, Customer)
     19|+ - ✅ Advanced modules (Customer)
     20|
     21|**Missing Import Types:**
     22|- - People/Employees import
     23|+ - Staff/Employees import
     24|
     25|**Import Wizard Features:**
     26|- - Select entity type (Assets, People, Sites, etc.)
     27|+ - Select entity type (Assets, Staff, Sites, etc.)
     28|
     29|**Import Controllers for Each Entity:**
     30|- - `PersonImportController.php`
     31|+ - `StaffImportController.php`
## Freshservice‑style Upgrade — Delivery Status

Phases 1–10 completed:
- Data models & migrations for Vendors, Products, Contracts, Purchase Orders (+ items), Software
- CRUD UI + routes with policies and permission middleware
- Asset form enhancements (vendor/product selectors, warranty/cost hints, PO link)
- Filters & lists across Assets, Contracts, POs, Software
- Reports for Contracts/POs/Software (ReportBuilder + CSV)
- Tools Import endpoints for vendors/products/contracts/POs/software
- Dashboard cards (contracts expiring; POs due; software seats)
- Alerts & notifications (contracts expiring; POs due/overdue)
- Permissions & policies + Admin sidebar visibility
- Optional enhancements: Asset custom_fields (JSON), Contracts Board

Next steps:
- Harden validation + add parsing/upsert logic inside import jobs
- Download Center for batched exports (XLSX), and export endpoints parity for all lists
- CMDB relationships and ITSM modules per docs/ROADMAP_FRESHDESK_FEATURES.md
