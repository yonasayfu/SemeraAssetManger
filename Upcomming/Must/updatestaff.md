## Plan: Consolidate `Staff` into `Staff`

The goal is to remove the `Staff` model and `staff` table, and instead use the `Staff` model and `staff` table for all asset assignments.

### Phase 1: Database Schema Modification

1.  **Create a new migration to modify the `assets` table:**
    *   Drop the existing `assigned_to` foreign key constraint referencing `staff.id`.
    *   Rename the `assigned_to` column to `staff_id`.
    *   Add a new foreign key constraint on `staff_id` referencing `staff.id`.
    *   (Optional but recommended) Add a nullable constraint to `staff_id` if assets can be unassigned. (The current `assigned_to` is nullable, so `staff_id` should also be nullable).

2.  **Create a new migration to drop the `staff` table:**
    *   This migration will simply drop the `staff` table. This should be done *after* the `assets` table no longer references it.

### Phase 2: Backend Code Updates

1.  **Delete `app/Models/Staff.php`:** Remove the `Staff` model file.
2.  **Delete `database/seeders/StaffSeeder.php`:** Remove the seeder file.
3.  **Update `app/Http/Controllers/AssetController.php`:**
    *   Remove `use App\Models\Staff;`.
    *   Change `Staff::select('id', 'name')->orderBy('name')->get()` to `Staff::select('id', 'first_name', 'last_name')->orderBy('first_name')->get()` (or similar, depending on how `Staff` names are handled).
    *   Adjust the `assigned_to` validation rule from `nullable|exists:staff,id` to `nullable|exists:staff,id`.
    *   Update any other references to `Staff` to `Staff`.
4.  **Update `app/Http/Controllers/AssetCheckoutController.php`:**
    *   Remove `use App\Models\Staff;`.
    *   Change `Staff::all()` to `Staff::all()` for the `staff` prop.
5.  **Review other controllers/services:** Search the entire `app/` directory for any other references to `App\Models\Staff` and update them to `App\Models\Staff` or remove them if no longer needed.
6.  **Update `app/Models/Asset.php`:**
    *   Change the `assignee` relationship from `belongsTo(Staff::class)` to `belongsTo(Staff::class, 'staff_id')`.
    *   Update any other references to `Staff`.

### Phase 3: Frontend Code Updates

1.  **Update `resources/js/Pages/Assets/Create.vue`:**
    *   Change the `staff` prop type from `StaffOption[]` to `StaffOption[]` (or similar, reflecting `Staff` structure).
    *   Update the `assigned_to` select input to iterate over `staff` instead of `staff`.
    *   Adjust `staff.name` to `staff.full_name` or `staff.first_name + ' ' + staff.last_name` as appropriate.
2.  **Update `resources/js/Pages/Assets/Checkout.vue`:**
    *   Change the `staff` prop type from `StaffOption[]` to `StaffOption[]`.
    *   Update the `assignee_id` select input to iterate over `staff` instead of `staff`.
    *   Adjust `staff.name` to `staff.full_name` or `staff.first_name + ' ' + staff.last_name` as appropriate.
3.  **Review other Vue components:** Search the `resources/js/` directory for any other references to `Staff` or `staff` prop and update them to `Staff` or `staff`.

### Phase 4: Roadmap and Schema Documentation Updates

1.  **Update `MyNewDatabaseSchema.md`:**
    *   Remove the `staff` table definition.
    *   Update the `assets` table definition to show `staff_id` (FK: `staff.id`) instead of `assigned_to` (FK: `staff.id`).
    *   Update the "RELATIONSHIPS SUMMARY" to reflect `Staff → Assets` (1:N) instead of `Department → Staff` and `Asset → Staff`.
2.  **Update `MyNewProjectRoadmap.md`:**
    *   Under "Identity & Org" in the database schema section, remove `staff`.
    *   Under "Advanced" features, update "Staff/Employees" to "Staff/Employees" or just "Staff".
    *   Review and update any other mentions of `Staff` or `staff`.
3.  **Update `TASK_CHECKLIST.md`:**
    *   Remove `StaffPolicy.php`.
    *   Remove `StaffImportController.php` and `ImportStaffJob.php`.
    *   Update any other mentions of `Staff`.

### Phase 5: Testing and Cleanup

1.  Run `php artisan migrate` (after creating the new migrations).
2.  Run `php artisan db:seed` (if there are other seeders).
3.  Manually test asset creation, checkout, and any other functionality that previously used `Staff`.
4.  Ensure no "Class not found" or "Undefined column" errors appear.
5.  Remove the `console.log` from `FileUploadField.vue` (if it's still there).