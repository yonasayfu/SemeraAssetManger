<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Asset;
use App\Models\Checkout;
use App\Models\Lease;
use App\Models\Maintenance;
use App\Models\Staff as User;
use App\Models\Warranty;
use Carbon\Carbon;

class AlertService
{
    /**
     * Check for overdue checkouts and generate alerts.
     */
    public function checkOverdueCheckouts(): void
    {
        $overdueCheckouts = Checkout::where('due_date', '<', Carbon::now())
            ->whereNull('checked_in_at')
            ->get();

        foreach ($overdueCheckouts as $checkout) {
            Alert::firstOrCreate(
                [
                    'type' => 'Overdue Checkout',
                    'asset_id' => $checkout->asset_id,
                    'source_id' => $checkout->id,
                    'source_type' => Checkout::class,
                ],
                [
                    'due_date' => $checkout->due_date,
                    'message' => 'Asset ' . $checkout->asset->asset_tag . ' is overdue for check-in.',
                ]
            );
        }
    }

    /**
     * Check for expiring leases and generate alerts.
     */
    public function checkExpiringLeases(): void
    {
        $expiringLeases = Lease::where('end_at', '<', Carbon::now()->addDays(30)) // Leases expiring in next 30 days
            ->where('end_at', '>', Carbon::now())
            ->get();

        foreach ($expiringLeases as $lease) {
            Alert::firstOrCreate(
                [
                    'type' => 'Expiring Lease',
                    'asset_id' => $lease->asset_id,
                    'source_id' => $lease->id,
                    'source_type' => Lease::class,
                ],
                [
                    'due_date' => $lease->end_at,
                    'message' => 'Lease for asset ' . $lease->asset->asset_tag . ' is expiring on ' . $lease->end_at->format('Y-m-d') . '.',
                ]
            );
        }
    }

    /**
     * Check for maintenance due and generate alerts.
     */
    public function checkMaintenanceDue(?int $userId = null): void
    {
        $maintenanceDue = Maintenance::where('scheduled_for', '<', Carbon::now()->addDays(30)) // Maintenance due in next 30 days
            ->where('scheduled_for', '>', Carbon::now())
            ->where('status', 'Open')
            ->get();

        foreach ($maintenanceDue as $maintenance) {
            Alert::firstOrCreate(
                [
                    'type' => 'Maintenance Due',
                    'asset_id' => $maintenance->asset_id,
                    'source_id' => $maintenance->id,
                    'source_type' => Maintenance::class,
                ],
                [
                    'user_id' => $userId,
                    'due_date' => $maintenance->scheduled_for,
                    'message' => 'Maintenance for asset ' . $maintenance->asset->asset_tag . ' is due on ' . $maintenance->scheduled_for->format('Y-m-d') . '.',
                ]
            );
        }
    }

    /**
     * Check for overdue maintenance and generate alerts. 
     */
    public function checkMaintenanceOverdue(): void
    {
        $maintenanceOverdue = Maintenance::where('scheduled_for', '<', Carbon::now())
            ->where('status', 'Open')
            ->get();

        foreach ($maintenanceOverdue as $maintenance) {
            Alert::firstOrCreate(
                [
                    'type' => 'Maintenance Overdue',
                    'asset_id' => $maintenance->asset_id,
                    'source_id' => $maintenance->id,
                    'source_type' => Maintenance::class,
                ],
                [
                    'due_date' => $maintenance->scheduled_for,
                    'message' => 'Maintenance for asset ' . $maintenance->asset->asset_tag . ' is overdue since ' . $maintenance->scheduled_for->format('Y-m-d') . '.',
                ]
            );
        }
    }

