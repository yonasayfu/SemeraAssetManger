<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AuditReportListController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
    }

    /**
     * Reports family page: list audits with filters (invokable for /reports/audits).
     */
    public function __invoke(Request $request)
    {
        $filters = $request->all();
        $reports = $this->reportService->getAuditReportQuery($filters)->paginate(10);

        return Inertia::render('Reports/Audits', [
            'reports' => $reports,
            'filters' => $filters,
        ]);
    }

    /**
     * Detailed audit report page: /audits/{audit}/report
     */
    public function show(Audit $audit)
    {
        $audit->load(['site:id,name', 'location:id,name']);

        $items = $audit->auditAssets()
            ->with(['asset:id,asset_tag,description,category_id,site_id,location_id', 'asset.category:id,name', 'asset.site:id,name', 'asset.location:id,name'])
            ->get()
            ->map(function ($auditAsset) {
                $asset = $auditAsset->asset;
                return [
                    'id' => $asset->id,
                    'asset_tag' => $asset->asset_tag,
                    'description' => $asset->description,
                    'category' => $asset->category->name ?? null,
                    'site' => $asset->site->name ?? null,
                    'location' => $asset->location->name ?? null,
                    'notes' => $auditAsset->notes,
                    'found' => (bool) $auditAsset->found,
                ];
            });

        $found = $items->where('found', true)->values()->all();
        $missing = $items->where('found', false)->values()->all();
        $extras = []; // Placeholder: implement extras if tracked separately

        $summary = [
            'total' => $items->count(),
            'found' => count($found),
            'missing' => count($missing),
        ];

        return Inertia::render('Audits/Report', [
            'audit' => $audit,
            'summary' => $summary,
            'found' => $found,
            'missing' => $missing,
            'extras' => $extras,
        ]);
    }

    /**
     * Export a CSV of the audit results.
     */
    public function export(Audit $audit)
    {
        $filename = sprintf('audit-%s-report-%s.csv', Str::slug($audit->name), now()->format('Ymd_His'));

        $callback = function () use ($audit) {
            $handle = fopen('php://output', 'w');
            $headers = ['status', 'asset_id', 'asset_tag', 'description', 'category', 'site', 'location', 'notes'];
            fputcsv($handle, $headers);

            $audit->auditAssets()
                ->with(['asset:id,asset_tag,description,category_id,site_id,location_id', 'asset.category:id,name', 'asset.site:id,name', 'asset.location:id,name'])
                ->orderBy('id')
                ->chunk(500, function ($chunk) use ($handle) {
                    foreach ($chunk as $auditAsset) {
                        $asset = $auditAsset->asset;
                        fputcsv($handle, [
                            $auditAsset->found ? 'found' : 'missing',
                            $asset->id,
                            $asset->asset_tag,
                            $asset->description,
                            $asset->category->name ?? null,
                            $asset->site->name ?? null,
                            $asset->location->name ?? null,
                            $auditAsset->notes,
                        ]);
                    }
                });

            fclose($handle);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

