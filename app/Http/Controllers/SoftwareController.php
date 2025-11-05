<?php

namespace App\Http\Controllers;

use App\Models\Software;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SoftwareController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = trim((string) $request->query('search', ''));

        $software = Software::with('vendor:id,name')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Software/Index', [
            'software' => $software,
            'filters' => [ 'search' => $search, 'per_page' => $perPage ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Software/Create', [
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vendor_id' => ['nullable','integer','exists:vendors,id'],
            'name' => ['required','string','max:255'],
            'type' => ['required','in:saas,on-prem'],
            'seats_total' => ['nullable','integer','min:0'],
            'seats_used' => ['nullable','integer','min:0'],
            'status' => ['nullable','string','max:50'],
            'notes' => ['nullable','string'],
        ]);
        $data['status'] = $data['status'] ?? 'active';
        Software::create($data);
        return redirect()->route('software.index')->with('bannerStyle','success')->with('banner','Software created.');
    }

    public function edit(Software $software): Response
    {
        return Inertia::render('Software/Edit', [
            'software' => $software->load('vendor:id,name'),
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Software $software): RedirectResponse
    {
        $data = $request->validate([
            'vendor_id' => ['nullable','integer','exists:vendors,id'],
            'name' => ['required','string','max:255'],
            'type' => ['required','in:saas,on-prem'],
            'seats_total' => ['nullable','integer','min:0'],
            'seats_used' => ['nullable','integer','min:0'],
            'status' => ['nullable','string','max:50'],
            'notes' => ['nullable','string'],
        ]);
        $software->update($data);
        return redirect()->route('software.index')->with('bannerStyle','success')->with('banner','Software updated.');
    }

    public function destroy(Software $software): RedirectResponse
    {
        $software->delete();
        return redirect()->route('software.index')->with('bannerStyle','info')->with('banner','Software deleted.');
    }
}
