<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Clearance;
use App\Models\ClearanceItem;
use App\Models\Department;
use App\Models\Site;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClearanceSampleSeeder extends Seeder
{
    public function run(): void
    {
        $staff = Staff::where('email', 'staff@example.com')->first() ?: Staff::whereNotNull('id')->skip(1)->first();
        if (!$staff) {
            $this->command?->warn('No staff found for ClearanceSampleSeeder.');
            return;
        }

        $site = Site::firstOrCreate(
            ['name' => 'HQ'],
            [
                'description' => 'Headquarters',
                'address' => '123 Main St',
                'suite' => 'Suite 100',
                'city' => 'Metropolis',
                'state' => 'CA',
                'postal_code' => '90210',
                'country' => 'USA',
            ]
        );
        $cat = Category::firstOrCreate(['name' => 'Computers']);
        $dept = Department::firstOrCreate(['name' => 'IT']);

        $asset = Asset::firstOrCreate([
            'asset_tag' => 'CLR-'.Str::upper(Str::random(5)),
        ], [
            'description' => 'Clearance Demo Laptop',
            'purchase_date' => now()->subYear()->toDateString(),
            'cost' => 1200,
            'currency' => 'USD',
            'site_id' => $site->id,
            'category_id' => $cat->id,
            'department_id' => $dept->id,
            'staff_id' => $staff->id,
            'status' => 'Checked Out',
            'created_by' => Staff::first()->id,
        ]);

        $clearance = Clearance::firstOrCreate([
            'staff_id' => $staff->id,
            'status' => 'submitted',
        ], [
            'requested_by' => $staff->id,
            'submitted_at' => now(),
            'remarks' => 'Demo clearance seeded.',
        ]);

        ClearanceItem::firstOrCreate([
            'clearance_id' => $clearance->id,
            'asset_id' => $asset->id,
        ], [
            'description' => $asset->asset_tag.' - '.$asset->description,
            'action' => 'return',
            'checked' => true,
        ]);

        $this->command?->info('Seeded demo clearance request.');
    }
}
