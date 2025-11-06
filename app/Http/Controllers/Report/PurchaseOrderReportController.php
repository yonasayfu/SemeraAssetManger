<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrderReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function __invoke(Request $request)
    {
        $filters = $request->all();
        $reports = $this->reportService->getPurchaseOrderReportQuery($filters)->paginate(10);

        return Inertia::render('Reports/PurchaseOrders', [
            'reports' => $reports,
            'filters' => $filters,
        ]);
    }
}

