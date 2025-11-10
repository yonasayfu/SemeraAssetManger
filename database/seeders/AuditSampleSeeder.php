<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Location;
use App\Models\Site;
use App\Models\Staff;
use App\Services\AuditService;
use Illuminate\Database\Seeder;

class AuditSampleSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have the domain entities seeded
        $site = Site::first();
        $location = $site ? Location::where('site_id', $site->id)->first() : null;
        if (! $site || ! $location) {
            $this->command?->warn('No sites/locations found; run SampleDataSeeder first.');
            return;
        }

        // Ensure there are assets at the chosen site/location
        $assetsAtLocation = Asset::where('site_id', $site->id)
            ->where('location_id', $location->id)
            ->take(10)
            ->pluck('id');

        if ($assetsAtLocation->isEmpty()) {
            $this->command?->warn('No assets at the seed location; skipping audit samples.');
            return;
        }

        $service = new AuditService();

        // Ongoing audit with a mix of found/missing
        $ongoing = $service->start([
            'name' => 'Q4 Warehouse Cycle Count',
            'site_id' => $site->id,
            'location_id' => $location->id,
            'asset_ids' => $assetsAtLocation->all(),
        ]);

        // Mark half of items as found, half as pending/missing
        $ongoing->load('auditAssets');
        foreach ($ongoing->auditAssets as $idx => $auditAsset) {
            // Alternate found true/false
            $service->updateAuditAsset($auditAsset, [
                'found' => $idx % 2 === 0,
                'notes' => $idx % 2 === 0 ? 'Scanned in rack A' : 'Not on shelf',
            ]);
        }

        // Completed audit at site-level (gathers more assets from the same site)
        $moreAssets = Asset::where('site_id', $site->id)->take(8)->pluck('id');
        if ($moreAssets->isNotEmpty()) {
            $completed = $service->start([
                'name' => 'Plant Annual Verification',
                'site_id' => $site->id,
                'location_id' => null,
                'asset_ids' => $moreAssets->all(),
            ]);

            $completed->load('auditAssets');
            // Mark all as found
            foreach ($completed->auditAssets as $auditAsset) {
                $service->updateAuditAsset($auditAsset, [
                    'found' => true,
                    'notes' => 'Verified by seed',
                ]);
            }
            $service->complete($completed);
        }

        $this->command?->info('Audit samples seeded.');
    }
}

