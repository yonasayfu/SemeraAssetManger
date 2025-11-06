<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Lease;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\Move;
use App\Models\Staff;
use App\Models\Reservation;
use App\Models\Site;

use App\Models\Warranty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Services\AlertService;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Staff::first();
        if (!$admin) {
            $this->command->warn('No users found. Ensure DatabaseSeeder creates an admin before running SampleDataSeeder.');
            return;
        }

        // Company
        $company = Company::firstOrCreate([
            'name' => 'Acme Corp',
        ], [
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'state' => 'CA',
            'postal_code' => '90210',
            'country' => 'USA',
            'timezone' => 'UTC',
            'currency' => 'USD',
            'date_format' => 'Y-m-d',
            'financial_year_start' => Carbon::parse('2025-07-01'),
            'hr_email' => 'hr@example.com',
        ]);
        if (empty($company->hr_email)) {
            $company->hr_email = 'hr@example.com';
            $company->save();
        }

        // Sites
        $hq = Site::firstOrCreate(['name' => 'Headquarters'], [
            'description' => 'Main office',
            'address' => '123 Main St',
            'suite' => 'Suite 100',
            'city' => 'Metropolis',
            'state' => 'CA',
            'postal_code' => '90210',
            'country' => 'USA',
        ]);
        $plant = Site::firstOrCreate(['name' => 'Manufacturing Plant'], [
            'description' => 'Primary plant',
            'address' => '456 Industrial Rd',
            'city' => 'Factoryville',
            'state' => 'TX',
            'postal_code' => '73301',
            'country' => 'USA',
        ]);

        // Locations
        $locations = [
            Location::firstOrCreate(['site_id' => $hq->id, 'name' => 'Warehouse A']),
            Location::firstOrCreate(['site_id' => $hq->id, 'name' => 'IT Closet']),
            Location::firstOrCreate(['site_id' => $plant->id, 'name' => 'Line 1']),
            Location::firstOrCreate(['site_id' => $plant->id, 'name' => 'Line 2']),
        ];

        // Categories
        $categories = [
            'Computers' => null,
            'Vehicles' => null,
            'Furniture' => null,
            'Machinery' => null,
        ];
        foreach (array_keys($categories) as $name) {
            $categories[$name] = Category::firstOrCreate(['name' => $name]);
        }

        // Departments
        $departments = [
            'IT' => null,
            'Operations' => null,
            'Finance' => null,
            'HR' => null,
        ];
        foreach (array_keys($departments) as $name) {
            $departments[$name] = Department::firstOrCreate(['name' => $name]);
        }

        // Customers
        if (class_exists(Customer::class)) {
            Customer::firstOrCreate(['name' => 'Globex LLC'], [
                'email' => 'contact@globex.com',
                'phone' => '+1-555-0100',
            ]);
        }

        // Generate a richer set of assets across statuses
        $statuses = ['Available', 'Checked Out', 'Under Repair', 'Leased'];
        $assetConfigs = [
            ['desc' => 'Dell Latitude 7440 Laptop', 'brand' => 'Dell', 'model' => 'Latitude 7440', 'cat' => 'Computers', 'dept' => 'IT', 'site' => $hq->id, 'loc' => Arr::first($locations)->id],
            ['desc' => 'Toyota Forklift', 'brand' => 'Toyota', 'model' => '8FGCU25', 'cat' => 'Machinery', 'dept' => 'Operations', 'site' => $plant->id, 'loc' => $locations[2]->id],
            ['desc' => 'MacBook Pro 14"', 'brand' => 'Apple', 'model' => 'M3 Pro', 'cat' => 'Computers', 'dept' => 'IT', 'site' => $hq->id, 'loc' => $locations[1]->id],
            ['desc' => 'Office Desk', 'brand' => 'Ikea', 'model' => 'Bekant', 'cat' => 'Furniture', 'dept' => 'HR', 'site' => $hq->id, 'loc' => $locations[0]->id],
            ['desc' => 'CNC Machine', 'brand' => 'Haas', 'model' => 'VF-2', 'cat' => 'Machinery', 'dept' => 'Operations', 'site' => $plant->id, 'loc' => $locations[3]->id],
        ];

        $assets = [];
        for ($i = 0; $i < 25; $i++) {
            $base = $assetConfigs[$i % count($assetConfigs)];
            $status = $statuses[$i % count($statuses)];

            $row = [
                'asset_tag' => 'AST-' . Str::upper(Str::random(6)),
                'description' => $base['desc'],
                'purchase_date' => Carbon::now()->subDays(rand(30, 730))->toDateString(),
                'cost' => rand(200, 25000),
                'currency' => 'USD',
                'brand' => $base['brand'],
                'model' => $base['model'],
                'serial_no' => Str::upper(Str::random(12)),
                'project_code' => rand(0, 1) ? 'PRJ-' . rand(100, 999) : null,
                'asset_condition' => Arr::random(['New', 'Good', 'Fair', 'Poor']),
                'site_id' => $base['site'],
                'location_id' => $base['loc'],
                'category_id' => $categories[$base['cat']]->id,
                'department_id' => $departments[$base['dept']]->id,
                'staff_id' => rand(0, 1) ? Staff::inRandomOrder()->first()?->id : null,
                'status' => $status,
                'photo' => null,
                'created_by' => $admin->id,
            ];

            // Randomize created_at within last 12 months for better dashboard trends
            $created = Carbon::now()->subDays(rand(0, 365));
            $asset = Asset::create(array_merge($row, ['created_at' => $created, 'updated_at' => $created]));
            $assets[] = $asset;
        }

        // Maintenance - mix of scheduled/open/completed
        foreach ($assets as $asset) {
            if (rand(0, 1)) {
                Maintenance::firstOrCreate([
                    'asset_id' => $asset->id,
                    'title' => 'Quarterly Inspection',
                ], [
                    'description' => 'Routine check',
                    'maintenance_type' => 'Preventive',
                    'scheduled_for' => Carbon::now()->addDays(rand(3, 45)),
                    'status' => Arr::random(['Open', 'Scheduled', 'Completed']),
                    'cost' => null,
                    'vendor' => null,
                ]);
            }
        }

        // Warranty
        foreach ($assets as $asset) {
            Warranty::firstOrCreate([
                'asset_id' => $asset->id,
                'description' => 'Standard Warranty',
            ], [
                'provider' => 'OEM',
                'length_months' => 12,
                'start_date' => Carbon::now()->subMonths(3)->toDateString(),
                'expiry_date' => Carbon::now()->addMonths(9)->toDateString(),
                'active' => true,
            ]);
        }

        // Create a few warranties that will expire within the next 60 days to populate alerts
        foreach (array_slice($assets, 0, 3) as $asset) {
            Warranty::updateOrCreate([
                'asset_id' => $asset->id,
                'description' => 'Near-Term Warranty',
            ], [
                'provider' => 'OEM',
                'length_months' => 12,
                'start_date' => Carbon::now()->subMonths(10)->toDateString(),
                'expiry_date' => Carbon::now()->addDays(rand(7, 55))->toDateString(),
                'active' => true,
            ]);
        }

        // Leases on a few assets (randomized)
        foreach (array_slice($assets, 0, 5) as $leased) {
            Lease::firstOrCreate([
                'asset_id' => $leased->id,
                'lessee_type' => 'department',
                'lessee_id' => $departments['IT']->id,
            ], [
                'start_at' => Carbon::now()->subWeeks(rand(1, 8))->toDateString(),
                'end_at' => Carbon::now()->addDays(rand(7, 120))->toDateString(),
                'rate_minor' => rand(200, 900),
                'currency' => 'USD',
                'terms' => 'Standard lease terms',
                'status' => 'active',
            ]);
        }

        // Ensure at least one lease ends within 30 days deterministically for alerts
        $targetAsset = $assets[0] ?? null;
        if ($targetAsset) {
            Lease::updateOrCreate([
                'asset_id' => $targetAsset->id,
                'lessee_type' => 'department',
                'lessee_id' => $departments['IT']->id,
            ], [
                'start_at' => Carbon::now()->subDays(20)->toDateString(),
                'end_at' => Carbon::now()->addDays(10)->toDateString(),
                'rate_minor' => 500,
                'currency' => 'USD',
                'terms' => 'Seeded near-term lease',
                'status' => 'active',
            ]);

            // Keep asset status consistent
            $targetAsset->update(['status' => 'Leased']);
        }

        // Reservations spread out
        foreach (array_slice($assets, 5, 5) as $reserved) {
            Reservation::firstOrCreate([
                'asset_id' => $reserved->id,
                'requester_id' => $admin->id,
            ], [
                'start_at' => Carbon::now()->addDays(rand(3, 14)),
                'end_at' => Carbon::now()->addDays(rand(15, 30)),
                'status' => Arr::random(['pending', 'approved']),
            ]);
        }

        // Moves
        foreach ($assets as $asset) {
            Move::firstOrCreate([
                'asset_id' => $asset->id,
                'moved_by' => $admin->id,
                'moved_at' => Carbon::now()->subDays(10),
            ], [
                'from_location_id' => $asset->location_id,
                'to_location_id' => Arr::last($locations)->id,
                'reason' => 'Initial placement',
            ]);
        }

        // Create some maintenance due soon and overdue
        foreach (array_slice($assets, 0, 3) as $asset) {
            Maintenance::updateOrCreate([
                'asset_id' => $asset->id,
                'title' => 'Due Soon Check',
            ], [
                'description' => 'Routine check',
                'maintenance_type' => 'Preventive',
                'scheduled_for' => Carbon::now()->addDays(rand(3, 10)),
                'status' => 'Open',
            ]);
        }
        foreach (array_slice($assets, 3, 3) as $asset) {
            Maintenance::updateOrCreate([
                'asset_id' => $asset->id,
                'title' => 'Overdue Check',
            ], [
                'description' => 'Follow-up check',
                'maintenance_type' => 'Corrective',
                'scheduled_for' => Carbon::now()->subDays(rand(3, 14)),
                'status' => 'Open',
            ]);
        }

        // Add an overdue checkout
        $anyAsset = $assets[0] ?? null;
        if ($anyAsset) {
            \App\Models\Checkout::firstOrCreate([
                'asset_id' => $anyAsset->id,
                'assignee_type' => 'staff',
                'assignee_id' => $admin->id,
            ], [
                'due_at' => Carbon::now()->subDays(3),
                'checked_out_at' => Carbon::now()->subDays(10),
                'status' => 'open',
                'notes' => 'Seeded overdue checkout',
            ]);
        }

        $this->command->info('Sample data seeded.');

        // Generate sample alerts
        $alertService = new AlertService();
        $alertService->checkAssetsDue();
        $alertService->checkAssetsPastDue();
        $alertService->checkLeasesExpiring();
        $alertService->checkMaintenanceDue($admin->id);
        $alertService->checkMaintenanceOverdue();
        $alertService->checkOverdueCheckouts();
        $alertService->checkWarrantiesExpiring();

        $this->command->info('Sample alerts generated.');
    }
}
