<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Person;
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
            'people' => Person::all(),
        ]);
    }
}
