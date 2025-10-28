<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditScanUpdateRequest;
use App\Models\Audit;
use App\Models\AuditAsset;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditScanController extends Controller
{
    public function __construct(protected AuditService $auditService)
    {
    }

    public function show(Audit $audit)
    {
        $this->authorize('view', $audit);
        return Inertia::render('Audits/Scan', [
            'audit' => $audit,
        ]);
    }

    public function update(AuditScanUpdateRequest $request, Audit $audit, AuditAsset $auditAsset)
    {
        $this->authorize('update', $audit);
        $this->auditService->updateAuditAsset($auditAsset, $request->validated());

        return response()->json(['success' => true]);
    }

    public function complete(Request $request, Audit $audit)
    {
        $this->authorize('update', $audit);
        $this->auditService->complete($audit);

        return redirect()->route('audits.report', ['audit' => $audit->id]);
    }

    public function search(Request $request, Audit $audit)
    {
        $this->authorize('view', $audit);
        $query = $request->input('query');

        $assets = $audit->auditAssets()->whereHas('asset', function ($q) use ($query) {
            $q->where('asset_tag', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        })->with('asset')->get();

        return response()->json($assets);
    }
}