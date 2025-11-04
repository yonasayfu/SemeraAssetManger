<?php

namespace Database\Seeders;

use App\Models\DataExport;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DataExportSampleSeeder extends Seeder
{
    public function run(): void
    {
        $user = Staff::first();
        if (! $user) {
            $this->command?->warn('No staff found; skipping DataExportSampleSeeder.');
            return;
        }

        // Seed two completed exports pointing at sample CSVs in storage/app/exports
        DataExport::updateOrCreate([
            'user_id' => $user->id,
            'name' => 'Initial Asset Export',
            'type' => 'assets',
            'format' => 'csv',
        ], [
            'status' => DataExport::STATUS_COMPLETED,
            'file_path' => 'exports/sample-assets.csv',
            'record_count' => 25,
            'completed_at' => Carbon::now(),
        ]);

        DataExport::updateOrCreate([
            'user_id' => $user->id,
            'name' => 'Warranty Summary',
            'type' => 'warranties',
            'format' => 'csv',
        ], [
            'status' => DataExport::STATUS_COMPLETED,
            'file_path' => 'exports/sample-warranties.csv',
            'record_count' => 10,
            'completed_at' => Carbon::now()->subMinutes(10),
        ]);

        $this->command?->info('Sample data exports seeded.');
    }
}

