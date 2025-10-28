<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;

class ManageDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return Inertia::render('Setup/ManageDashboard/Index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'widgets' => ['required', 'array'],
            'layout' => ['required', 'string', 'in:2-col,3-col,4-col'],
        ]);

        $request->user()->update([
            'dashboard_preferences' => [
                'widgets' => $request->input('widgets'),
                'layout' => $request->input('layout'),
            ],
        ]);

        return redirect()->back();
    }
}
