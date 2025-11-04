<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Staff;
use Inertia\Inertia;

class AssetCheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Asset $asset)
    {
        return Inertia::render('Assets/Checkout', [
            'asset' => $asset,
            'staff' => Staff::select('id','name')->orderBy('name')->get(),
        ]);
    }
}
