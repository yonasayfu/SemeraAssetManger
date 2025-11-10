<?php

namespace App\Jobs;

use App\Services\AlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AlertService $alertService): void
    {
        $alertService->checkOverdueCheckouts();
        $alertService->checkLeasesExpiring();
        $alertService->checkMaintenanceDue();
        $alertService->checkMaintenanceOverdue();
        $alertService->checkWarrantiesExpiring();
        $alertService->checkAssetsDue();
        $alertService->checkAssetsPastDue();
        $alertService->checkRefreshDue();
        // Freshservice-style
        $alertService->checkContractsExpiring();
        $alertService->checkPurchaseOrdersDue();
    }
}
