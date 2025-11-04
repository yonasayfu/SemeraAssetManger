<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Lease;
use Illuminate\Http\Request;

class StoreAssetLeaseReturnController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Asset $asset)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $lease = Lease::where('asset_id', $asset->id)
            ->where('status', 'active')
            ->latest()
            ->firstOrFail();

        $lease->update([
            'status' => 'returned',
        ]);

        $asset->update([
            'status' => 'Available',
            'staff_id' => null,
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
