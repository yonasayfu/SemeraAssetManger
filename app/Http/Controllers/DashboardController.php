<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $user = Auth::user();

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'totalAssets' => Asset::count(),
                'availableAssets' => Asset::where('status', 'Available')->count(),
                'checkedOutAssets' => Asset::where('status', 'Checked Out')->count(),
                'leasedAssets' => Asset::where('status', 'Leased')->count(),
                'underRepairAssets' => Asset::where('status', 'Under Repair')->count(),
                'disposedAssets' => Asset::whereIn('status', ['Disposed', 'Lost', 'Donated', 'Sold', 'Broken'])->count(),
                'totalStaff' => Staff::count(),
                'activeStaff' => Staff::where('status', 'active')->count(),
                'newStaffThisMonth' => Staff::where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                'staffLastMonth' => Staff::whereBetween('created_at', [
                    $now->copy()->subMonth()->startOfMonth(),
                    $now->copy()->subMonth()->endOfMonth(),
                ])->count(),
                'totalUsers' => \App\Models\User::count(),
                'twoFactorUsers' => \App\Models\User::whereNotNull('two_factor_secret')->count(),
                'reportsGeneratedToday' => \App\Models\DataExport::where('status', 'completed')
                    ->where('completed_at', '>=', $now->copy()->startOfDay())
                    ->count(),
            ],
            'recentExports' => \App\Models\DataExport::orderBy('created_at', 'desc')->take(5)->get(),
            'recentActivity' => \App\Models\ActivityLog::orderBy('created_at', 'desc')->take(6)->get(),
            'upcomingEvents' => $this->getUpcomingEvents($now),
            'assetValueByCategory' => $this->getAssetValueByCategoryChartData(),
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

    private function getAssetValueByCategoryChartData(): array
    {
        $data = Asset::query()
            ->selectRaw('categories.name as category, SUM(assets.purchase_price) as value')
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

        $raw = Staff::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, COUNT(*) as count')
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
