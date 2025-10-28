<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Inertia\Inertia;

class AssetCheckinController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Asset $asset)
    {
        return Inertia::render('Assets/Checkin', [
            'asset' => $asset,
        ]);
    }
}
