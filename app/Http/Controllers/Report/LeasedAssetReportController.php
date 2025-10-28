<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeasedAssetReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke(Request $request)
    {
        $filters = $request->all();
        $reports = $this->reportService->getLeasedAssetReportQuery($filters)->paginate(10);

        return Inertia::render('Reports/LeasedAssets', [
            'reports' => $reports,
            'filters' => $filters,
        ]);
    }
}
