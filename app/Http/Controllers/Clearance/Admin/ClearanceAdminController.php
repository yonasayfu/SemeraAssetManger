<?php

namespace App\Http\Controllers\Clearance\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clearance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\Company;
use App\Notifications\Clearance\ClearanceApprovedNotification;
use App\Notifications\Clearance\ClearanceRejectedNotification;

class ClearanceAdminController extends Controller
{
    public function index()
    {
        $items = Clearance::with('staff:id,name')
            ->orderByDesc('id')
            ->get(['id','staff_id','status','submitted_at','approved_at','created_at']);

        return Inertia::render('Clearances/AdminIndex', [
            'clearances' => $items,
        ]);
    }

    public function show(Clearance $clearance)
    {
        $clearance->load(['staff:id,name','items.asset:id,asset_tag,description']);
        return Inertia::render('Clearances/AdminShow', [
            'clearance' => $clearance,
        ]);
    }

    public function update(Request $request, Clearance $clearance)
    {
        $data = $request->validate([
            'remarks' => 'nullable|string',
            'items' => 'array',
            'items.*.id' => 'required|integer',
            'items.*.action' => 'nullable|string',
            'items.*.result' => 'nullable|string',
            'items.*.condition_note' => 'nullable|string',
        ]);

        $clearance->update(['remarks' => $data['remarks'] ?? $clearance->remarks, 'status' => $clearance->status === 'submitted' ? 'in_review' : $clearance->status]);

        foreach ($data['items'] as $row) {
            $item = $clearance->items()->where('id', $row['id'])->first();
            if ($item) {
                $item->update([
                    'action' => $row['action'] ?? $item->action,
                    'result' => $row['result'] ?? $item->result,
                    'condition_note' => $row['condition_note'] ?? $item->condition_note,
                ]);
            }
        }

        return back()->with('success', 'Clearance updated.');
    }

    public function approve(Request $request, Clearance $clearance)
    {
        $clearance->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ]);

        // Generate PDF
        $clearance->load(['staff','items.asset']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clearances.pdf', [ 'clearance' => $clearance, 'company' => Company::first() ]);
        $filename = 'clearances/'.$clearance->id.'_'.now()->format('Ymd_His').'.pdf';
        Storage::disk('local')->put($filename, $pdf->output());
        $clearance->update(['pdf_path' => $filename]);

        // Optional auto-checkin (default true)
        $auto = $request->boolean('auto_checkin', true);
        if ($auto) {
            foreach ($clearance->items as $item) {
                if ($item->asset && ($item->action === 'return' || $item->action === null)) {
                    $item->asset->update(['staff_id' => null, 'status' => 'Available']);
                }
            }
        }

        // Notify staff and HR (fallback to Company hr_email)
        optional($clearance->staff)->notify(new ClearanceApprovedNotification($clearance));
        $hr = $clearance->hr_email ?: optional(Company::first())->hr_email;
        if ($hr) {
            Notification::route('mail', $hr)->notify(new ClearanceApprovedNotification($clearance));
        }

        return back()->with('success', 'Clearance approved and PDF generated.');
    }

    public function reject(Clearance $clearance)
    {
        $clearance->update(['status' => 'rejected']);

        // Mark all items as rejected (not received)
        $clearance->load(['staff','items.asset']);
        foreach ($clearance->items as $item) {
            $item->update(['result' => 'rejected']);
        }

        // Generate PDF for rejected status too
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('clearances.pdf', [
            'clearance' => $clearance,
            'company' => Company::first(),
        ]);
        $filename = 'clearances/'.$clearance->id.'_'.now()->format('Ymd_His').'.pdf';
        Storage::disk('local')->put($filename, $pdf->output());
        $clearance->update(['pdf_path' => $filename]);

        optional($clearance->staff)->notify(new ClearanceRejectedNotification($clearance));
        return back()->with('success', 'Clearance rejected.');
    }

    public function pdf(Clearance $clearance)
    {
        if (!$clearance->pdf_path) {
            abort(404, 'PDF not generated.');
        }
        $path = Storage::disk('local')->path($clearance->pdf_path);
        return response()->download($path, 'clearance_'.$clearance->id.'.pdf');
    }
}
