<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $this->authorize('viewAny', \App\Models\Report::class);
        return Inertia::render('Reports/Index');
    }
}
