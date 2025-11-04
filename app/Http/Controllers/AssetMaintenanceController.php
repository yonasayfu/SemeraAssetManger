<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Asset $asset)
    {
        return Inertia::render('Assets/Maintenance/Index', [
            'asset' => $asset,
            'maintenances' => $asset->maintenances()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Asset $asset)
    {
        return Inertia::render('Assets/Maintenance/Create', [
            'asset' => $asset,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'maintenance_type' => 'required|in:Preventive,Corrective',
            'scheduled_for' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'vendor' => 'nullable|string|max:150',
        ]);

        $asset->maintenances()->create($data);

        return redirect()->route('assets.show', $asset->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
