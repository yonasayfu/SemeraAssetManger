<?php

namespace App\Jobs;

use App\Models\Maintenance;
use App\Services\MaintenanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateRecurringMaintenanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(MaintenanceService $maintenanceService): void
    {
        Maintenance::query()
            ->where('is_recurring', true)
            ->whereNotNull('next_scheduled_for')
            ->where('next_scheduled_for', '<=', now())
            ->with('asset')
            ->chunkById(50, function ($maintenances) use ($maintenanceService) {
                foreach ($maintenances as $maintenance) {
                    $maintenanceService->generateNextOccurrence($maintenance);
                }
            });
    }
}
