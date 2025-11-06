<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response as HttpResponse;
use Inertia\Response;

class VendorController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = trim((string) $request->query('search', ''));

        $vendors = Vendor::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Vendors/Index', [
            'vendors' => $vendors,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Vendors/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        Vendor::create($data);

        return redirect()->route('vendors.index')->with('bannerStyle', 'success')->with('banner', 'Vendor created.');
    }

    public function edit(Vendor $vendor): Response
    {
        return Inertia::render('Vendors/Edit', [
            'vendor' => $vendor,
        ]);
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $vendor->update($data);

        return redirect()->route('vendors.index')->with('bannerStyle', 'success')->with('banner', 'Vendor updated.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return redirect()->route('vendors.index')->with('bannerStyle', 'info')->with('banner', 'Vendor deleted.');
    }

    public function export(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $query = Vendor::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name');

        $filename = 'vendors-'.now()->format('Ymd_His').'.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name','Contact','Email','Phone','Website','Notes']);
            $query->chunk(1000, function ($chunk) use ($handle) {
                foreach ($chunk as $v) {
                    fputcsv($handle, [
                        $v->name,
                        $v->contact_name,
                        $v->email,
                        $v->phone,
                        $v->website,
                        $v->notes,
                    ]);
                }
            });
            fclose($handle);
        };

        return HttpResponse::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
