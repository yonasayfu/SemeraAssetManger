<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = trim((string) $request->query('search', ''));
        $vendorId = $request->integer('vendor_id');
        $productId = $request->integer('product_id');

        $pos = PurchaseOrder::with('vendor:id,name')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            })
            ->when($request->filled('vendor_id'), fn($q) => $q->where('vendor_id', $vendorId))
            ->when($request->filled('product_id'), function ($q) use ($productId) {
                $q->whereHas('items', fn($iq) => $iq->where('product_id', $productId));
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('PurchaseOrders/Index', [
            'purchaseOrders' => $pos,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'vendor_id' => $request->query('vendor_id'),
                'product_id' => $request->query('product_id'),
            ],
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
            'products' => \App\Models\Product::select('id','name','vendor_id')->orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PurchaseOrders/Create', [
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required','string','max:255','unique:purchase_orders,number'],
            'name' => ['required','string','max:255'],
            'vendor_id' => ['nullable','integer','exists:vendors,id'],
            'expected_delivery_at' => ['nullable','date'],
            'status' => ['nullable','string','max:50'],
            'currency' => ['nullable','string','max:10'],
            'notes' => ['nullable','string'],
        ]);
        $data['status'] = $data['status'] ?? 'open';
        PurchaseOrder::create($data);
        return redirect()->route('purchase-orders.index')->with('bannerStyle','success')->with('banner','Purchase order created.');
    }

    public function edit(PurchaseOrder $purchase_order): Response
    {
        return Inertia::render('PurchaseOrders/Edit', [
            'po' => $purchase_order->load(['vendor:id,name','items' => function($q){ $q->with('product:id,name'); }]),
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchase_order): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'vendor_id' => ['nullable','integer','exists:vendors,id'],
            'expected_delivery_at' => ['nullable','date'],
            'status' => ['nullable','in:open,received,cancelled'],
            'currency' => ['nullable','string','max:10'],
            'notes' => ['nullable','string'],
        ]);
        $purchase_order->update($data);
        return redirect()->route('purchase-orders.index')->with('bannerStyle','success')->with('banner','Purchase order updated.');
    }

    public function destroy(PurchaseOrder $purchase_order): RedirectResponse
    {
        $purchase_order->delete();
        return redirect()->route('purchase-orders.index')->with('bannerStyle','info')->with('banner','Purchase order deleted.');
    }

    public function receive(Request $request, PurchaseOrder $purchase_order): RedirectResponse
    {
        // Simple receive all items
        foreach ($purchase_order->items as $item) {
            $item->received_qty = $item->qty;
            $item->save();
        }
        $purchase_order->status = 'received';
        $purchase_order->save();
        return redirect()->route('purchase-orders.edit', $purchase_order)->with('bannerStyle','success')->with('banner','All items received.');
    }

    public function export(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = trim((string) $request->query('search', ''));
        $vendorId = $request->integer('vendor_id');
        $productId = $request->integer('product_id');

        $query = PurchaseOrder::with('vendor:id,name')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            })
            ->when($request->filled('vendor_id'), fn($q) => $q->where('vendor_id', $vendorId))
            ->when($request->filled('product_id'), function ($q) use ($productId) {
                $q->whereHas('items', fn($iq) => $iq->where('product_id', $productId));
            })
            ->orderByDesc('id');

        $filename = 'purchase-orders-'.now()->format('Ymd_His').'.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Number','Name','Vendor','Expected','Status','TotalMinor','Currency']);
            $query->chunk(1000, function ($chunk) use ($handle) {
                foreach ($chunk as $po) {
                    fputcsv($handle, [
                        $po->number,
                        $po->name,
                        optional($po->vendor)->name,
                        optional($po->expected_delivery_at)->toDateString(),
                        $po->status,
                        $po->total_minor,
                        $po->currency,
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
