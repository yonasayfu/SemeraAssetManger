<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class StoreAssetDisposeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Asset $asset)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'disposal_type' => 'required|in:Sold,Donated,Lost,Broken',
        ]);

        $asset->update([
            'status' => $request->disposal_type,
            'staff_id' => null,
        ]);

        return redirect()->route('assets.show', $asset->id);
    }
}
