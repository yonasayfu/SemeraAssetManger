<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Lease;
use Illuminate\Http\Request;

class StoreAssetLeaseController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Asset $asset)
    {
        $request->validate([
            'lessee_id' => 'required',
            'lessee_type' => 'required|in:person,department',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'rate_minor' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'terms' => 'nullable|string',
        ]);

        Lease::create([
            'asset_id' => $asset->id,
            'lessee_type' => $request->lessee_type,
            'lessee_id' => $request->lessee_id,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'rate_minor' => $request->rate_minor,
            'currency' => $request->currency,
            'terms' => $request->terms,
            'status' => 'active',
        ]);

        $asset->update([
            'status' => 'Leased',
            'assigned_to' => $request->lessee_id,
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
