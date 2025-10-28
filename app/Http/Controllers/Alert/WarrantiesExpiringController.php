<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Inertia\Inertia;

class WarrantiesExpiringController extends Controller
{
    public function __invoke()
    {
        $alerts = Alert::where('type', 'Expiring Warranty')
            ->with('asset')
            ->paginate(10);

        return Inertia::render('Alerts/WarrantiesExpiring', [
            'alerts' => $alerts,
        ]);
    }
}
