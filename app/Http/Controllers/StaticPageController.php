<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SupportPage;

class StaticPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $slug = (string) ($request->route('page') ?? 'about');
        $page = SupportPage::where('slug', $slug)->first();

        return Inertia::render('StaticPage', [
            'title' => $page->title ?? ucfirst(str_replace('-', ' ', $slug)),
            'content' => $page->content ?? '<p>Content coming soon.</p>',
        ]);
    }
}
