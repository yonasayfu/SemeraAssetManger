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

        $metrics = Cache::remember('dashboard.metrics', 300, function () {
            return [
                [
                    'label' => 'Total Assets',
                    'value' => Asset::count(),
                    'icon' => 'Archive',
                ],
                [
                    'label' => 'Available',
                    'value' => Asset::where('status', 'Available')->count(),
                    'icon' => 'CheckCircle',
                ],
                [
                    'label' => 'Checked Out',
                    'value' => Asset::where('status', 'Checked Out')->count(),
                    'icon' => 'Users',
                ],
                [
                    'label' => 'Under Repair',
                    'value' => Asset::where('status', 'Under Repair')->count(),
                    'icon' => 'Wrench',
                ],
            ];
        });

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

        return $maintenance->merge($leases)->merge($warranties)->sortBy('date')->values()->toArray();
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
        return Cache::remember('dashboard.asset_value_by_category', 300, function () {
            $data = Asset::query()
                ->selectRaw('categories.name as category, SUM(assets.cost) as value')
                ->join('categories', 'assets.category_id', '=', 'categories.id')
                ->groupBy('categories.name')
                ->pluck('value', 'category');

            return [
                'labels' => $data->keys(),
                'series' => $data->values(),
            ];
        });
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

        $raw = Staff::selectRaw("to_char(created_at, 'YYYY-MM') as period, COUNT(*) as count")
            ->where('created_at', '>=', $start)
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('count', 'period');

        $labels = [];
        $series = [];
        $cursor = $start->copy();

        while ($cursor <= $now) {
            $periodKey = $cursor->format('Y-m');
            $labels[] = $cursor->format('M Y');
            $series[] = (int) ($raw[$periodKey] ?? 0);
            $cursor->addMonth();
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }
}
