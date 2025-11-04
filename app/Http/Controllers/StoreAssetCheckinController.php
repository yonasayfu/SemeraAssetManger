<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Checkout;
use Illuminate\Http\Request;

class StoreAssetCheckinController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Asset $asset)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $checkout = Checkout::where('asset_id', $asset->id)
            ->where('status', 'open')
            ->latest()
            ->firstOrFail();

        $checkout->update([
            'checked_in_at' => now(),
            'notes' => $request->notes,
            'status' => 'closed',
        ]);

        $asset->update([
            'status' => 'Available',
            'staff_id' => null,
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
