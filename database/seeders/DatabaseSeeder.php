<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Notifications\DataExportReadyNotification;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $approvalTimestamp = Carbon::now();

        $adminUser = Staff::factory()
            ->withoutTwoFactor()
            ->create([
                'name' => 'System Administrator',
                'email' => 'admin@example.com',
                'recovery_email' => 'recovery_admin@example.com',
                'phone' => '+1-555-0101',
                'job_title' => 'Administrator',
                'account_status' => Staff::STATUS_ACTIVE,
                'account_type' => Staff::TYPE_INTERNAL,
                'approved_at' => $approvalTimestamp,
                'approved_by' => null,
            ]);

        $adminUser->assignRole('Admin');

        // Seed a sample export notification so the notification bell has data on first login
        $adminUser->notify(new DataExportReadyNotification(
            'Initial Asset Export',
            rtrim(config('app.url') ?? 'http://localhost', '/') . '/exports'
        ));

        $assignmentCatalog = [
            'Fleet Vehicle Inspection',
            'HVAC Preventative Maintenance',
            'Safety Compliance Review',
            'Inventory Cycle Count',
            'Facility Audit Preparation',
        ];

        $samples = [
            [
                'role' => 'Manager',
                'name' => 'Morgan Manager',
                'email' => 'manager@example.com',
                'first_name' => 'Morgan',
                'last_name' => 'Manager',
                'job_title' => 'Operations Manager',
                'status' => 'active',
                'account_type' => Staff::TYPE_INTERNAL,
            ],
            [
                'role' => 'Technician',
                'name' => 'Taylor Technician',
                'email' => 'technician@example.com',
                'first_name' => 'Taylor',
                'last_name' => 'Technician',
                'job_title' => 'Field Technician',
                'status' => 'active',
                'account_type' => Staff::TYPE_INTERNAL,
            ],
            [
                'role' => 'Staff',
                'name' => 'Sydney Staff',
                'email' => 'staff@example.com',
                'first_name' => 'Sydney',
                'last_name' => 'Staff',
                'job_title' => 'Support Specialist',
                'status' => 'active',
                'account_type' => Staff::TYPE_INTERNAL,
            ],
            [
                'role' => 'Auditor',
                'name' => 'Avery Auditor',
                'email' => 'auditor@example.com',
                'first_name' => 'Avery',
                'last_name' => 'Auditor',
                'job_title' => 'Compliance Auditor',
                'status' => 'active',
                'account_type' => Staff::TYPE_INTERNAL,
            ],
            [
                'role' => 'ReadOnly',
                'name' => 'Riley Readonly',
                'email' => 'readonly@example.com',
                'first_name' => 'Riley',
                'last_name' => 'Readonly',
                'job_title' => 'Reporting Analyst',
                'status' => 'inactive',
                'account_type' => Staff::TYPE_INTERNAL,
            ],
        ];

        foreach ($samples as $sample) {
            $sampleUser = Staff::factory()
                ->withoutTwoFactor()
                ->create([
                    'name' => $sample['name'],
                    'email' => $sample['email'],
                    'recovery_email' => 'recovery_' . strtolower(str_replace(' ', '', $sample['role'])) . '@example.com',
                    'phone' => '+1-555-01' . rand(10, 99),
                    'job_title' => $sample['job_title'],
                    'status' => $sample['status'],
                    'account_status' => Staff::STATUS_ACTIVE,
                    'account_type' => $sample['account_type'],
                    'approved_at' => $approvalTimestamp,
                    'approved_by' => $adminUser->id,
                ]);

            $sampleUser->assignRole($sample['role']);

            $assignmentName = $assignmentCatalog[array_rand($assignmentCatalog)];

            $sampleUser->notify(new NewAssignmentNotification(
                $assignmentName,
                $adminUser->name
            ));
        }

        // Seed sample domain data after users exist
        $this->call([
            SampleDataSeeder::class,
            GallerySampleSeeder::class,
            DataExportSampleSeeder::class,
            SupportPageSeeder::class,
        ]);
    }
}
