<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreAssetCheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Asset $asset)
    {
        $request->validate([
            'assignee_id' => 'required|exists:people,id',
            'due_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Checkout::create([
            'asset_id' => $asset->id,
            'assignee_type' => 'person',
            'assignee_id' => $request->assignee_id,
            'due_at' => $request->due_at,
            'checked_out_at' => now(),
            'notes' => $request->notes,
            'status' => 'open',
        ]);

        $asset->update([
            'status' => 'Checked Out',
            'assigned_to' => $request->assignee_id,
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
