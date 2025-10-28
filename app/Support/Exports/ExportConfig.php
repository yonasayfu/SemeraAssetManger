<?php

namespace App\Support\Exports;

use Illuminate\Support\Str;

class ExportConfig
{
    public static function brandHeader(string $documentTitle): array
    {
        $logoPath = public_path('images/asset-logo.svg');

        return [
            'logo' => file_exists($logoPath) ? $logoPath : null,
            'organization_name' => config('app.name', 'Asset Management'),
            'document_title' => $documentTitle,
        ];
    }

    public static function staff(): array
    {
        return [
            'label' => 'Staff Directory',
            'type' => 'staff',
            'filename_prefix' => 'staff-directory',

            'csv' => [
                'headers' => [
                    '#',
                    'Full Name',
                    'Email',
                    'Phone',
                    'Job Title',
                    'Status',
                    'Linked User',
                ],
                'fields' => [
                    'index',
                    'full_name',
                    'email',
                    'phone',
                    'job_title',
                    [
                        'field' => 'status',
                        'transform' => fn ($value) => Str::of($value ?? '')->replace('_', ' ')->title(),
                        'default' => 'Inactive',
                    ],
                    [
                        'field' => 'user.name',
                        'default' => 'â€”',
                    ],
                ],
                'with_relations' => ['user:id,name'],
                'filename_prefix' => 'staff-directory',
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Staff Directory - Full Export',
                'filename_prefix' => 'staff-directory',
                'orientation' => 'landscape',
                'with_relations' => ['user:id,name'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'job_title', 'label' => 'Job Title'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'user.name', 'label' => 'Linked User'],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Staff Directory - Current View',
                'filename_prefix' => 'staff-current-view',
                'orientation' => 'landscape',
                'with_relations' => ['user:id,name'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'job_title', 'label' => 'Job Title'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'user.name', 'label' => 'Linked User'],
                ],
            ],

            'single_record' => [
                'view' => 'pdf-layout',
                'document_title' => 'Staff Profile Summary',
                'filename_prefix' => 'staff-profile',
                'with_relations' => ['user:id,name,email'],
                'columns' => [
                    ['key' => 'full_name', 'label' => 'Full Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'phone', 'label' => 'Phone'],
                    ['key' => 'job_title', 'label' => 'Job Title'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'hire_date', 'label' => 'Hire Date'],
                    ['key' => 'user.name', 'label' => 'Linked User'],
                ],
            ],
        ];
    }

