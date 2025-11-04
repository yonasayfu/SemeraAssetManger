<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Inertia\Inertia;

class AssetsDueController extends Controller
{
    public function __invoke()
    {
        $alerts = Alert::where('type', 'Assets Due')
            ->with('asset')
            ->paginate(10);

        return Inertia::render('Alerts/AssetsDue', [
            'alerts' => $alerts,
        ]);
    }
}
