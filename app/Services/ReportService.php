<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Audit;
use App\Models\Checkout;
use App\Models\Lease;
use App\Models\Maintenance;
use App\Models\Reservation;
use App\Models\ActivityLog; // Assuming ActivityLog is used for transactions/status changes
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReportService
{
    /**
     * Resolve the correct query builder for a report family.
     *
     * @throws \InvalidArgumentException
     */
    public function getReportQuery(string $family, array $filters = []): Builder
    {
        $method = $this->resolveMethodForFamily($family);

        if (! method_exists($this, $method)) {
            throw new \InvalidArgumentException("Unknown report family [{$family}].");
        }

        /** @phpstan-ignore-next-line */
        return $this->{$method}($filters);
    }

    protected function resolveMethodForFamily(string $family): string
    {
        $normalized = Str::of($family)->replace(['-', '_'], ' ')->studly();
        $method = 'get'.$normalized.'ReportQuery';

        if (method_exists($this, $method)) {
            return $method;
        }

        $singular = Str::singular($normalized);
        $fallback = 'get'.$singular.'ReportQuery';

        return $fallback;
    }

    /**
     * Base query for Asset Reports.
     */
    public function getAssetReportQuery(array $filters = []): Builder
    {
        return Asset::query()
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['category_id']), fn ($query) => $query->where('category_id', $filters['category_id']))
            ->when(isset($filters['site_id']), fn ($query) => $query->where('site_id', $filters['site_id']))
            // Add more filters as needed
            ->with(['category', 'site', 'location', 'department', 'assignedTo']);
    }

    /**
     * Base query for Audit Reports.
     */
    public function getAuditReportQuery(array $filters = []): Builder
    {
        return Audit::query()
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['site_id']), fn ($query) => $query->where('site_id', $filters['site_id']))
            ->when(isset($filters['date_range']), fn ($query) => $query->whereBetween('started_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['site', 'location']);
    }

    /**
     * Base query for Checkout Reports.
     */
    public function getCheckoutReportQuery(array $filters = []): Builder
    {
        return Checkout::query()
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['asset_id']), fn ($query) => $query->where('asset_id', $filters['asset_id']))
            ->when(isset($filters['assignee_id']), fn ($query) => $query->where('assignee_id', $filters['assignee_id']))
            ->when(isset($filters['date_range']), fn ($query) => $query->whereBetween('checked_out_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['asset', 'assignee']);
    }

    /**
     * Base query for Leased Asset Reports.
     */
    public function getLeasedAssetReportQuery(array $filters = []): Builder
    {
        return Lease::query()
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['asset_id']), fn ($query) => $query->where('asset_id', $filters['asset_id']))
            ->when(isset($filters['lessee_id']), fn ($query) => $query->where('lessee_id', $filters['lessee_id']))
            ->when(isset($filters['date_range']), fn ($query) => $query->whereBetween('start_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['asset', 'lessee']);
    }

    /**
     * Base query for Maintenance Reports.
     */
    public function getMaintenanceReportQuery(array $filters = []): Builder
    {
        return Maintenance::query()
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['asset_id']), fn ($query) => $query->where('asset_id', $filters['asset_id']))
            ->when(isset($filters['type']), fn ($query) => $query->where('maintenance_type', $filters['type']))
            ->when(isset($filters['date_range']), fn ($query) => $query->whereBetween('scheduled_for', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['asset']);
    }

    /**
     * Base query for Reservation Reports.
     */
    public function getReservationReportQuery(array $filters = []): Builder
    {
        return Reservation::query()
            ->when(isset($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['asset_id']), fn ($query) => $query->where('asset_id', $filters['asset_id']))
            ->when(isset($filters['requester_id']), fn ($query) => $query->where('requester_id', $filters['requester_id']))
            ->when(isset($filters['date_range']), fn ($query) => $query->whereBetween('start_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['asset', 'requester']);
    }

    /**
     * Base query for Transaction Reports (using ActivityLog).
     */
    public function getTransactionReportQuery(array $filters = []): Builder
    {
        return ActivityLog::query()
            ->when(isset($filters['user_id']), fn ($query) => $query->where('causer_id', $filters['user_id']))
            ->when(isset($filters['action']), fn ($query) => $query->where('action', $filters['action']))
            ->when(isset($filters['model_type']), fn ($query) => $query->where('subject_type', $filters['model_type']))
            ->when(isset($filters['date_range']), fn ($query) => $query->whereBetween('created_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['causer', 'subject']);
    }

    // Placeholder for Automated Reports - will likely use existing queries with scheduling logic
    public function getAutomatedReportQuery(array $filters = []): Builder
    {
        // Example: return $this->getAssetReportQuery($filters);
        return Asset::query(); // Placeholder
    }

    // Placeholder for Custom Reports - will involve dynamic query building based on user input
    public function getCustomReportQuery(array $definition): Builder
    {
        // This will be complex, involving parsing $definition to build a query
        return Asset::query(); // Placeholder
    }

    // Placeholder for Status Reports - might aggregate data from assets or activity logs
    public function getStatusReportQuery(array $filters = []): Builder
    {
        return Asset::query(); // Placeholder
    }

    // Placeholder for Other Reports
    public function getOtherReportQuery(array $filters = []): Builder
    {
        return Asset::query(); // Placeholder
    }
}
