<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreAssetReserveController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Asset $asset)
    {
        $request->validate([
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
        ]);

        Reservation::create([
            'asset_id' => $asset->id,
            'requester_id' => Auth::id(),
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'status' => 'pending',
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
