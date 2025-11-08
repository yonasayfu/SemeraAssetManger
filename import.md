Asset Import Guide (ASLM)
=========================

Overview
- Use this guide to import a CSV/XLSX export (e.g., from Asset Tiger) into the ASLM Assets module.
- The importer supports mapping, normalization, a Dry Run step, and optional auto‑creation of taxonomy (Site/Location/Category/Department).

Where to import
- Go to Assets → Import or visit `/assets/import`.
- You’ll also see an “Import CSV/XLSX” button on the Assets list toolbar.

Step‑by‑step
1) Upload file
   - Click “Preview” to detect your column headers.
   - Detected headers appear under “Detected columns”.

2) Map columns
   - In the mapping grid, choose the target field for each source header.
   - Check “Create missing Site/Location/Category/Department” if you want new records created when a name is not found.

3) Dry Run
   - Click “Run Dry Run” to validate records without writing to the database.
   - The report shows:
     - Rows: total lines scanned
     - Ready: rows with no blocking errors
     - Errors: items that must be fixed before import
     - Warnings: non‑blocking issues (e.g., unknown assignee)

4) Import Now
   - If you’re satisfied with the Dry Run results, click “Import Now”.

Recommended mapping (Asset Tiger → ASLM)
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

Notes and normalization
- Dates: Accepts Excel date serials and strings (e.g., `11/18/2021`, `2021-11-18`).
- Cost: Handles thousands separators and currency symbols (e.g., `27,269.91`, `$800.00`).
- Status: Case‑insensitive mapping to ASLM enums:
  - Available, Checked Out, Under Repair, Leased, Disposed, Lost, Donated, Sold
- Taxonomy auto‑create: If enabled, missing Site/Location/Category/Department are created by name.
- Assigned to: Resolves a staff member by email first, then by full name; users are not auto‑created.
- Asset Photo: If provided, the value is stored as a path. To display images, files must exist under `/storage` (e.g., upload in the Photos tab or place files under `storage/app/public`).

Common questions
- What is Dry Run?
  - A validation pass — no data is written. Use it to find problems safely before performing a real import.

- I saw “GET /assets/import/preview not allowed (405)”. Why?
  - The preview endpoint only accepts `POST`. This error appears if you open the URL directly in the browser or trigger a `GET` request. Use the “Preview” button in the UI — it submits a `POST`.
  - If you still see a GET request, rebuild your frontend and refresh:
    - `npm run dev` (during development) or `npm run build` (for production)
    - Clear browser cache and reload `/assets/import`

Tips for larger files (e.g., 600 rows)
- Prefer CSV for speed; XLSX is also supported.
- Ensure `upload_max_filesize` and `post_max_size` in PHP are large enough.
- Start with a Dry Run to surface issues early, then run the import.

