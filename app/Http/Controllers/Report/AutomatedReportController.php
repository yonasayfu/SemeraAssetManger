<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AutomatedReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke(Request $request)
    {
        $filters = $request->all();
        $reports = $this->reportService->getAutomatedReportQuery($filters)->paginate(10);

        return Inertia::render('Reports/Automated', [
            'reports' => $reports,
            'filters' => $filters,
        ]);
    }
}
