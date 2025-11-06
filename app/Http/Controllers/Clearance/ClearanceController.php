<?php

namespace App\Http\Controllers\Clearance;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Clearance;
use App\Models\ClearanceItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Clearance\ClearanceSubmittedNotification;

class ClearanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $items = Clearance::where('staff_id', $user->id)
            ->orderByDesc('id')
            ->get(['id','status','submitted_at','approved_at','pdf_path','created_at']);

        return Inertia::render('Clearances/Index', [
            'clearances' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $ids = collect((array) $request->input('asset_ids', []))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->values();
        $clearance = Clearance::create([
            'staff_id' => $user->id,
            'requested_by' => $user->id,
            'status' => 'draft',
        ]);

        // Prefill assets assigned to the staff
        $query = Asset::where('staff_id', $user->id);
        if ($ids->isNotEmpty()) {
            $query->whereIn('id', $ids);
        }
        $assets = $query->get(['id','asset_tag','description']);
        foreach ($assets as $asset) {
            ClearanceItem::create([
                'clearance_id' => $clearance->id,
                'asset_id' => $asset->id,
                'description' => $asset->asset_tag.' - '.$asset->description,
                'action' => 'return',
                'checked' => true,
            ]);
        }

        return redirect()->route('clearances.show', $clearance->id);
    }

    public function show(Clearance $clearance)
    {
        abort_unless($clearance->staff_id === auth()->id(), 403);

        $clearance->load(['items.asset:id,asset_tag,description']);

        return Inertia::render('Clearances/Show', [
            'clearance' => $clearance,
        ]);
    }

    public function update(Request $request, Clearance $clearance)
    {
        abort_unless($clearance->staff_id === auth()->id() && in_array($clearance->status, ['draft','submitted']), 403);

        $data = $request->validate([
            'remarks' => 'nullable|string',
            'items' => 'array',
            'items.*.id' => 'nullable|integer',
            'items.*.checked' => 'boolean',
            'items.*.action' => 'nullable|string',
            'items.*.description' => 'nullable|string',
        ]);

        $clearance->update(['remarks' => $data['remarks'] ?? $clearance->remarks]);

        foreach ($data['items'] ?? [] as $row) {
            if (isset($row['id'])) {
                $item = $clearance->items()->where('id', $row['id'])->first();
                if ($item) {
                    $item->update([
                        'checked' => (bool)($row['checked'] ?? $item->checked),
                        'action' => $row['action'] ?? $item->action,
                        'description' => $row['description'] ?? $item->description,
                    ]);
                }
            }
        }

        return back()->with('success', 'Clearance updated.');
    }

    public function submit(Clearance $clearance)
    {
        abort_unless($clearance->staff_id === auth()->id(), 403);
        $clearance->update(['status' => 'submitted', 'submitted_at' => now()]);
        // Notify approvers (users with manage permission)
        $approvers = \App\Models\Staff::permission('clearances.manage')->get();
        Notification::send($approvers, new ClearanceSubmittedNotification($clearance));
        return back()->with('success', 'Clearance submitted.');
    }

    public function pdf(Clearance $clearance)
    {
        abort_unless($clearance->staff_id === auth()->id(), 403);
        if (!$clearance->pdf_path) abort(404);
        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($clearance->pdf_path);
        return response()->download($path, "clearance_{$clearance->id}.pdf");
    }
}
