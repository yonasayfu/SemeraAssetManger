<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke(Request $request)
    {
        $filters = $request->all();
        // For custom reports, the 'definition' would come from a saved report or dynamic UI input
        $definition = []; // Placeholder for actual custom report definition
        $reports = $this->reportService->getCustomReportQuery($definition)->paginate(10);

        return Inertia::render('Reports/Custom', [
            'reports' => $reports,
            'filters' => $filters,
            'definition' => $definition,
        ]);
    }
}
