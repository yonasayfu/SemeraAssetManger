<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('manage-companies');
        return Inertia::render('Setup/Company/Index', [
            'company' => Company::first(),
        ]);
    }

    public function create()
    {
        $this->authorize('manage-companies');
        return Inertia::render('Setup/Company/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-companies');
        Company::create($request->all());

        return redirect()->route('companies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(Company $company)
    {
        $this->authorize('manage-companies');
        return Inertia::render('Setup/Company/Edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $this->authorize('manage-companies');
        $company->update($request->all());

        return redirect()->route('companies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $this->authorize('manage-companies');
        $company->delete();

        return redirect()->route('companies.index');
    }
}
