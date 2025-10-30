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
use App\Models\User;
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
        $admin = User::first();
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
        ]);

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

        // Assets
        $assetData = [
            [
                'asset_tag' => 'AST-' . Str::upper(Str::random(6)),
                'description' => 'Dell Latitude 7440 Laptop',
                'purchase_date' => Carbon::now()->subMonths(3)->toDateString(),
                'cost' => 1450.00,
                'currency' => 'USD',
                'brand' => 'Dell',
                'model' => 'Latitude 7440',
                'serial_no' => 'DL-' . Str::upper(Str::random(8)),
                'project_code' => 'IT-2025',
                'asset_condition' => 'Good',
                'site_id' => $hq->id,
                'location_id' => Arr::first($locations)->id,
                'category_id' => $categories['Computers']->id,
                'department_id' => $departments['IT']->id,
                'staff_id' => Staff::firstWhere('email', 'john@example.com')?->id,
                'status' => 'Available',
                'photo' => null,
                'created_by' => $admin->id,
            ],
            [
                'asset_tag' => 'AST-' . Str::upper(Str::random(6)),
                'description' => 'Toyota Forklift',
                'purchase_date' => Carbon::now()->subYears(1)->toDateString(),
                'cost' => 23000.00,
                'currency' => 'USD',
                'brand' => 'Toyota',
                'model' => '8FGCU25',
                'serial_no' => 'TY-' . Str::upper(Str::random(8)),
                'project_code' => 'OPS-PLANT',
                'asset_condition' => 'Fair',
                'site_id' => $plant->id,
                'location_id' => $locations[2]->id,
                'category_id' => $categories['Machinery']->id,
                'department_id' => $departments['Operations']->id,
                'staff_id' => null,
                'status' => 'Under Repair',
                'photo' => null,
                'created_by' => $admin->id,
            ],
        ];

        $assets = [];
        foreach ($assetData as $row) {
            $assets[] = Asset::firstOrCreate(
                ['serial_no' => $row['serial_no']],
                $row
            );
        }

        // Maintenance - Scheduled for dashboard
        foreach ($assets as $asset) {
            Maintenance::firstOrCreate([
                'asset_id' => $asset->id,
                'title' => 'Quarterly Inspection',
            ], [
                'description' => 'Routine check',
                'maintenance_type' => 'Preventive',
                'scheduled_for' => Carbon::now()->addDays(rand(3, 30)),
                'status' => 'Open',
                'cost' => null,
                'vendor' => null,
            ]);
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

        // Lease (one asset)
        if (!empty($assets)) {
            $leased = $assets[0];
            Lease::firstOrCreate([
                'asset_id' => $leased->id,
                'lessee_type' => 'department',
                'lessee_id' => $departments['IT']->id,
            ], [
                'start_at' => Carbon::now()->subMonth()->toDateString(),
                'end_at' => Carbon::now()->addMonths(2)->toDateString(),
                'rate_minor' => 500.00,
                'currency' => 'USD',
                'terms' => 'Standard lease terms',
                'status' => 'active',
            ]);
        }

        // Reservation (other asset)
        if (count($assets) > 1) {
            $reserved = $assets[1];
            Reservation::firstOrCreate([
                'asset_id' => $reserved->id,
                'requester_id' => $admin->id,
            ], [
                'start_at' => Carbon::now()->addDays(7),
                'end_at' => Carbon::now()->addDays(10),
                'status' => 'approved',
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

        $this->command->info('Sample data seeded.');

        // Generate sample alerts
        $alertService = new AlertService();
        $alertService->checkAssetsDue();
        $alertService->checkAssetsPastDue();
        $alertService->checkLeasesExpiring();
        $alertService->checkMaintenanceDue($admin->id);
        $alertService->checkWarrantiesExpiring();

        $this->command->info('Sample alerts generated.');
    }
}
