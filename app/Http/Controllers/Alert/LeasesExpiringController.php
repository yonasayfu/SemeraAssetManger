<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Inertia\Inertia;

class LeasesExpiringController extends Controller
{
    public function __invoke()
    {
        $alerts = Alert::where('type', 'Lease Expiring')
            ->with('asset')
            ->paginate(10);

        return Inertia::render('Alerts/LeasesExpiring', [
            'alerts' => $alerts,
        ]);
    }
}
