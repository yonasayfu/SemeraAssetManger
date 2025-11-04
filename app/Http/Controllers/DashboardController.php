<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $user = Auth::user();

        // Live metrics directly from the database (no cache) for realtime dashboard
        [$totalChange, $availChange, $coChange, $repairChange] = $this->buildMetricChanges($now);

        $metrics = [
            [
                'label' => 'Total Assets',
                'value' => Asset::count(),
                'icon' => 'Archive',
                'change' => $totalChange,
            ],
            [
                'label' => 'Available',
                'value' => Asset::where('status', 'Available')->count(),
                'icon' => 'CheckCircle',
                'change' => $availChange,
            ],
            [
                'label' => 'Checked Out',
                'value' => Asset::where('status', 'Checked Out')->count(),
                'icon' => 'Users',
                'change' => $coChange,
            ],
            [
                'label' => 'Under Repair',
                'value' => Asset::where('status', 'Under Repair')->count(),
                'icon' => 'Wrench',
                'change' => $repairChange,
            ],
        ];

        return Inertia::render('Dashboard', [
            'metrics' => $metrics,
            'maintenance' => $this->getUpcomingMaintenanceList($now),
            'calendarEvents' => $this->getUpcomingEvents($now),
            'recentExports' => \App\Models\DataExport::orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($export) {
                    return [
                        'id' => (string) $export->id,
                        'name' => $export->name ?? 'Export',
                        'type' => $export->type ?? 'Unknown',
                        'status' => $export->status ?? 'unknown',
                        'completed_at' => optional($export->completed_at)->toDateTimeString(),
                        'requested_by' => optional($export->user)->name ?? null,
                    ];
                }),
            'recentActivity' => \App\Models\ActivityLog::orderBy('created_at', 'desc')
                ->take(6)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'description' => $log->description,
                        'action' => $log->event ?? null,
                        'causer' => optional($log->causer)->name ?? null,
                        'occurred_at' => optional($log->created_at)->toDateTimeString(),
                    ];
                }),
            'assetValueByCategoryChartData' => $this->getAssetValueByCategoryChartData(),
            'staffTrend' => $this->buildStaffTrend($now),
            'fiscalYearData' => $this->getFiscalYearData($now),
        ]);
    }

    private function getUpcomingEvents(Carbon $now): array
    {
        $maintenance = \App\Models\Maintenance::with('asset')
            ->where('status', 'Scheduled')
            ->where('scheduled_for', '>=', $now)
            ->orderBy('scheduled_for')
            ->take(5)
            ->get()
            ->map(function (\App\Models\Maintenance $maintenance) {
                return [
                    'id' => $maintenance->id,
                    'title' => 'Maintenance Scheduled',
                    'asset_tag' => optional($maintenance->asset)->asset_tag,
                    'date' => $maintenance->scheduled_for->toDateString(),
                    'type' => 'maintenance',
                ];
            });

        $leases = \App\Models\Lease::with('asset')
            ->where('end_at', '>=', $now)
            ->orderBy('end_at')
            ->take(5)
            ->get()
            ->map(function (\App\Models\Lease $lease) {
                return [
                    'id' => $lease->id,
                    'title' => 'Lease Expiration',
                    'asset_tag' => optional($lease->asset)->asset_tag,
                    'date' => $lease->end_at->toDateString(),
                    'type' => 'lease',
                ];
            });

        $warranties = \App\Models\Warranty::with('asset')
            ->where('expiry_date', '>=', $now)
            ->orderBy('expiry_date')
            ->take(5)
            ->get()
            ->map(function (\App\Models\Warranty $warranty) {
                return [
                    'id' => $warranty->id,
                    'title' => 'Warranty Expiration',
                    'asset_tag' => optional($warranty->asset)->asset_tag,
                    'date' => $warranty->expiry_date->toDateString(),
                    'type' => 'warranty',
                ];
            });

        return collect($maintenance->toArray())
            ->merge($leases->toArray())
            ->merge($warranties->toArray())
            ->sortBy('date')
            ->values()
            ->toArray();
    }

    private function getUpcomingMaintenanceList(Carbon $now): array
    {
        return \App\Models\Maintenance::with('asset')
            ->where('status', 'Scheduled')
            ->where('scheduled_for', '>=', $now)
            ->orderBy('scheduled_for')
            ->take(5)
            ->get()
            ->map(function (\App\Models\Maintenance $maintenance) {
                return [
                    'id' => $maintenance->id,
                    'title' => 'Maintenance Scheduled',
                    'asset_tag' => optional($maintenance->asset)->asset_tag,
                    'scheduled_for' => $maintenance->scheduled_for->toDateString(),
                    'status' => $maintenance->status,
                ];
            })
            ->values()
            ->toArray();
    }

    private function getAssetValueByCategoryChartData(): array
    {
        $data = Asset::query()
            ->selectRaw('categories.name as category, SUM(assets.cost) as value')
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->pluck('value', 'category');

        return [
            'labels' => $data->keys(),
            'series' => $data->values(),
        ];
    }

    private function getFiscalYearData(Carbon $now): array
    {
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        $totalAssets = Asset::whereBetween('created_at', [$startOfYear, $endOfYear])->count();
        $checkedOutAssets = Asset::where('status', 'Checked Out')->whereBetween('created_at', [$startOfYear, $endOfYear])->count();
        $leasedAssets = Asset::where('status', 'Leased')->whereBetween('created_at', [$startOfYear, $endOfYear])->count();

        return [
            'totalAssets' => $totalAssets,
            'checkedOutAssets' => $checkedOutAssets,
            'leasedAssets' => $leasedAssets,
        ];
    }

    /**
     * Construct a month-over-month staff onboarding trend for the past six months.
     */
    private function buildStaffTrend(Carbon $now): array
    {
        $start = $now->copy()->subMonths(5)->startOfMonth();

        $labels = [];
        $series = [];
        $cursor = $start->copy();

        while ($cursor <= $now) {
            $labels[] = $cursor->format('M Y');
            $count = Staff::whereBetween('created_at', [$cursor->copy()->startOfMonth(), $cursor->copy()->endOfMonth()])->count();
            $series[] = (int) $count;
            $cursor->addMonth();
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    private function buildMetricChanges(Carbon $now): array
    {
        $start30 = $now->copy()->subDays(30);
        $prevStart = $now->copy()->subDays(60);
        $prevEnd = $start30;

        $currentCreated = Asset::whereBetween('created_at', [$start30, $now])->count();
        $prevCreated = Asset::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $currentAvail = Asset::where('status', 'Available')->whereBetween('created_at', [$start30, $now])->count();
        $prevAvail = Asset::where('status', 'Available')->whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $currentCo = Asset::where('status', 'Checked Out')->whereBetween('created_at', [$start30, $now])->count();
        $prevCo = Asset::where('status', 'Checked Out')->whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $currentRepair = Asset::where('status', 'Under Repair')->whereBetween('created_at', [$start30, $now])->count();
        $prevRepair = Asset::where('status', 'Under Repair')->whereBetween('created_at', [$prevStart, $prevEnd])->count();

        return [
            $this->percentChange($currentCreated, $prevCreated, 'vs. prev 30 days'),
            $this->percentChange($currentAvail, $prevAvail, 'new in 30 days'),
            $this->percentChange($currentCo, $prevCo, 'new in 30 days'),
            $this->percentChange($currentRepair, $prevRepair, 'new in 30 days'),
        ];
    }

    private function percentChange(int $current, int $previous, string $label): ?array
    {
        $delta = $current - $previous;
        if ($current === 0 && $previous === 0) {
            return [
                'direction' => 'flat',
                'percentage' => 0,
                'label' => $label,
            ];
        }

        $base = max($previous, 1);
        $pct = round(($delta / $base) * 100);
        $direction = $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat');

        return [
            'direction' => $direction,
            'percentage' => abs($pct),
            'label' => $label,
        ];
    }
}
