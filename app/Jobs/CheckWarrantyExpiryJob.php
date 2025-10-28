<?php

namespace App\Jobs;

use App\Services\WarrantyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckWarrantyExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(WarrantyService $warrantyService): void
    {
        $updated = $warrantyService->deactivateExpiredWarranties();

        if ($updated > 0) {
            Log::info('Warranty expiry job deactivated warranties.', ['count' => $updated]);
        }
    }
}
