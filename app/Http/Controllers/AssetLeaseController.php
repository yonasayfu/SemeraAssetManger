<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Person;
use App\Models\Department;
use Inertia\Inertia;

class AssetLeaseController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Asset $asset)
    {
        return Inertia::render('Assets/Lease', [
            'asset' => $asset,
            'people' => Person::all(),
            'departments' => Department::all(),
        ]);
    }
}
