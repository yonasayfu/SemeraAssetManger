# Audit Module Flow

This guide explains how to run a physical audit of assets: create an audit, scan items (mark Found/Missing with notes), and review/export a variance report.

## Overview
- Goal: Verify presence and location of assets for a site (optionally a specific location).
- Statuses: Draft (no assets yet), Ongoing (active scanning), Completed (finalized, read‑only).
- Key pages: Tools → Audits list, Audit Wizard (create), Scan, Report.

## Data Model
- `Audit`: name, site_id, optional location_id, status, timestamps (started_at, completed_at).
- `AuditAsset`: pivot list for an audit; one row per included asset with fields `found` (true/false/null) and `notes`.

## Core Flow
1) Create Audit (Wizard)
   - Go to `/tools/audits` → “Create Audit” (→ `/audits/wizard`).
   - Fill: Name, Site, optional Location; pick categories and/or specific assets.
   - Submits to `AuditWizardController@store` → `AuditService::start(...)` creates the audit and preloads `audit_assets`.
   - If no assets are found for the selection, the audit is created as Draft.

2) Scan (Mark Found/Missing)
   - Open `/audits/{audit}/scan` (from Tools → Audits list or Wizard confirmation).
   - Use search (by asset tag/description) to filter the audit list.
   - For each row: click Found (✓) or Missing (✕) and optionally add a note; notes save on blur.
   - End scanning by clicking “Complete Audit”. This sets status to Completed and redirects to the report.

3) Report (Review & Export)
   - Open `/audits/{audit}/report`.
   - Summary: totals for Found/Missing.
   - Tables: lists of Found and Missing items (with category/site/location and notes).
   - Print: browser print (styled for A4 landscape).
   - Export CSV: `/audits/{audit}/report/export` for offline analysis.

## Pages ↔ Routes ↔ Controllers
- Tools list: `/tools/audits` → `AuditController@index` (filters: search, status). Quick links to Scan/Report.
- Wizard: `/audits/wizard` (GET/POST) → `AuditWizardController@create/store`.
- Scan: `/audits/{audit}/scan` → `AuditScanController@show` (preloads `auditAssets.asset`).
  - Search (JSON): `GET /audits/{audit}/scan/search`.
  - Update row: `POST /audits/{audit}/scan/assets/{auditAsset}` (found/notes).
  - Complete: `POST /audits/{audit}/scan/complete`.
- Report: `/audits/{audit}/report` → `Report\AuditReportListController@show`.
- Export: `/audits/{audit}/report/export` → CSV stream.

## Roles & Permissions (typical)
- View audits: staff with assets/tools visibility.
- Create/Scan/Complete: users with audit update permissions (e.g., Managers/Auditors).

## Scenarios
- Scenario A — Warehouse Cycle Count (Ongoing)
  - Choose Site: “Headquarters”, Location: “Warehouse A”.
  - Create the audit and start scanning. Mark pallets Found; mark a missing laptop with note “Sent for repair”.
  - Complete Audit → Report shows Found vs Missing breakdown. Export CSV for records.

- Scenario B — Annual Site Verification (Completed)
  - Choose Site only (no location). Include all categories.
  - Scan team sweeps all areas; all assets Found. Complete → Report totals match 100% found.

- Scenario C — Spot Check (Category or Hand‑picked Assets)
  - Create an audit for Site: “Plant”, Category: “Machinery”.
  - Add specific high‑value assets manually if needed.
  - During Scan, search by asset tag (e.g., CNC) and mark status with notes.

## Tips & Behavior
- Draft audits: created when no assets match selection (add items later or delete).
- Ongoing audits: can continue scanning over multiple sessions.
- Completed audits: read‑only; use Report/Export for evidence.
- Extras: “extra” assets (found but not in the list) aren’t tracked yet in this build.

## Seed Data (for demo)
- Seeder creates two sample audits:
  - “Q4 Warehouse Cycle Count” (Ongoing): mix of found/missing with notes.
  - “Plant Annual Verification” (Completed): all found.
- Run: `php artisan db:seed` (includes `AuditSampleSeeder`).
- Then visit `/tools/audits` to explore, or jump to Scan/Report.

## Troubleshooting
- No rows in Scan: ensure audit has assets (site/location/category selection) or seed sample data.
- Export blocked: ensure your user has Admin or export permission.
- Printing: use the toolbar Print on Report for optimized layout.

