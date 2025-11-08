**Import Issue Summary**
- Symptom: Dry Run reports all rows ready (e.g., Rows: 626 | Ready: 626), but running “Import Now” results in no new records in the `assets` table. Previously, a green success toast appeared but the list did not update. After fixes, server now shows accurate success/failure toasts, but imports still need verification.

**Context**
- Stack: Laravel 12, Inertia, Vue 3, Maatwebsite/Excel.
- Flow: Upload → Preview → Map columns → Dry Run → Import Now.
- Mapping used (Auto‑map from AssetTiger‑style headers):
  - Asset Photo → `asset_photo`
  - Asset Tag ID → `asset_tag`
  - Description → `description`
  - Purchase Date → `purchase_date`
  - Cost → `cost`
  - Status → `status`
  - Purchased from → `purchased_from`
  - Serial No → `serial_no`
  - Site → `site`
  - Location → `location`
  - Category → `category`
  - Department → `department`
  - Assigned to → `assigned_to`
  - Project code → `project_code`

**What Was Wrong**
- Missing toast wiring: Flash messages from the server were not shared to Inertia, so success/failure toasts did not render, giving a false sense of success.
  - Fix: Share flash in middleware.
  - app/Http/Middleware/HandleInertiaRequests.php:83
- Sites auto‑creation failing DB constraints: `sites` table requires non‑nullable fields (`address`, `city`, `state`, `postal_code`, `country`). Importer originally created Sites with only `name`, causing inserts to fail and abort the whole import.
  - Fix: Provide placeholder values when auto‑creating Sites.
  - app/Imports/AssetsImport.php:126
- UI mapped count confusion: Count included `currency` even when the file had no such column.
  - Fix: Denominator now reflects only targets relevant to the file (required + suggested + mapped), so it shows 14/14.
  - resources/js/pages/Assets/Import.vue:25,114,139
- Import feedback: No count or error details were shown.
  - Fix: Wrap import in try/catch and report either the exception or “Imported N record(s)”.
  - app/Http/Controllers/StoreAssetImportController.php:47

**Remaining Risks / Possible Causes If It Still Fails**
- Duplicate `asset_tag` values (unique index). Dry Run does not yet detect duplicates; import would fail on first duplicate.
- `locations.site_id` is NOT nullable (database/migrations/2025_10_27_115414_create_locations_table.php:14). If a row has a `location` name but `site` is blank, creating a Location will fail (site_id NULL). UI offers “Swap Site/Location” and Dry Run currently does not error when `location` present but `site` missing.
- Expired preview token (e.g., navigating away then importing later) results in “No import file provided.”
- Permissions: Backend authorizes `create` on Asset. If user lacks permission, import is blocked (403).

**Files Involved**
- UI
  - resources/js/pages/Assets/Import.vue:21
  - resources/js/pages/Assets/Import.vue:25
  - resources/js/pages/Assets/Import.vue:114
  - resources/js/pages/Assets/Import.vue:139
- Controllers / Routes
  - routes/web.php:265
  - app/Http/Controllers/AssetImportPreviewController.php:12
  - app/Http/Controllers/AssetImportDryRunController.php:16
  - app/Http/Controllers/StoreAssetImportController.php:47
  - app/Http/Middleware/HandleInertiaRequests.php:83
- Importer
  - app/Imports/AssetsImport.php:18
  - app/Imports/AssetsImport.php:45
  - app/Imports/AssetsImport.php:120
  - app/Imports/AssetsImport.php:126
  - app/Imports/AssetsImport.php:137
- Database
  - database/migrations/2025_10_27_115404_create_sites_table.php:14
  - database/migrations/2025_10_27_115414_create_locations_table.php:14
  - database/migrations/2025_10_27_121813_create_assets_table.php:16

**How To Reproduce**
- Upload the CSV/XLSX with headers listed above.
- Auto‑map; ensure “Create missing Site/Location/Category/Department” is enabled.
- Run Dry Run: expect “Rows: 626 | Ready: 626”.
- Click “Import Now”.

**What To Expect Now (Post‑Fix)**
- On success: Redirect to `/assets` and toast “Import completed. Imported N record(s).” The assets list reflects the new rows.
- On failure: Toast on the Import page with “Import failed: {error}” or “No import file provided.”

**Quick Diagnostics**
- Check DB connection in `.env` to ensure you’re looking at the same database as the app.
- Review logs: `storage/logs/laravel.log` immediately after importing for any stack trace.
- Count assets before/after:
  - `php artisan tinker` → `\App\Models\Asset::count()`
- Verify permissions: user must be authorized to create `Asset`.

**Suggested Improvements (for Gemini to implement)**
- Dry Run hardening:
  - Detect duplicate `asset_tag` values and surface as errors.
  - Error when `location` has a value but `site` is missing (because `locations.site_id` is not nullable) to prevent runtime DB errors.
  - Add a caption “Showing first 50 rows” to the Dry Run details.
- Import resilience:
  - Implement `SkipsFailures` (Maatwebsite/Excel) to skip failing rows and produce a failure report (row number + error), while importing the rest.
  - Optionally add an “Upsert by asset_tag” mode to update existing assets instead of failing on duplicates.
- UX polish:
  - In the mapping UI, only include `availableTargets` (already partially done) and hide targets not applicable to the provided file.
  - Consider pre‑swap detection: if `site` looks like a country and `location` like a room, prompt “Swap Site/Location?”.
- Observability:
  - Add server metrics/log entries for number of rows processed, created, updated, skipped.

**Open Questions**
- Should the importer auto‑create Staff if `assigned_to` doesn’t resolve? (Currently: no; it warns in Dry Run and sets `staff_id` null.)
- Do we want to require `site` whenever `location` is present? (Likely yes.)

**Next Steps**
- Re‑run the import end‑to‑end now that Site auto‑creation includes required fields.
- If it still fails, capture the exact toast error and the latest lines from `storage/logs/laravel.log` to pinpoint whether it’s a duplicate `asset_tag`, missing `site` when `location` is present, or a different constraint.

