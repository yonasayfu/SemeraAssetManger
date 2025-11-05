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
            // New modules
            'vendors.view', 'vendors.create', 'vendors.update', 'vendors.delete',
            'products.view', 'products.create', 'products.update', 'products.delete',
            'contracts.view', 'contracts.create', 'contracts.update', 'contracts.delete',
            'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.update', 'purchase-orders.delete',
            'software.view', 'software.create', 'software.update', 'software.delete',
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
                'vendors.view', 'vendors.create', 'vendors.update',
                'products.view', 'products.create', 'products.update',
                'contracts.view', 'contracts.create', 'contracts.update',
                'purchase-orders.view', 'purchase-orders.create', 'purchase-orders.update',
                'software.view', 'software.create', 'software.update',
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
                'vendors.view', 'products.view', 'contracts.view', 'purchase-orders.view', 'software.view',
            ],
            'Staff' => [
                'dashboard.view',
                'assets.view',
                'lists.view',
                'reports.view',
                'advanced.view',
                'staff.view',
                'vendors.view', 'products.view', 'contracts.view', 'purchase-orders.view', 'software.view',
            ],
            'Auditor' => [
                'dashboard.view',
                'assets.view',
                'lists.view', 'lists.export',
                'reports.view', 'reports.export',
                'audits.view',
                'advanced.view',
                'staff.view',
                'vendors.view', 'products.view', 'contracts.view', 'purchase-orders.view', 'software.view',
            ],
            'ReadOnly' => [
                'dashboard.view',
                'assets.view',
                'lists.view',
                'reports.view',
                'advanced.view',
                'staff.view',
                'vendors.view', 'products.view', 'contracts.view', 'purchase-orders.view', 'software.view',
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
