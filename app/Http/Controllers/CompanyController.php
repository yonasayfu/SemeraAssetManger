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
        return Inertia::render('Setup/Company/Index', [
            'company' => Company::first(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Setup/Company/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:150',
            'state' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:150',
            'timezone' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
            'date_format' => 'nullable|string|max:50',
            'financial_year_start' => 'nullable|date',
            'hr_email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
            'sidebar_logo' => 'nullable|image|max:2048',
            'brand_color' => ['nullable', 'regex:/^#(?:[A-Fa-f0-9]{3}){1,2}$/'],
            'secondary_color' => ['nullable', 'regex:/^#(?:[A-Fa-f0-9]{3}){1,2}$/'],
            'brand_logo_height' => 'nullable|integer|min:16|max:128',
            'brand_title_size' => 'nullable|integer|min:10|max:32',
            'brand_print_logo_height' => 'nullable|integer|min:16|max:256',
            'brand_logo_padding' => 'nullable|integer|min:0|max:24',
            'brand_logo_fit' => 'nullable|in:contain,cover',
            'brand_logo_scale' => 'nullable|integer|min:50|max:200',
            'brand_logo_width' => 'nullable|integer|min:0|max:256',
            'brand_logo_offset_x' => 'nullable|integer|min:-100|max:100',
            'brand_logo_offset_y' => 'nullable|integer|min:-100|max:100',
            'sidebar_logo_height' => 'nullable|integer|min:16|max:128',
            'sidebar_logo_width' => 'nullable|integer|min:0|max:256',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            $data['logo'] = $path;
        }
        if ($request->hasFile('sidebar_logo')) {
            $path = $request->file('sidebar_logo')->store('branding', 'public');
            $data['sidebar_logo'] = $path;
        }

        Company::create($data);
        return redirect()->route('setup.companies.index');
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
        return Inertia::render('Setup/Company/Edit', [
            'company' => $company,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'address_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:150',
            'state' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:150',
            'timezone' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
            'date_format' => 'nullable|string|max:50',
            'financial_year_start' => 'nullable|date',
            'hr_email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
            'sidebar_logo' => 'nullable|image|max:2048',
            'brand_color' => ['nullable', 'regex:/^#(?:[A-Fa-f0-9]{3}){1,2}$/'],
            'secondary_color' => ['nullable', 'regex:/^#(?:[A-Fa-f0-9]{3}){1,2}$/'],
            'brand_logo_height' => 'nullable|integer|min:16|max:128',
            'brand_title_size' => 'nullable|integer|min:10|max:32',
            'brand_print_logo_height' => 'nullable|integer|min:16|max:256',
            'brand_logo_padding' => 'nullable|integer|min:0|max:24',
            'brand_logo_fit' => 'nullable|in:contain,cover',
            'brand_logo_scale' => 'nullable|integer|min:50|max:200',
            'brand_logo_width' => 'nullable|integer|min:0|max:256',
            'brand_logo_offset_x' => 'nullable|integer|min:-100|max:100',
            'brand_logo_offset_y' => 'nullable|integer|min:-100|max:100',
            'sidebar_logo_height' => 'nullable|integer|min:16|max:128',
            'sidebar_logo_width' => 'nullable|integer|min:0|max:256',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            $data['logo'] = $path;
        }
        if ($request->hasFile('sidebar_logo')) {
            $path = $request->file('sidebar_logo')->store('branding', 'public');
            $data['sidebar_logo'] = $path;
        }

        $company->update($data);
        return redirect()->route('setup.companies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('setup.companies.index');
    }
}