    public static function users(): array
    {
        return [
            'label' => 'User Roster',
            'type' => 'users',
            'filename_prefix' => 'users',

            'csv' => [
                'headers' => [
                    '#',
                    'Name',
                    'Email',
                    'Roles',
                    'Direct Permissions',
                    'Has 2FA',
                    'Linked Staff',
                    'Staff Status',
                ],
                'fields' => [
                    'index',
                    'name',
                    'email',
                    [
                        'field' => 'roles',
                        'transform' => fn ($value) => $value instanceof \Illuminate\Support\Collection
                            ? $value->implode(', ')
                            : (is_array($value) ? implode(', ', $value) : $value),
                        'default' => 'â€”',
                    ],
                    [
                        'field' => 'permissions',
                        'transform' => fn ($value) => $value instanceof \Illuminate\Support\Collection
                            ? $value->implode(', ')
                            : (is_array($value) ? implode(', ', $value) : $value),
                        'default' => 'Inherited',
                    ],
                    [
                        'field' => 'has_two_factor',
                        'transform' => fn ($value) => $value ? 'Yes' : 'No',
                    ],
                    ['field' => 'staff.full_name', 'default' => 'â€”'],
                    [
                        'field' => 'staff.status',
                        'transform' => fn ($value) => $value ? Str::of($value)->headline() : 'â€”',
                        'default' => 'â€”',
                    ],
                ],
                'with_relations' => ['roles:id,name', 'permissions:id,name', 'staff:id,first_name,last_name,status,user_id'],
            ],

            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'User Accounts - Full Export',
                'filename_prefix' => 'users-roster',
                'orientation' => 'landscape',
                'with_relations' => ['roles:id,name', 'permissions:id,name', 'staff:id,first_name,last_name,status,user_id'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    [
                        'key' => 'roles',
                        'label' => 'Roles',
                        'transform' => fn ($model) => $model->roles?->pluck('name')->join(', ') ?: 'â€”',
                    ],
                    [
                        'key' => 'permissions',
                        'label' => 'Direct Permissions',
                        'transform' => fn ($model) => $model->permissions?->pluck('name')->join(', ') ?: 'Inherited',
                    ],
                    [
                        'key' => 'has_two_factor',
                        'label' => '2FA',
                        'transform' => fn ($model) => $model->two_factor_secret ? 'Yes' : 'No',
                    ],
                    [
                        'key' => 'staff.full_name',
                        'label' => 'Staff',
                        'transform' => fn ($model) => $model->staff?->full_name ?? 'â€”',
                    ],
                    [
                        'key' => 'staff.status',
                        'label' => 'Staff Status',
                        'transform' => fn ($model) => $model->staff?->status ? Str::of($model->staff->status)->headline() : 'â€”',
                    ],
                ],
            ],

            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'User Accounts - Current View',
                'filename_prefix' => 'users-current-view',
                'orientation' => 'landscape',
                'with_relations' => ['roles:id,name', 'permissions:id,name', 'staff:id,first_name,last_name,status,user_id'],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    [
                        'key' => 'roles',
                        'label' => 'Roles',
                        'transform' => fn ($model) => $model->roles?->pluck('name')->join(', ') ?: 'â€”',
                    ],
                    [
                        'key' => 'has_two_factor',
                        'label' => '2FA',
                        'transform' => fn ($model) => $model->two_factor_secret ? 'Yes' : 'No',
                    ],
                    [
                        'key' => 'staff.full_name',
                        'label' => 'Staff',
                        'transform' => fn ($model) => $model->staff?->full_name ?? 'â€”',
                    ],
                ],
            ],
        ];
    }

    public static function assets(): array
    {
        return [
            'label' => 'Asset Inventory',
            'type' => 'assets',
            'filename_prefix' => 'asset-inventory',
            'csv' => [
                'headers' => [
                    '#',
                    'Asset Tag',
                    'Description',
                    'Status',
                    'Condition',
                    'Category',
                    'Department',
                    'Site',
                    'Location',
                    'Assigned To',
                    'Purchase Date',
                    'Cost',
                    'Currency',
                    'Warranty Provider',
                    'Warranty Expires',
                    'Next Maintenance',
                ],
                'fields' => [
                    'index',
                    'asset_tag',
                    'description',
                    'status',
                    'asset_condition',
                    ['field' => 'category.name', 'default' => 'â€”'],
                    ['field' => 'department.name', 'default' => 'â€”'],
                    ['field' => 'site.name', 'default' => 'â€”'],
                    ['field' => 'location.name', 'default' => 'â€”'],
                    ['field' => 'assignee.name', 'default' => 'â€”'],
                    [
                        'field' => 'purchase_date',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d'),
                    ],
                    [
                        'field' => 'cost',
                        'transform' => fn ($value) => $value !== null ? number_format((float) $value, 2) : null,
                    ],
                    'currency',
                    ['field' => 'activeWarranty.provider', 'default' => 'â€”'],
                    [
                        'field' => 'activeWarranty.expiry_date',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d'),
                        'default' => 'â€”',
                    ],
                    [
                        'field' => 'upcomingMaintenance.scheduled_for',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d'),
                        'default' => 'â€”',
                    ],
                ],
                'with_relations' => [
                    'site:id,name',
                    'location:id,name',
                    'category:id,name',
                    'department:id,name',
                    'assignee:id,name',
                    'activeWarranty:id,asset_id,provider,expiry_date',
                    'upcomingMaintenance:id,asset_id,scheduled_for',
                ],
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Asset Inventory - Full Export',
                'filename_prefix' => 'asset-inventory',
                'orientation' => 'landscape',
                'with_relations' => [
                    'site:id,name',
                    'location:id,name',
                    'category:id,name',
                    'department:id,name',
                    'assignee:id,name',
                    'activeWarranty:id,asset_id,provider,expiry_date',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'asset_tag', 'label' => 'Asset Tag'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'asset_condition', 'label' => 'Condition'],
                    ['key' => 'category.name', 'label' => 'Category'],
                    ['key' => 'department.name', 'label' => 'Department'],
                    ['key' => 'site.name', 'label' => 'Site'],
                    ['key' => 'location.name', 'label' => 'Location'],
                    ['key' => 'assignee.name', 'label' => 'Assigned To'],
                    [
                        'key' => 'purchase_date',
                        'label' => 'Purchase Date',
                        'transform' => fn ($model) => optional($model->purchase_date)->format('Y-m-d'),
                    ],
                    [
                        'key' => 'cost',
                        'label' => 'Cost',
                        'transform' => fn ($model) => $model->cost !== null ? number_format((float) $model->cost, 2) : 'â€”',
                    ],
                    ['key' => 'currency', 'label' => 'Currency'],
                    ['key' => 'activeWarranty.provider', 'label' => 'Warranty Provider'],
                    [
                        'key' => 'activeWarranty.expiry_date',
                        'label' => 'Warranty Expires',
                        'transform' => fn ($model) => optional($model->activeWarranty?->expiry_date)->format('Y-m-d') ?? 'â€”',
                    ],
                ],
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Asset Inventory - Current View',
                'filename_prefix' => 'asset-inventory-current',
                'orientation' => 'landscape',
                'with_relations' => [
                    'site:id,name',
                    'location:id,name',
                    'category:id,name',
                    'department:id,name',
                    'assignee:id,name',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'asset_tag', 'label' => 'Asset Tag'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'category.name', 'label' => 'Category'],
                    ['key' => 'site.name', 'label' => 'Site'],
                    ['key' => 'location.name', 'label' => 'Location'],
                    ['key' => 'assignee.name', 'label' => 'Assigned To'],
                ],
            ],
        ];
    }

    public static function audits(): array
    {
        return [
            'label' => 'Audit Tracker',
            'type' => 'audits',
            'filename_prefix' => 'audit-tracker',
            'csv' => [
                'headers' => [
                    '#',
                    'Audit Name',
                    'Status',
                    'Site',
                    'Location',
                    'Started At',
                    'Completed At',
                ],
                'fields' => [
                    'index',
                    'name',
                    'status',
                    ['field' => 'site.name', 'default' => 'â€”'],
                    ['field' => 'location.name', 'default' => 'â€”'],
                    [
                        'field' => 'started_at',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d H:i'),
                    ],
                    [
                        'field' => 'completed_at',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d H:i'),
                        'default' => 'â€”',
                    ],
                ],
                'with_relations' => [
                    'site:id,name',
                    'location:id,name',
                ],
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Audit Tracker - Full Export',
                'filename_prefix' => 'audit-tracker',
                'orientation' => 'landscape',
                'with_relations' => [
                    'site:id,name',
                    'location:id,name',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Audit Name'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'site.name', 'label' => 'Site'],
                    ['key' => 'location.name', 'label' => 'Location'],
                    [
                        'key' => 'started_at',
                        'label' => 'Started At',
                        'transform' => fn ($model) => optional($model->started_at)->format('Y-m-d H:i') ?? 'â€”',
                    ],
                    [
                        'key' => 'completed_at',
                        'label' => 'Completed At',
                        'transform' => fn ($model) => optional($model->completed_at)->format('Y-m-d H:i') ?? 'â€”',
                    ],
                ],
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Audit Tracker - Current View',
                'filename_prefix' => 'audit-tracker-current',
                'orientation' => 'landscape',
                'with_relations' => [
                    'site:id,name',
                    'location:id,name',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'name', 'label' => 'Audit Name'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'site.name', 'label' => 'Site'],
                    ['key' => 'location.name', 'label' => 'Location'],
                ],
            ],
        ];
    }\n
    public static function maintenances(): array
    {
        return [
            'label' => 'Maintenance Tickets',
            'type' => 'maintenances',
            'filename_prefix' => 'maintenance-tickets',
            'csv' => [
                'headers' => [
                    '#',
                    'Title',
                    'Type',
                    'Status',
                    'Scheduled For',
                    'Completed At',
                    'Cost',
                    'Asset Tag',
                    'Asset Description',
                    'Site',
                    'Location',
                ],
                'fields' => [
                    'index',
                    'title',
                    'maintenance_type',
                    'status',
                    [
                        'field' => 'scheduled_for',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d'),
                    ],
                    [
                        'field' => 'completed_at',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d'),
                        'default' => 'N/A',
                    ],
                    [
                        'field' => 'cost',
                        'transform' => fn ($value) => $value !== null ? number_format((float) $value, 2) : null,
                        'default' => 'N/A',
                    ],
                    ['field' => 'asset.asset_tag', 'default' => 'N/A'],
                    ['field' => 'asset.description', 'default' => 'N/A'],
                    ['field' => 'asset.site.name', 'default' => 'N/A'],
                    ['field' => 'asset.location.name', 'default' => 'N/A'],
                ],
                'with_relations' => [
                    'asset:id,asset_tag,description,site_id,location_id',
                    'asset.site:id,name',
                    'asset.location:id,name',
                ],
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Maintenance Tickets - Full Export',
                'filename_prefix' => 'maintenance-tickets',
                'orientation' => 'landscape',
                'with_relations' => [
                    'asset:id,asset_tag,description,site_id,location_id',
                    'asset.site:id,name',
                    'asset.location:id,name',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'maintenance_type', 'label' => 'Type'],
                    ['key' => 'status', 'label' => 'Status'],
                    [
                        'key' => 'scheduled_for',
                        'label' => 'Scheduled For',
                        'transform' => fn ($model) => optional($model->scheduled_for)->format('Y-m-d') ?? 'N/A',
                    ],
                    [
                        'key' => 'completed_at',
                        'label' => 'Completed At',
                        'transform' => fn ($model) => optional($model->completed_at)->format('Y-m-d') ?? 'N/A',
                    ],
                    [
                        'key' => 'cost',
                        'label' => 'Cost',
                        'transform' => fn ($model) => $model->cost !== null ? number_format((float) $model->cost, 2) : 'N/A',
                    ],
                    ['key' => 'asset.asset_tag', 'label' => 'Asset Tag'],
                    ['key' => 'asset.site.name', 'label' => 'Site'],
                ],
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Maintenance Tickets - Current View',
                'filename_prefix' => 'maintenance-tickets-current',
                'orientation' => 'landscape',
                'with_relations' => [
                    'asset:id,asset_tag,description,site_id,location_id',
                    'asset.site:id,name',
                    'asset.location:id,name',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'asset.asset_tag', 'label' => 'Asset Tag'],
                    ['key' => 'scheduled_for', 'label' => 'Scheduled'],
                ],
            ],
        ];
    }

    public static function warranties(): array
    {
        return [
            'label' => 'Warranties',
            'type' => 'warranties',
            'filename_prefix' => 'warranty-tracker',
            'csv' => [
                'headers' => [
                    '#',
                    'Provider',
                    'Description',
                    'Length (months)',
                    'Start Date',
                    'Expiry Date',
                    'Active',
                    'Asset Tag',
                    'Asset Description',
                    'Site',
                    'Department',
                ],
                'fields' => [
                    'index',
                    'provider',
                    'description',
                    'length_months',
                    [
                        'field' => 'start_date',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d'),
                        'default' => 'N/A',
                    ],
                    [
                        'field' => 'expiry_date',
                        'transform' => fn ($value) => optional($value)->format('Y-m-d'),
                        'default' => 'N/A',
                    ],
                    [
                        'field' => 'active',
                        'transform' => fn ($value) => $value ? 'Yes' : 'No',
                    ],
                    ['field' => 'asset.asset_tag', 'default' => 'N/A'],
                    ['field' => 'asset.description', 'default' => 'N/A'],
                    ['field' => 'asset.site.name', 'default' => 'N/A'],
                    ['field' => 'asset.department.name', 'default' => 'N/A'],
                ],
                'with_relations' => [
                    'asset:id,asset_tag,description,site_id,department_id',
                    'asset.site:id,name',
                    'asset.department:id,name',
                ],
            ],
            'pdf' => [
                'view' => 'pdf-layout',
                'document_title' => 'Warranties - Full Export',
                'filename_prefix' => 'warranty-tracker',
                'orientation' => 'landscape',
                'with_relations' => [
                    'asset:id,asset_tag,description,site_id,department_id',
                    'asset.site:id,name',
                    'asset.department:id,name',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'provider', 'label' => 'Provider'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'length_months', 'label' => 'Length (months)'],
                    [
                        'key' => 'expiry_date',
                        'label' => 'Expiry Date',
                        'transform' => fn ($model) => optional($model->expiry_date)->format('Y-m-d') ?? 'N/A',
                    ],
                    [
                        'key' => 'active',
                        'label' => 'Active',
                        'transform' => fn ($model) => $model->active ? 'Yes' : 'No',
                    ],
                    ['key' => 'asset.asset_tag', 'label' => 'Asset Tag'],
                    ['key' => 'asset.department.name', 'label' => 'Department'],
                ],
            ],
            'current_page' => [
                'view' => 'pdf-layout',
                'document_title' => 'Warranties - Current View',
                'filename_prefix' => 'warranty-tracker-current',
                'orientation' => 'landscape',
                'with_relations' => [
                    'asset:id,asset_tag,description,site_id,department_id',
                    'asset.site:id,name',
                    'asset.department:id,name',
                ],
                'columns' => [
                    ['key' => 'index', 'label' => '#'],
                    ['key' => 'provider', 'label' => 'Provider'],
                    ['key' => 'expiry_date', 'label' => 'Expires'],
                    ['key' => 'asset.asset_tag', 'label' => 'Asset Tag'],
                    ['key' => 'active', 'label' => 'Active'],
                ],
            ],
        ];
    }\n}\n



