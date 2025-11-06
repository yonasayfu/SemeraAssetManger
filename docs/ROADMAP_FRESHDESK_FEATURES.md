# ITSM Enhancements (Freshdesk/Freshworks Inspired)

Goal: Differentiate from AssetTiger by adding IT/Service Management capabilities that ASLM needs.

Note: Core Catalog & Procurement (Vendors, Products, Contracts, Purchase Orders, Software) has been delivered in the main app; this roadmap focuses on next‑phase ITSM features (tickets, CMDB, discovery).

## Phase A — Ticketing & Service Desk
- Tickets: incidents/requests with priorities, SLA timers, statuses, assignee, watchers.
- Categories: issue types, request catalog with dynamic forms.
- Automation: assignment rules, escalations, email parsing, canned responses.
- Knowledge Base (basic): articles linked to categories; suggest on ticket creation.

## Phase B — CMDB & Relationships
- CMDB: track configuration items (devices, servers, apps) in addition to assets.
- Relationships: Asset ↔ Asset (depends on, connected to), Asset ↔ CI, Site ↔ Service.
- Impact analysis: show related items and service impact.

## Phase C — Software & Licensing
- Software inventory: product, version, license key, seats.
- License tracking: assignment to staff/asset, expiry alerts, compliance report.

## Phase D — Discovery & Integrations
- Discovery: lightweight agent or import to reconcile devices (CSV/JSON/API).
- Integrations: Email channel for tickets; SSO (OIDC) if needed; webhooks for automation.

## Cross‑cutting
- Roles/permissions for new modules.
- Reports: SLA compliance, ticket volumes, MTTR, license utilisation.
- UI/UX: dashboard widgets for tickets, SLAs, license renewal.

## Suggested Timeline
- A: 3–4 weeks
- B: 3 weeks
- C: 2 weeks
- D: 3–4 weeks
