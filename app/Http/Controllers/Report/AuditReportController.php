<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Report::class);
        $filters = $request->all();
        $reports = $this->reportService->getAuditReportQuery($filters)->paginate(10);

        return Inertia::render('Reports/Audits', [
            'reports' => $reports,
            'filters' => $filters,
        ]);
    }
}
