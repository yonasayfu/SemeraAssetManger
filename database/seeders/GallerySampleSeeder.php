<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetDocument;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GallerySampleSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a few assets have photos pointing to a placeholder SVG
        $placeholder = 'placeholders/placeholder.svg';

        $assets = Asset::inRandomOrder()->limit(8)->get();
        foreach ($assets as $asset) {
            if (! $asset->photo) {
                $asset->photo = $placeholder;
                $asset->save();
            }
        }

        // Create a few asset documents referencing the placeholder
        $targets = Asset::inRandomOrder()->limit(5)->pluck('id');
        $uploaderId = Staff::query()->value('id');
        if (! $uploaderId) {
            $this->command?->warn('No staff available to assign as uploader; skipping AssetDocument seeding.');
            return;
        }
        foreach ($targets as $assetId) {
            AssetDocument::firstOrCreate([
                'asset_id' => $assetId,
                'title' => 'Sample Document '.Str::upper(Str::random(4)),
            ], [
                'file_path' => $placeholder,
                'mime_type' => 'image/svg+xml',
                'uploaded_by' => $uploaderId,
            ]);
        }

        $this->command?->info('Gallery samples seeded (assets with photos and asset documents).');
    }
}
