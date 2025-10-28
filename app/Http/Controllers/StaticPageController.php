<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $title = $request->route('page') ?? 'Static Page';
        return Inertia::render('StaticPage', [
            'title' => $title,
        ]);
    }
}
