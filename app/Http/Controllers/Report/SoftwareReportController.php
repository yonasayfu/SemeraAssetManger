<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoftwareReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function __invoke(Request $request)
    {
        $filters = $request->all();
        $reports = $this->reportService->getSoftwareReportQuery($filters)->paginate(10);

        return Inertia::render('Reports/Software', [
            'reports' => $reports,
            'filters' => $filters,
        ]);
    }
}

