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
        $data = $request->validate([
            'lessee_id' => 'required',
            'lessee_type' => 'required|in:staff,department',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'rate_minor' => 'required|numeric|min:0',
            'currency' => 'required|string|max:10',
            'terms' => 'nullable|string',
        ]);

        // Conditional existence check for lessee_id
        if ($data['lessee_type'] === 'staff') {
            $request->validate(['lessee_id' => 'exists:staff,id']);
        } else {
            $request->validate(['lessee_id' => 'exists:departments,id']);
        }

        Lease::create([
            'asset_id' => $asset->id,
            'lessee_type' => $data['lessee_type'],
            'lessee_id' => $data['lessee_id'],
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'rate_minor' => $data['rate_minor'],
            'currency' => $data['currency'],
            'terms' => $data['terms'] ?? null,
            'status' => 'active',
        ]);

        $asset->update([
            'status' => 'Leased',
            // Only set staff_id when lessee is a staff; otherwise null
            'staff_id' => $data['lessee_type'] === 'staff' ? $data['lessee_id'] : null,
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
