<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Inertia\Inertia;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Site::class);
        return Inertia::render('Setup/Site/Index', [
            'sites' => Site::all(),
            'can' => [
                'create' => auth()->user()->can('create', Site::class),
                'edit' => auth()->user()->can('update', Site::class),
                'delete' => auth()->user()->can('delete', Site::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Site::class);
        return Inertia::render('Setup/Site/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Site::class);
        Site::create($request->all());

        return redirect()->route('sites.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(Site $site)
    {
        $this->authorize('update', $site);
        return Inertia::render('Setup/Site/Edit', [
            'site' => $site,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        $this->authorize('update', $site);
        $site->update($request->all());

        return redirect()->route('sites.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site)
    {
        $this->authorize('delete', $site);
        $site->delete();

        return redirect()->route('sites.index');
    }
}
