<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response as HttpResponse;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = trim((string) $request->query('search', ''));

        $products = Product::query()
            ->with('vendor')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Products/Create', [
            'vendors' => Vendor::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'warranty_months' => ['nullable', 'integer', 'min:0'],
            'unit_cost_minor' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);

        Product::create($data);

        return redirect()->route('products.index')->with('bannerStyle', 'success')->with('banner', 'Product created.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Edit', [
            'product' => $product->load('vendor:id,name'),
            'vendors' => Vendor::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'warranty_months' => ['nullable', 'integer', 'min:0'],
            'unit_cost_minor' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('bannerStyle', 'success')->with('banner', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('products.index')->with('bannerStyle', 'info')->with('banner', 'Product deleted.');
    }

    public function export(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10); // unused but kept for consistency
        $search = trim((string) $request->query('search', ''));
        $vendorId = $request->integer('vendor_id');

        $query = Product::query()
            ->with('vendor')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            })
            ->when($request->filled('vendor_id'), fn($q) => $q->where('vendor_id', $vendorId))
            ->orderBy('name');

        $filename = 'products-'.now()->format('Ymd_His').'.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name','Vendor','SKU','WarrantyMonths','UnitCost','Currency','Notes']);
            $query->chunk(1000, function ($chunk) use ($handle) {
                foreach ($chunk as $p) {
                    fputcsv($handle, [
                        $p->name,
                        optional($p->vendor)->name,
                        $p->sku,
                        $p->warranty_months,
                        $p->unit_cost_minor,
                        $p->currency,
                        $p->notes,
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
