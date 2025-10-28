<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Inertia\Inertia;

class MaintenanceDueController extends Controller
{
    public function __invoke()
    {
        $alerts = Alert::where('type', 'Maintenance Due')
            ->with('asset')
            ->paginate(10);

        return Inertia::render('Alerts/MaintenanceDue', [
            'alerts' => $alerts,
        ]);
    }
}
