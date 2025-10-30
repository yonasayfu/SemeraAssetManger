<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect([
            // Core management
            'staff.view', 'staff.create', 'staff.update', 'staff.delete',
            'roles.manage',
            'activity-logs.view', 'mailbox.view', 'mailbox.process',

            // Modules - high level
            'dashboard.view',
            'alerts.view',
            'assets.view', 'assets.create', 'assets.update', 'assets.delete',
            'lists.view',
            'lists.export',
            'reports.view',
            'reports.export',
            'tools.view',
            'tools.import',
            'tools.export',
            'advanced.view',
            'setup.manage',
            'maintenance.view',
            'warranty.view',
            'audits.view',
        ])->map(function (string $name) {
            return Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
            );
        });

        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web'],
        );
        $adminRole->syncPermissions($permissions->pluck('name')->all());

        $roles = [
            'Manager' => [
                'dashboard.view',
                'alerts.view',
                'assets.view', 'assets.create', 'assets.update',
                'lists.view', 'lists.export',
                'reports.view', 'reports.export',
                'tools.view', 'tools.export',
                'advanced.view',
                'maintenance.view',
                'warranty.view',
                'audits.view',
                'staff.view', 'staff.create', 'staff.update',
            ],
            'Technician' => [
                'dashboard.view',
                'alerts.view',
                'assets.view',
                'lists.view', 'lists.export',
                'tools.view', 'tools.import',
                'advanced.view',
                'maintenance.view',
                'warranty.view',
                'audits.view',
                'staff.view',
            ],
            'Staff' => [
                'dashboard.view',
                'assets.view',
                'lists.view',
                'reports.view',
                'advanced.view',
                'staff.view',
            ],
            'Auditor' => [
                'dashboard.view',
                'assets.view',
                'lists.view', 'lists.export',
                'reports.view', 'reports.export',
                'audits.view',
                'advanced.view',
                'staff.view',
            ],
            'ReadOnly' => [
                'dashboard.view',
                'assets.view',
                'lists.view',
                'reports.view',
                'advanced.view',
                'staff.view',
            ],
            'External' => [
                'dashboard.view',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
            );

            $role->syncPermissions($rolePermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
