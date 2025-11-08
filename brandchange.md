ASLM Branding Guide
===================

Use this checklist to rebrand the app (name, logos, icons, colors, emails, PDFs/exports). File paths are clickable; each entry includes the key line to update.

App Name (global)
- config/app.php:16 — set 'name' to your org name.
- .env:1 — set APP_NAME=YourName (also drives email “from” name by default).
- resources/js/app.ts:11 — SPA title fallback (uses VITE_APP_NAME or this hard fallback).
- resources/js/ssr.ts:7 — SSR title fallback.
- resources/views/app.blade.php:33 — HTML <title> uses config('app.name').
- app/Http/Middleware/HandleInertiaRequests.php:48 — exposes name to the frontend via Inertia props (no change needed if APP_NAME is set).

Main Logo (UI header + PDFs/exports)
- public/images/asset-logo.svg — primary logo file used by PDFs/exports and as a default in some views. Replace with your SVG.
- resources/js/components/AppLogo.vue:12 — visible product text next to the icon (e.g., “ASLM”).
- resources/js/components/AppLogoIcon.vue:20 — the inline SVG icon; replace path(s) to change the symbol.

Clearances PDF and other PDFs
- resources/views/clearances/pdf.blade.php:22 — falls back to public/images/asset-logo.svg unless Company.logo is set.
  - To use an org‑specific PDF logo without code changes, set the Company’s logo from the UI (Setup → Companies) so it’s stored under public/... and will be picked up.
- resources/views/pdf-layout.blade.php:91 — shared PDF layout supports a logo path; name defaults to config('app.name') (lines 95, 197).
- app/Support/Exports/ExportConfig.php:11 — export header logo path (defaults to public/images/asset-logo.svg) and organization name (line 15).

Favicons and App Icons
- public/favicon.ico — update to your ICO.
- public/favicon.svg — update to your brand SVG.
- public/apple-touch-icon.png — 180×180 PNG for iOS home screen.
- resources/views/app.blade.php:37 — favicon registrations (ico, svg, apple-touch).

Emails (from name and domain)
- .env — set:
  - MAIL_FROM_NAME="${APP_NAME}" (default already uses APP_NAME)
  - MAIL_FROM_ADDRESS=noreply@your-domain
  - APP_URL=https://app.your-domain (impacts links and some mailers’ local domain)
- config/mail.php:113 — default “from” if env is missing.

Brand Colors / Theme
- resources/css/app.css:165 — brand palette overrides (commented section for primary color family). Adjust shades or add CSS vars as needed, then rebuild assets.

Welcome / Marketing Copy (optional)
- resources/js/pages/Welcome.vue:19 — update landing text or remove Laravel references.

Placeholders (optional, used by seeds/tools)
- storage/app/public/placeholders/placeholder.svg — generic placeholder image used by demo seeders and galleries. Replace if desired.

Environment and cache
- After changing APP_NAME or config:
  - php artisan config:clear
  - php artisan route:clear
  - php artisan view:clear
- Rebuild frontend when changing logos, colors, or text in Vue/CSS:
  - npm run build (or npm run dev during development)

Notes / Other mentions
- Session/Cache/Redis prefixes are derived from APP_NAME; setting APP_NAME is sufficient (see config/session.php:132, config/cache.php:106, config/database.php:150).
- Docs (README, hosting guides) reference Laravel; update if you plan to redistribute.

Quick Rebrand Steps
1) Replace logo files in public/images/asset-logo.svg, public/favicon.svg, public/favicon.ico, public/apple-touch-icon.png.
2) Set APP_NAME (and mail settings) in .env and config/app.php.
3) Update in‑app label text and icon: resources/js/components/AppLogo.vue and AppLogoIcon.vue.
4) If using a custom PDF logo, set Company → Logo in the UI or update resources/views/clearances/pdf.blade.php fallback.
5) Optionally tweak colors in resources/css/app.css.
6) Clear caches and rebuild frontend.

