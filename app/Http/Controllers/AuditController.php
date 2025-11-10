<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $allowed = ['Draft', 'Ongoing', 'Completed'];

        $audits = Audit::query()
            ->with(['site:id,name', 'location:id,name,site_id'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when(in_array($status, $allowed, true), function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->get();

        return Inertia::render('Tools/Audits/Index', [
            'audits' => $audits,
            'filters' => [
                'search' => $search,
                'status' => in_array($status, $allowed, true) ? $status : null,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
