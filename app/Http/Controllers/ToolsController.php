<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ToolsController extends Controller
{
    public function import()
    {
        return Inertia::render('Tools/Import');
    }

    public function export()
    {
        return Inertia::render('Tools/Export');
    }
}
