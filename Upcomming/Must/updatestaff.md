## Plan: Consolidate assignments onto `staff`

Use the `Staff` model and `staff` table for all asset assignments. Replace legacy `assigned_to` and any `Person` references.

### Phase 1: Database Schema Modification

1.  **Modify the `assets` table (if needed):**
    *   Ensure there is a nullable `staff_id` column with FK to `staff.id`.
    *   If an old `assigned_to` column exists, backfill `staff_id` from it and drop `assigned_to`.

### Phase 2: Backend Code Updates

1.  Keep `app/Models/Staff.php` (primary identity model).
2.  Remove legacy `Person` references (controllers, views, validation).
3.  **`app/Models/Asset.php`:**
    *   `fillable` includes `staff_id` (not `assigned_to`).
    *   `assignee()` is `belongsTo(Staff::class, 'staff_id')`.
4.  **`app/Http/Controllers/AssetController.php`:**
    *   Validation uses `staff_id: nullable|exists:staff,id`.
    *   Provide `staff` options via `Staff::select('id','name')->orderBy('name')`.
5.  **Checkout & Lease controllers:**
    *   Checkout validates `assignee_id: exists:staff,id`, sets `assignee_type: 'staff'`, and updates `asset.staff_id`.
    *   Lease sets `asset.staff_id` only when `lessee_type === 'person'`, otherwise `null`.
6.  **Return/Dispose/Check-in:** set `asset.staff_id = null`.
7.  **Import/Export:**
    *   Import accepts `staff_id` (fallback to `assigned_to` if present).
    *   Export maps the `Staff ID` column from `asset.staff_id`.

### Phase 3: Frontend Code Updates

1.  **Assets Create/Edit pages:** use `staff_id` form field; select iterates `staff` list.
2.  **Checkout page:** uses `assignee_type: 'staff'` and `assignee_id` from `staff` list.
3.  **Lease page:** when `lessee_type === 'person'`, select from `staff` list.
4.  **Types:** `Asset.staff_id: number | null` (remove `assigned_to`).

### Phase 4: Documentation Updates

1.  **`MyNewDatabaseSchema.md`:** show `staff_id` (FK: `staff.id`); `created_by` references `staff.id`.
2.  **`MyNewProjectRoadmap.md`:** reflect checkout `assignee_type: staff|department|customer`; maintenance references staff consistently.
3.  **`TASK_CHECKLIST.md`:** ensure any legacy references to `assigned_to`/`Person` are removed.

### Phase 5: Testing and Cleanup

1.  Run `php artisan migrate`.
2.  Seed and test: create asset (with/without staff), checkout/checkin, lease/return.
3.  Confirm no "Class not found" or "Undefined column" errors.
4.  Remove stray debug logs from components if any.
