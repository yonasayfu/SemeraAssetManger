<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Move;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreAssetMoveController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'to_location_id' => 'required|exists:locations,id',
            'reason' => 'nullable|string',
        ]);

        // Prevent moving to the same location
        if ((int) $asset->location_id === (int) $data['to_location_id']) {
            return back()->withErrors(['to_location_id' => 'Destination location must be different from current location.']);
        }

        Move::create([
            'asset_id' => $asset->id,
            'from_location_id' => $asset->location_id,
            'to_location_id' => $data['to_location_id'],
            'moved_by' => Auth::id(),
            'moved_at' => now(),
            'reason' => $data['reason'] ?? null,
        ]);

        $asset->update([
            'location_id' => $data['to_location_id'],
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
