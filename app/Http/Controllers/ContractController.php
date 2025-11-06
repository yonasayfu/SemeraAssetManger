<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Contract;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContractController extends Controller
{
    public function index(Request $request): Response
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = trim((string) $request->query('search', ''));
        $vendorId = $request->integer('vendor_id');
        $productId = $request->integer('product_id');
        $usedBy = (string) $request->query('used_by', '');

        $contracts = Contract::with(['vendor:id,name', 'product:id,name', 'asset:id,asset_tag,description,staff_id'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            })
            ->when($request->filled('vendor_id'), fn($q) => $q->where('vendor_id', $vendorId))
            ->when($request->filled('product_id'), fn($q) => $q->where('product_id', $productId))
            ->when($usedBy === 'assigned', fn($q) => $q->whereHas('asset', fn($aq) => $aq->whereNotNull('staff_id')))
            ->when($usedBy === 'unassigned', function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('asset_id')
                        ->orWhereHas('asset', fn($aq) => $aq->whereNull('staff_id'));
                });
            })
            ->latest('end_at')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Contracts/Index', [
            'contracts' => $contracts,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'vendor_id' => $request->query('vendor_id'),
                'product_id' => $request->query('product_id'),
                'used_by' => $request->query('used_by'),
            ],
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
            'products' => Product::select('id','name','vendor_id')->orderBy('name')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Contracts/Create', [
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
            'products' => Product::select('id','name')->orderBy('name')->get(),
            'assets' => Asset::select('id','asset_tag','description')->latest()->take(50)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required','in:lease,maintenance,license,warranty'],
            'status' => ['nullable','string','max:50'],
            'asset_id' => ['nullable','integer','exists:assets,id'],
            'vendor_id' => ['nullable','integer','exists:vendors,id'],
            'product_id' => ['nullable','integer','exists:products,id'],
            'start_at' => ['nullable','date'],
            'end_at' => ['nullable','date'],
            'amount_minor' => ['nullable','integer','min:0'],
            'currency' => ['nullable','string','max:10'],
            'notes' => ['nullable','string'],
        ]);
        Contract::create($data);
        return redirect()->route('contracts.index')->with('bannerStyle','success')->with('banner','Contract created.');
    }

    public function edit(Contract $contract): Response
    {
        return Inertia::render('Contracts/Edit', [
            'contract' => $contract->load(['vendor:id,name','product:id,name','asset:id,asset_tag,description']),
            'vendors' => Vendor::select('id','name')->orderBy('name')->get(),
            'products' => Product::select('id','name')->orderBy('name')->get(),
            'assets' => Asset::select('id','asset_tag','description')->latest()->take(50)->get(),
        ]);
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required','in:lease,maintenance,license,warranty'],
            'status' => ['nullable','string','max:50'],
            'asset_id' => ['nullable','integer','exists:assets,id'],
            'vendor_id' => ['nullable','integer','exists:vendors,id'],
            'product_id' => ['nullable','integer','exists:products,id'],
            'start_at' => ['nullable','date'],
            'end_at' => ['nullable','date'],
            'amount_minor' => ['nullable','integer','min:0'],
            'currency' => ['nullable','string','max:10'],
            'notes' => ['nullable','string'],
        ]);
        $contract->update($data);
        return redirect()->route('contracts.index')->with('bannerStyle','success')->with('banner','Contract updated.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $contract->delete();
        return redirect()->route('contracts.index')->with('bannerStyle','info')->with('banner','Contract deleted.');
    }

    public function board(Request $request): \Inertia\Response
    {
        $contracts = Contract::with(['vendor:id,name','product:id,name','asset:id,asset_tag'])
            ->orderBy('end_at')
            ->get()
            ->map(function (Contract $c) {
                return [
                    'id' => $c->id,
                    'type' => $c->type,
                    'status' => $c->status,
                    'vendor' => optional($c->vendor)->name,
                    'product' => optional($c->product)->name,
                    'asset_tag' => optional($c->asset)->asset_tag,
                    'end_at' => optional($c->end_at)->toDateString(),
                ];
            });

        $groups = collect(['lease','maintenance','license','warranty'])
            ->map(function ($type) use ($contracts) {
                return [
                    'label' => ucfirst($type),
                    'type' => $type,
                    'items' => $contracts->where('type', $type)->values(),
                ];
            })->values();

        return Inertia::render('Contracts/Board', [
            'groups' => $groups,
        ]);
    }

    public function export(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $search = trim((string) $request->query('search', ''));
        $vendorId = $request->integer('vendor_id');
        $productId = $request->integer('product_id');
        $usedBy = (string) $request->query('used_by', '');

        $query = Contract::with(['vendor:id,name', 'product:id,name', 'asset:id,asset_tag,description,staff_id'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            })
            ->when($request->filled('vendor_id'), fn($q) => $q->where('vendor_id', $vendorId))
            ->when($request->filled('product_id'), fn($q) => $q->where('product_id', $productId))
            ->when($usedBy === 'assigned', fn($q) => $q->whereHas('asset', fn($aq) => $aq->whereNotNull('staff_id')))
            ->when($usedBy === 'unassigned', function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('asset_id')
                        ->orWhereHas('asset', fn($aq) => $aq->whereNull('staff_id'));
                });
            })
            ->orderBy('end_at');

        $filename = 'contracts-'.now()->format('Ymd_His').'.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Type','Status','Vendor','Product','Asset Tag','Start','End','AmountMinor','Currency']);
            $query->chunk(1000, function ($chunk) use ($handle) {
                foreach ($chunk as $c) {
                    fputcsv($handle, [
                        $c->type,
                        $c->status,
                        optional($c->vendor)->name,
                        optional($c->product)->name,
                        optional($c->asset)->asset_tag,
                        optional($c->start_at)->toDateString(),
                        optional($c->end_at)->toDateString(),
                        $c->amount_minor,
                        $c->currency,
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
