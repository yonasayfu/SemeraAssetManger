# RBAC User Guide

This guide summarizes roles, permissions, and accessible features in the Asset Management System.

## Test Users (default password: password)

- Admin — admin@example.com (all features)
- Manager — manager@example.com
- Technician — technician@example.com
- Staff — staff@example.com
- Auditor — auditor@example.com
- ReadOnly — readonly@example.com

These accounts are created by DatabaseSeeder. If missing, run:

```
composer dump-autoload
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
```

## Roles and Permissions

Permissions are powered by Spatie Laravel-Permission. Admin is a superuser (Gate::before) and also receives all explicit permissions.

- dashboard.view — Access Dashboard
- alerts.view — Access Alerts module
- assets.view — View Assets (and operation select pages)
- assets.create — Create/Import Assets
- assets.update — Edit Assets
- assets.delete — Delete Assets
- maintenance.view — Maintenance module
- warranty.view — Warranty module
- lists.view — Lists module (Assets, Maintenances, Warranties, Audits)
- lists.export — Export/print on list pages
- reports.view — Reports module
- reports.export — Export/run reports
- tools.view — Tools (Import/Export, Galleries, Audits)
- tools.import — Submit imports on Tools > Import
- tools.export — Submit exports on Tools > Export
- advanced.view — Advanced (Persons, Customers)
- setup.manage — Setup (Companies, Sites, Locations, Categories, Departments, Manage Dashboard)
- activity-logs.view — Activity Logs
- users.manage — User Management
- roles.manage — Role Management
- mailbox.view, mailbox.process — Dev mailbox
- staff.view/create/update/delete — Staff module
- users.impersonate — Impersonation

## Default Role Access Matrix

- Admin
  - All permissions
- Manager
  - dashboard.view, alerts.view, assets.view/create/update, lists.view/export, reports.view/export, tools.view/export, advanced.view, maintenance.view, warranty.view, audits.view, staff.view/create/update
- Technician
  - dashboard.view, alerts.view, assets.view, lists.view/export, tools.view/import, advanced.view, maintenance.view, warranty.view, audits.view, staff.view
- Staff
  - dashboard.view, assets.view, lists.view, reports.view, advanced.view, staff.view
- Auditor
  - dashboard.view, assets.view, lists.view/export, reports.view/export, audits.view, advanced.view, staff.view
- ReadOnly
  - dashboard.view, assets.view, lists.view, reports.view, advanced.view, staff.view
- External
  - dashboard.view

## Menu Visibility

- The sidebar hides items the user lacks permission for. Parent items gate children.
- Examples:
  - Setup only appears with setup.manage.
  - Reports only appears with reports.view.
  - Activity Logs only appears with activity-logs.view.

## Troubleshooting

- After changing roles/permissions run:
```
php artisan optimize:clear
php artisan permission:cache-reset
```
- If Admin sees 403, ensure AuthServiceProvider is loaded and RolePermissionSeeder has been re-run.

## Notes

- Mailbox routes are enabled only in local environment.
- Lists, Reports, and Tools link to their respective controllers and honor guards.
