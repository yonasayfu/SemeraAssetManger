<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Location;
use Inertia\Inertia;

class AssetMoveController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Asset $asset)
    {
        return Inertia::render('Assets/Move', [
            'asset' => $asset,
            'locations' => Location::all(),
        ]);
    }
}
