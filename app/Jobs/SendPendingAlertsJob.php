<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Staff;
use App\Notifications\Alert\ContractExpiringNotification;
use App\Notifications\Alert\PurchaseOrderDueNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPendingAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle(): void
    {
        // Limit to recent unsent alerts to avoid flooding
        $since = Carbon::now()->subDays(7);

        $alerts = Alert::query()
            ->where('sent', false)
            ->where('created_at', '>=', $since)
            ->whereIn('type', [
                'Contract Expiring',
                'PO Due Soon',
                'PO Overdue',
            ])
            ->orderBy('due_date')
            ->get();

        if ($alerts->isEmpty()) {
            return;
        }

        // Notify Admins and Managers
        $recipients = Staff::role(['Admin', 'Manager'])->get();
        if ($recipients->isEmpty()) {
            return;
        }

        foreach ($alerts as $alert) {
            foreach ($recipients as $user) {
                match ($alert->type) {
                    'Contract Expiring' => $user->notify(new ContractExpiringNotification($alert)),
                    'PO Due Soon', 'PO Overdue' => $user->notify(new PurchaseOrderDueNotification($alert)),
                    default => null,
                };
            }

            $alert->forceFill([
                'sent' => true,
                'sent_at' => Carbon::now(),
            ])->save();
        }
    }
}