    /**
     * Check for expiring warranties and generate alerts.
     */
    public function checkWarrantiesExpiring(): void
    {
        $expiringWarranties = Warranty::where('expiry_date', '<', Carbon::now()->addDays(60)) // Warranties expiring in next 60 days
            ->where('expiry_date', '>', Carbon::now())
            ->where('active', true)
            ->get();

        foreach ($expiringWarranties as $warranty) {
            Alert::firstOrCreate(
                [
                    'type' => 'Warranty Expiring',
                    'asset_id' => $warranty->asset_id,
                    'source_id' => $warranty->id,
                    'source_type' => Warranty::class,
                ],
                [
                    'due_date' => $warranty->expiry_date,
                    'message' => 'Warranty for asset ' . $warranty->asset->asset_tag . ' is expiring on ' . $warranty->expiry_date->format('Y-m-d') . '.',
                ]
            );
        }
    }

    /**
     * Check for assets due (e.g., for audit or re-calibration) and generate alerts.
     * This method assumes there's a 'next_audit_date' or similar field on the Asset model.
     * If not, this would need to be adapted based on how 'assets due' is defined.
     */
    public function checkAssetsDue(): void
    {
        // For demonstration, let's consider assets due for a "review" 1 year after purchase
        $assetsDueForReview = Asset::where('purchase_date', '<=', Carbon::now()->subYears(1))
            ->whereDoesntHave('alerts', function ($query) {
                $query->where('type', 'Assets Due')
                    ->where('due_date', '>', Carbon::now()->subYears(1)); // Only consider alerts for the current review cycle
            })
            ->get();

        foreach ($assetsDueForReview as $asset) {
            Alert::firstOrCreate(
                [
                    'type' => 'Assets Due',
                    'asset_id' => $asset->id,
                    'source_id' => $asset->id,
                    'source_type' => Asset::class,
                ],
                [
                    'due_date' => $asset->purchase_date->addYears(1), // Example: Due 1 year after purchase
                    'message' => 'Asset ' . $asset->asset_tag . ' is due for a review 1 year after purchase on ' . $asset->purchase_date->addYears(1)->format('Y-m-d') . '.',
                ]
            );
        }
    }

    /**
     * Check for assets past due (e.g., for audit or re-calibration) and generate alerts.
     */
    public function checkAssetsPastDue(): void
    {
        // For demonstration, let's consider assets past due for a "review" 1 year after purchase
        $assetsPastDueForReview = Asset::where('purchase_date', '<=', Carbon::now()->subYears(1))
            ->whereDoesntHave('alerts', function ($query) {
                $query->where('type', 'Assets Past Due')
                    ->where('due_date', '<', Carbon::now());
            })
            ->get();

        foreach ($assetsPastDueForReview as $asset) {
            Alert::firstOrCreate(
                [
                    'type' => 'Assets Past Due',
                    'asset_id' => $asset->id,
                    'source_id' => $asset->id,
                    'source_type' => Asset::class,
                ],
                [
                    'due_date' => $asset->purchase_date->addYears(1), // Example: Due 1 year after purchase
                    'message' => 'Asset ' . $asset->asset_tag . ' was due for a review 1 year after purchase on ' . $asset->purchase_date->addYears(1)->format('Y-m-d') . ' and is now past due.',
                ]
            );
        }
    }

    /**
     * Check for leases expiring and generate alerts.
     */
    public function checkLeasesExpiring(): void
    {
        $expiringLeases = Lease::where('end_at', '<', Carbon::now()->addDays(30)) // Leases expiring in next 30 days
            ->where('end_at', '>', Carbon::now())
            ->get();

        foreach ($expiringLeases as $lease) {
            Alert::firstOrCreate(
                [
                    'type' => 'Lease Expiring',
                    'asset_id' => $lease->asset_id,
                    'source_id' => $lease->id,
                    'source_type' => Lease::class,
                ],
                [
                    'due_date' => $lease->end_at,
                    'message' => 'Lease for asset ' . $lease->asset->asset_tag . ' is expiring on ' . $lease->end_at->format('Y-m-d') . '.',
                ]
            );
        }
    }
}
