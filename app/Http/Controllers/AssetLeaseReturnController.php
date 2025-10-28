<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Inertia\Inertia;

class AssetLeaseReturnController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Asset $asset)
    {
        return Inertia::render('Assets/LeaseReturn', [
            'asset' => $asset,
        ]);
    }
}
