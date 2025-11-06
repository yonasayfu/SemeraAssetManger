<?php

namespace App\Http\Controllers\Clearance;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Inertia\Inertia;

class MyAssetsController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $assets = Asset::query()
            ->where('staff_id', $user->id)
            ->orderBy('asset_tag')
            ->get(['id','asset_tag','description','status','serial_no']);

        return Inertia::render('Clearances/MyAssets', [
            'assets' => $assets,
        ]);
    }
}

