<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Inertia\Inertia;

class AlertController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return Inertia::render('Alerts/Index', [
            'alerts' => Alert::with('asset')->latest()->get(),
        ]);
    }
}
