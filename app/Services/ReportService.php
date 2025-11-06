<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Audit;
use App\Models\Checkout;
use App\Models\Lease;
use App\Models\Maintenance;
use App\Models\Reservation;
use App\Models\Contract;
use App\Models\PurchaseOrder;
use App\Models\Software;
use App\Models\ActivityLog; // Assuming ActivityLog is used for transactions/status changes
use App\Models\SavedReport;
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
            ->when(isset($filters['department_id']), fn ($query) => $query->where('department_id', $filters['department_id']))
            ->when(isset($filters['date_range']), fn ($query) => $query->whereBetween('purchase_date', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            // Add more filters as needed
            ->with(['category', 'site', 'location', 'department', 'assignee']);
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
     * Base query for Contract Reports.
     */
    public function getContractReportQuery(array $filters = []): Builder
    {
        return Contract::query()
            ->when(isset($filters['type']) && $filters['type'] !== '', fn ($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['vendor_id']), fn ($q) => $q->where('vendor_id', $filters['vendor_id']))
            ->when(isset($filters['product_id']), fn ($q) => $q->where('product_id', $filters['product_id']))
            ->when(isset($filters['asset_id']), fn ($q) => $q->where('asset_id', $filters['asset_id']))
            ->when(isset($filters['date_range']), fn ($q) => $q->whereBetween('end_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['vendor:id,name', 'product:id,name', 'asset:id,asset_tag,description']);
    }

    /**
     * Base query for Purchase Order Reports.
     */
    public function getPurchaseOrderReportQuery(array $filters = []): Builder
    {
        return PurchaseOrder::query()
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['vendor_id']), fn ($q) => $q->where('vendor_id', $filters['vendor_id']))
            ->when(isset($filters['product_id']), function ($q) use ($filters) {
                $q->whereHas('items', fn ($iq) => $iq->where('product_id', $filters['product_id']));
            })
            ->when(isset($filters['date_range']), fn ($q) => $q->whereBetween('expected_delivery_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->with(['vendor:id,name', 'items' => function ($q) {
                $q->with(['product:id,name']);
            }]);
    }

    /**
     * Base query for Software Reports.
     */
    public function getSoftwareReportQuery(array $filters = []): Builder
    {
        return Software::query()
            ->when(isset($filters['vendor_id']), fn ($q) => $q->where('vendor_id', $filters['vendor_id']))
            ->when(isset($filters['type']) && in_array($filters['type'], ['saas','on-prem']), fn ($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['seats_min']), fn ($q) => $q->where('seats_total', '>=', (int) $filters['seats_min']))
            ->when(isset($filters['seats_max']), fn ($q) => $q->where('seats_total', '<=', (int) $filters['seats_max']))
            ->with(['vendor:id,name']);
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
        return SavedReport::query()
            ->when(isset($filters['family']) && $filters['family'] !== '', fn ($q) => $q->where('family', $filters['family']))
            ->when(isset($filters['owner_id']), fn ($q) => $q->where('owner_id', $filters['owner_id']))
            ->when(isset($filters['schedule_cron']) && $filters['schedule_cron'] !== '', fn ($q) => $q->where('schedule_cron', 'like', '%'.$filters['schedule_cron'].'%'))
            ->when(isset($filters['date_range']), fn ($q) => $q->whereBetween('last_run_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]));
    }

    // Placeholder for Custom Reports - will involve dynamic query building based on user input
    public function getCustomReportQuery(array $definition): Builder
    {
        // For now, surface saved custom report definitions (metadata), not data rows.
        return SavedReport::query()
            ->where('family', 'custom')
            ->when(isset($definition['owner_id']), fn ($q) => $q->where('owner_id', $definition['owner_id']))
            ->when(isset($definition['data_sources']) && $definition['data_sources'] !== '', function ($q) use ($definition) {
                $q->where('definition_json->data_sources', 'like', '%'.$definition['data_sources'].'%');
            })
            ->when(isset($definition['group_by']) && $definition['group_by'] !== '', function ($q) use ($definition) {
                $q->where('definition_json->group_by', 'like', '%'.$definition['group_by'].'%');
            })
            ->when(isset($definition['tag']) && $definition['tag'] !== '', function ($q) use ($definition) {
                $q->where('definition_json->tags', 'like', '%'.$definition['tag'].'%');
            });
    }

    // Placeholder for Status Reports - might aggregate data from assets or activity logs
    public function getStatusReportQuery(array $filters = []): Builder
    {
        // Use ActivityLog entries that indicate status changes
        return ActivityLog::query()
            ->when(isset($filters['user_id']), fn ($q) => $q->where('causer_id', $filters['user_id']))
            ->when(isset($filters['date_range']), fn ($q) => $q->whereBetween('created_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]))
            ->where('action', 'like', '%status%')
            ->with(['causer', 'subject']);
    }

    // Placeholder for Other Reports
    public function getOtherReportQuery(array $filters = []): Builder
    {
        // Reuse saved report metadata for miscellaneous families
        return SavedReport::query()
            ->where('family', 'other')
            ->when(isset($filters['owner_id']), fn ($q) => $q->where('owner_id', $filters['owner_id']))
            ->when(isset($filters['module']) && $filters['module'] !== '', function ($q) use ($filters) {
                $q->where('definition_json->module', 'like', '%'.$filters['module'].'%');
            })
            ->when(isset($filters['tag']) && $filters['tag'] !== '', function ($q) use ($filters) {
                $q->where('definition_json->tags', 'like', '%'.$filters['tag'].'%');
            })
            ->when(isset($filters['date_range']), fn ($q) => $q->whereBetween('created_at', [
                Carbon::parse($filters['date_range']['start']),
                Carbon::parse($filters['date_range']['end']),
            ]));
    }
}
