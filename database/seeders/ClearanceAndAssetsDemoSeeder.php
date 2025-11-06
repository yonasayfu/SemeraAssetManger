<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetDocument;
use App\Models\AssetPhoto;
use App\Models\Category;
use App\Models\Clearance;
use App\Models\ClearanceItem;
use App\Models\Company;
use App\Models\Department;
use App\Models\Location;
use App\Models\Site;
use App\Models\Staff;
use App\Models\Warranty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClearanceAndAssetsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure HR email present for PDF footer and notifications
        $company = Company::first();
        if ($company && empty($company->hr_email)) {
            $company->hr_email = 'hr@example.com';
            $company->save();
        }

        // Ensure basic taxonomy
        $site = Site::firstOrCreate(['name' => 'HQ'], [
            'description' => 'Headquarters',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'state' => 'CA',
            'postal_code' => '90210',
            'country' => 'USA',
        ]);
        $location = Location::firstOrCreate(['site_id' => $site->id, 'name' => 'IT Closet']);
        $it = Department::firstOrCreate(['name' => 'IT']);
        $ops = Department::firstOrCreate(['name' => 'Operations']);
        $computers = Category::firstOrCreate(['name' => 'Computers']);
        $furniture = Category::firstOrCreate(['name' => 'Furniture']);

        // Ensure public placeholder exists for seeded photos/documents
        $placeholderPath = 'placeholders/placeholder.svg';
        if (! Storage::disk('public')->exists($placeholderPath)) {
            Storage::disk('public')->put($placeholderPath, <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="120" height="80" viewBox="0 0 120 80">
  <rect width="120" height="80" fill="#e2e8f0"/>
  <circle cx="24" cy="24" r="14" fill="#94a3b8"/>
  <path d="M0 80 L50 40 L80 65 L120 30 L120 80 Z" fill="#cbd5e1"/>
  <text x="60" y="76" font-size="10" text-anchor="middle" fill="#64748b">placeholder</text>
  Sorry, your browser does not support inline SVG.
</svg>
SVG);
        }

        // Two sample staff for clearance testing
        $staffSamples = [
            [
                'name' => 'Alice Employee',
                'email' => 'alice.employee@example.com',
                'job_title' => 'Support Specialist',
                'department' => $it->id,
            ],
            [
                'name' => 'Bob Employee',
                'email' => 'bob.employee@example.com',
                'job_title' => 'Field Operator',
                'department' => $ops->id,
            ],
        ];

        $createdStaff = [];
        foreach ($staffSamples as $sample) {
            $user = Staff::firstOrCreate(
                ['email' => $sample['email']],
                [
                    'name' => $sample['name'],
                    'password' => 'password',
                    'job_title' => $sample['job_title'],
                    'account_status' => Staff::STATUS_ACTIVE,
                    'account_type' => Staff::TYPE_INTERNAL,
                    'approved_at' => now(),
                ]
            );
            // Assign Staff role if available
            try { $user->assignRole('Staff'); } catch (\Throwable $e) {}
            $createdStaff[] = $user;
        }

        // Assets for each staff with photos, documents, warranties
        foreach ($createdStaff as $index => $user) {
            $assetDefs = $index === 0
                ? [
                    ['desc' => 'Lenovo ThinkPad X1', 'brand' => 'Lenovo', 'model' => 'X1 Carbon', 'cat' => $computers->id],
                    ['desc' => 'iPhone 14 Pro', 'brand' => 'Apple', 'model' => 'A2890', 'cat' => $computers->id],
                ]
                : [
                    ['desc' => 'Dell 27" Monitor', 'brand' => 'Dell', 'model' => 'U2720Q', 'cat' => $computers->id],
                    ['desc' => 'Office Desk', 'brand' => 'Ikea', 'model' => 'Bekant', 'cat' => $furniture->id],
                ];

            $userAssets = [];
            foreach ($assetDefs as $def) {
                $asset = Asset::create([
                    'asset_tag' => strtoupper(Str::random(3)).'-'.Str::upper(Str::random(5)),
                    'description' => $def['desc'],
                    'brand' => $def['brand'],
                    'model' => $def['model'],
                    'site_id' => $site->id,
                    'location_id' => $location->id,
                    'category_id' => $def['cat'],
                    'department_id' => $index === 0 ? $it->id : $ops->id,
                    'staff_id' => $user->id,
                    'status' => 'Checked Out',
                    'purchase_date' => now()->subMonths(rand(1, 18))->toDateString(),
                    'cost' => rand(500, 2500),
                    'currency' => 'USD',
                    'created_by' => Staff::query()->value('id'),
                ]);

                // Photos
                foreach ([
                    ['caption' => 'Front view'],
                    ['caption' => 'Serial plate'],
                ] as $photoMeta) {
                    AssetPhoto::create([
                        'asset_id' => $asset->id,
                        'path' => $placeholderPath,
                        'caption' => $photoMeta['caption'],
                    ]);
                }

                // Documents
                foreach ([
                    ['title' => 'User Manual', 'mime' => 'image/svg+xml'],
                    ['title' => 'Warranty Card', 'mime' => 'image/svg+xml'],
                ] as $doc) {
                    AssetDocument::create([
                        'asset_id' => $asset->id,
                        'title' => $doc['title'],
                        'file_path' => $placeholderPath,
                        'mime_type' => $doc['mime'],
                        'uploaded_by' => $user->id,
                    ]);
                }

                // Warranty
                Warranty::create([
                    'asset_id' => $asset->id,
                    'provider' => 'OEM',
                    'description' => 'Standard Coverage',
                    'length_months' => 12,
                    'start_date' => now()->subMonths(3)->toDateString(),
                    'expiry_date' => now()->addMonths(9)->toDateString(),
                    'active' => true,
                    'notes' => 'Seeded',
                ]);

                $userAssets[] = $asset;
            }

            // Clearance per user
            $status = $index === 0 ? 'submitted' : 'approved';
            $clearance = Clearance::create([
                'staff_id' => $user->id,
                'requested_by' => $user->id,
                'status' => $status,
                'submitted_at' => now(),
                'approved_at' => $status === 'approved' ? now() : null,
                'approved_by' => $status === 'approved' ? ($createdStaff[0]->id ?? null) : null,
                'remarks' => $status === 'approved' ? 'Auto-approved in seed.' : 'Submitted for approval.',
                'hr_email' => optional($company)->hr_email,
            ]);

            foreach ($userAssets as $asset) {
                ClearanceItem::create([
                    'clearance_id' => $clearance->id,
                    'asset_id' => $asset->id,
                    'description' => $asset->asset_tag.' - '.$asset->description,
                    'action' => 'return',
                    'checked' => true,
                    'result' => $status === 'approved' ? 'ok' : null,
                ]);
            }

            // For approved sample, simulate a generated PDF placeholder
            if ($status === 'approved') {
                $pdfPath = 'clearances/demo_'.$clearance->id.'.pdf';
                if (! Storage::disk('local')->exists($pdfPath)) {
                    Storage::disk('local')->put($pdfPath, "%PDF-1.1\n% Seeded placeholder PDF\n1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] >>endobj\nxref\n0 4\n0000000000 65535 f \n0000000010 00000 n \n0000000052 00000 n \n0000000098 00000 n \ntrailer<< /Size 4 /Root 1 0 R >>\nstartxref\n144\n%%EOF");
                }
                $clearance->update(['pdf_path' => $pdfPath]);
            }
        }

        $this->command?->info('Seeded two demo staff with assets, photos, documents, warranties, and clearance records.');
    }
}

