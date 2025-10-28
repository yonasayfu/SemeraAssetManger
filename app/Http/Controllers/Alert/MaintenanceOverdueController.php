<?php

namespace App\Http\Controllers\Alert;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Inertia\Inertia;

class MaintenanceOverdueController extends Controller
{
    public function __invoke()
    {
        $alerts = Alert::where('type', 'Maintenance Overdue')
            ->with('asset')
            ->paginate(10);

        return Inertia::render('Alerts/MaintenanceOverdue', [
            'alerts' => $alerts,
        ]);
    }
}
