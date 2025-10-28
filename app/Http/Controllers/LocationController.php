<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Inertia\Inertia;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Location::class);
        return Inertia::render('Setup/Location/Index', [
            'locations' => Location::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Location::class);
        return Inertia::render('Setup/Location/Create', [
            'sites' => Site::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Location::class);
        Location::create($request->all());

        return redirect()->route('locations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(Location $location)
    {
        $this->authorize('update', $location);
        return Inertia::render('Setup/Location/Edit', [
            'location' => $location,
            'sites' => Site::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $this->authorize('update', $location);
        $location->update($request->all());

        return redirect()->route('locations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);
        $location->delete();

        return redirect()->route('locations.index');
    }
}
