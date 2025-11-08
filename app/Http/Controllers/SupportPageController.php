<?php

namespace App\Http\Controllers;

use App\Models\SupportPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportPageController extends Controller
{
    public function index()
    {
        $pages = SupportPage::orderBy('slug')->get(['id','slug','title','published','updated_at']);
        return Inertia::render('Help/Pages/Index', [
            'pages' => $pages,
        ]);
    }

    public function create()
    {
        return Inertia::render('Help/Pages/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|max:150|alpha_dash|unique:support_pages,slug',
            'title' => 'required|string|max:200',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'published' => 'nullable|boolean',
        ]);
        $data['published'] = (bool)($data['published'] ?? true);
        SupportPage::create($data);
        return redirect()->route('help.pages.index');
    }

    public function edit(SupportPage $page)
    {
        return Inertia::render('Help/Pages/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, SupportPage $page)
    {
        $data = $request->validate([
            'slug' => 'required|string|max:150|alpha_dash|unique:support_pages,slug,'.$page->id,
            'title' => 'required|string|max:200',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'published' => 'nullable|boolean',
        ]);
        $data['published'] = (bool)($data['published'] ?? $page->published);
        $page->update($data);
        return redirect()->route('help.pages.index');
    }

    public function destroy(SupportPage $page)
    {
        $page->delete();
        return redirect()->route('help.pages.index');
    }
}

