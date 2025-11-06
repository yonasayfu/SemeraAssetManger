<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\AssetDocument;
use App\Models\AssetPhoto;
use App\Models\AuditAsset;
use App\Models\Category;
use App\Models\Checkout;
use App\Models\Department;
use App\Models\Lease;
use App\Models\Location;
use App\Models\Maintenance;
use App\Models\Reservation;
use App\Models\Site;
use App\Models\Warranty;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Asset::class);
        $search = trim((string) $request->query('search', ''));
        $sort = (string) $request->query('sort', 'asset_tag');
        $direction = strtolower((string) $request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = (int) $request->query('per_page', 10);

        $assets = Asset::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('asset_tag', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('vendor_id'), fn ($q) => $q->where('vendor_id', $request->integer('vendor_id')))
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->integer('product_id')))
            ->when($request->filled('staff_id'), fn ($q) => $q->where('staff_id', $request->integer('staff_id')))
            ->when($request->query('used_by') === 'assigned', fn ($q) => $q->whereNotNull('staff_id'))
            ->when($request->query('used_by') === 'unassigned', fn ($q) => $q->whereNull('staff_id'))
            ->when(in_array($sort, ['asset_tag','description','status']), fn ($q) => $q->orderBy($sort, $direction), fn ($q) => $q->orderBy('asset_tag'))
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Assets/Index', [
            'assets' => $assets,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => $perPage,
                'vendor_id' => $request->query('vendor_id'),
                'product_id' => $request->query('product_id'),
                'staff_id' => $request->query('staff_id'),
                'used_by' => $request->query('used_by'),
            ],
            'exportColumns' => data_get(auth()->user()?->list_preferences, 'assets.export.columns', []),
            'vendors' => \App\Models\Vendor::select('id','name')->orderBy('name')->get(),
            'products' => \App\Models\Product::select('id','name','vendor_id')->orderBy('name')->get(),
            'staff' => \App\Models\Staff::select('id','name')->orderBy('name')->get(),
        ]);
    }

    public function export()
    {
        $this->authorize('viewAny', Asset::class);
        // Optional column selection via query string (?columns=Asset%20Tag%20ID,Description,...)
        $selected = request()->query('columns');
        $columns = [];
        if (is_string($selected)) {
            // Split by comma and trim spaces
            $columns = array_filter(array_map('trim', explode(',', $selected)), fn ($v) => $v !== '');
        } elseif (is_array($selected)) {
            $columns = array_values(array_filter(array_map('trim', $selected), fn ($v) => $v !== ''));
        }

        return Excel::download(new AssetsExport($columns), 'assets.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Asset::class);
        $poItems = \App\Models\PurchaseOrderItem::select('id','purchase_order_id','product_id','qty','received_qty','unit_cost_minor','currency')
            ->with([
                'purchaseOrder:id,number,vendor_id,status',
                'product:id,name,vendor_id',
            ])
            ->whereHas('purchaseOrder', fn ($q) => $q->where('status', 'open'))
            ->where(function ($q) {
                $q->whereNull('received_qty')->orWhereColumn('received_qty', '<', 'qty');
            })
            ->orderByDesc('id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'purchase_order_id' => $item->purchase_order_id,
                    'po_number' => optional($item->purchaseOrder)->number,
                    'vendor_id' => optional($item->purchaseOrder)->vendor_id,
                    'product_id' => $item->product_id,
                    'product_name' => optional($item->product)->name,
                    'qty' => $item->qty,
                    'received_qty' => $item->received_qty,
                    'unit_cost_minor' => $item->unit_cost_minor,
                    'currency' => $item->currency,
                ];
            });

        return Inertia::render('Assets/Create', [
            'sites' => Site::select('id', 'name')->orderBy('name')->get(),
            'locations' => Location::select('id', 'name', 'site_id')->orderBy('name')->get(),
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
            'staff' => Staff::select('id', 'name')->orderBy('name')->get(),
            'vendors' => \App\Models\Vendor::select('id','name')->orderBy('name')->get(),
            'products' => \App\Models\Product::select('id','name','vendor_id','warranty_months','unit_cost_minor','currency')->orderBy('name')->get(),
            'poItems' => $poItems,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Asset::class);
        $request->validate([
            'asset_tag' => 'required|string|max:50|unique:assets',
            'description' => 'required|string',
            'purchase_date' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'purchased_from' => 'nullable|string|max:150',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_no' => 'nullable|string|max:150',
            'project_code' => 'nullable|string|max:150',
            'asset_condition' => 'nullable|in:New,Good,Fair,Poor,Broken',
            'vendor_id' => 'nullable|exists:vendors,id',
            'product_id' => 'nullable|exists:products,id',
            'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'site_id' => 'nullable|exists:sites,id',
            'location_id' => 'nullable|exists:locations,id',
            'category_id' => 'nullable|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'staff_id' => 'nullable|exists:staff,id',
            'status' => 'required|in:Available,Checked Out,Under Repair,Leased,Disposed,Lost,Donated,Sold',
            'photo' => 'nullable|image|max:2048',
            'custom_fields' => 'nullable|array',
        ]);

        $asset = Asset::create(
            array_merge(
                $request->except('photo'),
                ['created_by' => Auth::id()]
            )
        );

        if ($request->hasFile('photo')) {
            $asset->photo = $request->file('photo')->store('assets', 'public');
            $asset->save();
        }

        return redirect()->route('assets.index');
    }

    public function show(Asset $asset)
    {
        $this->authorize('view', $asset);
        $asset->load([
            'site:id,name',
            'location:id,name,site_id',
            'category:id,name',
            'department:id,name',
            'assignee:id,name',
            'activeWarranty:id,asset_id,provider,expiry_date,start_date,length_months,notes,active',
        ])->loadCount([
            'photos',
            'documents',
            'warranties',
            'maintenances',
            'reservations',
            'auditAssets',
            'activityLogs',
        ]);

        $details = $this->formatDetails($asset);

        return Inertia::render('Assets/Show', [
            'asset' => [
                'id' => $asset->id,
                'asset_tag' => $asset->asset_tag,
                'description' => $asset->description,
                'status' => $asset->status,
                'photo' => $asset->photo ? Storage::disk('public')->url($asset->photo) : null,
            ],
            'details' => $details,
            'tabMeta' => [
                'photos' => $asset->photos_count,
                'documents' => $asset->documents_count,
                'warranty' => $asset->warranties_count,
                'maintenance' => $asset->maintenances_count,
                'reservations' => $asset->reservations_count,
                'audits' => $asset->audit_assets_count,
                'activity' => $asset->activity_logs_count,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);
        $poItems = \App\Models\PurchaseOrderItem::select('id','purchase_order_id','product_id','qty','received_qty','unit_cost_minor','currency')
            ->with([
                'purchaseOrder:id,number,vendor_id,status',
                'product:id,name,vendor_id',
            ])
            ->whereHas('purchaseOrder', fn ($q) => $q->where('status', 'open'))
            ->where(function ($q) {
                $q->whereNull('received_qty')->orWhereColumn('received_qty', '<', 'qty');
            })
            ->orderByDesc('id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'purchase_order_id' => $item->purchase_order_id,
                    'po_number' => optional($item->purchaseOrder)->number,
                    'vendor_id' => optional($item->purchaseOrder)->vendor_id,
                    'product_id' => $item->product_id,
                    'product_name' => optional($item->product)->name,
                    'qty' => $item->qty,
                    'received_qty' => $item->received_qty,
                    'unit_cost_minor' => $item->unit_cost_minor,
                    'currency' => $item->currency,
                ];
            });

        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
            'sites' => Site::select('id', 'name')->orderBy('name')->get(),
            'locations' => Location::select('id', 'name', 'site_id')->orderBy('name')->get(),
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
            'staff' => Staff::select('id', 'name')->orderBy('name')->get(),
            'vendors' => \App\Models\Vendor::select('id','name')->orderBy('name')->get(),
            'products' => \App\Models\Product::select('id','name','vendor_id','warranty_months','unit_cost_minor','currency')->orderBy('name')->get(),
            'poItems' => $poItems,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        $request->validate([
            'asset_tag' => "required|string|max:50|unique:assets,asset_tag,{$asset->id}",
            'description' => 'required|string',
            'purchase_date' => 'nullable|date',
            'cost' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'purchased_from' => 'nullable|string|max:150',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_no' => 'nullable|string|max:150',
            'project_code' => 'nullable|string|max:150',
            'asset_condition' => 'nullable|in:New,Good,Fair,Poor,Broken',
            'vendor_id' => 'nullable|exists:vendors,id',
            'product_id' => 'nullable|exists:products,id',
            'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'site_id' => 'nullable|exists:sites,id',
            'location_id' => 'nullable|exists:locations,id',
            'category_id' => 'nullable|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'staff_id' => 'nullable|exists:staff,id',
            'status' => 'required|in:Available,Checked Out,Under Repair,Leased,Disposed,Lost,Donated,Sold',
            'photo' => 'nullable|image|max:2048',
            'custom_fields' => 'nullable|array',
        ]);

        $asset->update($request->except('photo'));

        if ($request->hasFile('photo')) {
            if ($asset->photo) {
                Storage::disk('public')->delete($asset->photo);
            }

            $asset->photo = $request->file('photo')->store('assets', 'public');
            $asset->save();
        }

        return redirect()->route('assets.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);
        if ($asset->photo) {
            Storage::disk('public')->delete($asset->photo);
        }

        $asset->delete();

        return redirect()->route('assets.index');
    }

    public function details(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        return response()->json($this->formatDetails($asset->fresh()));
    }

    public function history(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        return response()->json([
            'events' => $this->buildHistory($asset),
        ]);
    }

    public function photos(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $photos = $asset->photos()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (AssetPhoto $photo) => [
                'id' => $photo->id,
                'caption' => $photo->caption,
                'url' => $photo->path ? Storage::disk('public')->url($photo->path) : null,
                'uploaded_at' => optional($photo->created_at)->toIso8601String(),
            ]);

        return response()->json([
            'photos' => $photos,
        ]);
    }

    public function documents(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $documents = $asset->documents()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (AssetDocument $document) => [
                'id' => $document->id,
                'title' => $document->title,
                'mime_type' => $document->mime_type,
                'url' => $document->file_path ? Storage::disk('public')->url($document->file_path) : null,
                'uploaded_at' => optional($document->created_at)->toIso8601String(),
            ]);

        return response()->json([
            'documents' => $documents,
        ]);
    }

    public function warranty(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $warranties = $asset->warranties()
            ->orderByDesc('expiry_date')
            ->get()
            ->map(fn (Warranty $warranty) => [
                'id' => $warranty->id,
                'provider' => $warranty->provider,
                'description' => $warranty->description,
                'length_months' => $warranty->length_months,
                'start_date' => optional($warranty->start_date)->toDateString(),
                'expiry_date' => optional($warranty->expiry_date)->toDateString(),
                'active' => (bool) $warranty->active,
                'notes' => $warranty->notes,
            ]);

        return response()->json([
            'warranties' => $warranties,
        ]);
    }

    public function maintenance(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $maintenances = $asset->maintenances()
            ->orderByDesc('scheduled_for')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Maintenance $maintenance) => [
                'id' => $maintenance->id,
                'title' => $maintenance->title,
                'status' => $maintenance->status,
                'maintenance_type' => $maintenance->maintenance_type,
                'scheduled_for' => optional($maintenance->scheduled_for)->toIso8601String(),
                'completed_at' => optional($maintenance->completed_at)->toIso8601String(),
                'cost' => $maintenance->cost,
                'vendor' => $maintenance->vendor,
            ]);

        return response()->json([
            'maintenances' => $maintenances,
        ]);
    }

    public function reservations(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $reservations = $asset->reservations()
            ->with(['requester:id,name'])
            ->orderByDesc('start_at')
            ->get()
            ->map(fn (Reservation $reservation) => [
                'id' => $reservation->id,
                'status' => $reservation->status,
                'start_at' => optional($reservation->start_at)->toIso8601String(),
                'end_at' => optional($reservation->end_at)->toIso8601String(),
                'requester' => $reservation->requester?->name,
            ]);

        return response()->json([
            'reservations' => $reservations,
        ]);
    }

    public function audits(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $audits = $asset->auditAssets()
            ->with([
                'audit:id,name,status,site_id,location_id,started_at,completed_at',
                'audit.site:id,name',
                'audit.location:id,name',
            ])
            ->orderByDesc('checked_at')
            ->get()
            ->map(fn (AuditAsset $auditAsset) => [
                'id' => $auditAsset->id,
                'found' => (bool) $auditAsset->found,
                'notes' => $auditAsset->notes,
                'checked_at' => optional($auditAsset->checked_at)->toIso8601String(),
                'audit' => [
                    'id' => $auditAsset->audit?->id,
                    'name' => $auditAsset->audit?->name,
                    'status' => $auditAsset->audit?->status,
                    'site' => $auditAsset->audit?->site?->name,
                    'location' => $auditAsset->audit?->location?->name,
                    'started_at' => optional($auditAsset->audit?->started_at)->toIso8601String(),
                    'completed_at' => optional($auditAsset->audit?->completed_at)->toIso8601String(),
                ],
            ]);

        return response()->json([
            'audits' => $audits,
        ]);
    }

    public function activity(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);
        $activity = ActivityLog::query()
            ->where('subject_type', Asset::class)
            ->where('subject_id', $asset->id)
            ->with(['causer:id,name,email'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (ActivityLog $log) => [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'created_at' => optional($log->created_at)->toIso8601String(),
                'causer' => $log->causer ? [
                    'id' => $log->causer->id,
                    'name' => $log->causer->name,
                    'email' => $log->causer->email,
                ] : null,
            ]);

        return response()->json([
            'activity' => $activity,
        ]);
    }

    protected function formatDetails(Asset $asset): array
    {
        $asset = $asset->fresh([
            'site:id,name',
            'location:id,name',
            'category:id,name',
            'department:id,name',
            'assignee:id,name',
            'activeWarranty:id,asset_id,provider,expiry_date,start_date,length_months,notes,active',
        ]);

        $recentWarranties = $asset->warranties()
            ->orderByDesc('expiry_date')
            ->limit(3)
            ->get()
            ->map(fn (Warranty $warranty) => [
                'id' => $warranty->id,
                'provider' => $warranty->provider,
                'expiry_date' => optional($warranty->expiry_date)->toDateString(),
                'is_active' => (bool) $warranty->active,
            ]);

        $upcomingMaintenance = $asset->maintenances()
            ->whereNull('completed_at')
            ->orderBy('scheduled_for')
            ->first();

        $upcomingReservation = $asset->reservations()
            ->where('status', 'approved')
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->with('requester:id,name')
            ->first();

        $stats = [
            [
                'label' => 'Open Maintenance',
                'value' => $asset->maintenances()->whereIn('status', ['Open', 'Scheduled', 'In Progress', 'Overdue'])->count(),
                'tone' => 'warning',
            ],
            [
                'label' => 'Completed Maintenance',
                'value' => $asset->maintenances()->where('status', 'Completed')->count(),
                'tone' => 'success',
            ],
            [
                'label' => 'Total Reservations',
                'value' => $asset->reservations()->count(),
                'tone' => 'primary',
            ],
        ];

        return [
            'asset' => [
                'asset_tag' => $asset->asset_tag,
                'description' => $asset->description,
                'status' => $asset->status,
                'purchase_date' => optional($asset->purchase_date)->toDateString(),
                'cost' => $asset->cost,
                'currency' => $asset->currency,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'serial_no' => $asset->serial_no,
                'project_code' => $asset->project_code,
                'asset_condition' => $asset->asset_condition,
                'site' => $asset->site?->name,
                'location' => $asset->location?->name,
                'category' => $asset->category?->name,
                'department' => $asset->department?->name,
                'assignee' => $asset->assignee?->name,
                'active_warranty' => $asset->activeWarranty ? [
                    'provider' => $asset->activeWarranty->provider,
                    'expiry_date' => optional($asset->activeWarranty->expiry_date)->toDateString(),
                ] : null,
            ],
            'stats' => $stats,
            'warranties' => $recentWarranties,
            'upcoming_maintenance' => $upcomingMaintenance ? [
                'id' => $upcomingMaintenance->id,
                'title' => $upcomingMaintenance->title,
                'scheduled_for' => optional($upcomingMaintenance->scheduled_for)->toIso8601String(),
                'status' => $upcomingMaintenance->status,
            ] : null,
            'upcoming_reservation' => $upcomingReservation ? [
                'id' => $upcomingReservation->id,
                'start_at' => optional($upcomingReservation->start_at)->toIso8601String(),
                'end_at' => optional($upcomingReservation->end_at)->toIso8601String(),
                'requester' => $upcomingReservation->requester?->name,
                'status' => $upcomingReservation->status,
            ] : null,
        ];
    }

    protected function buildHistory(Asset $asset): array
    {
        $events = Collection::make();

        $asset->checkouts()
            ->orderByDesc('checked_out_at')
            ->limit(25)
            ->get()
            ->each(function (Checkout $checkout) use (&$events) {
                $events->push([
                    'type' => 'checkout',
                    'title' => 'Asset checked out',
                    'status' => $checkout->status,
                    'occurred_at' => optional($checkout->checked_out_at)->toIso8601String(),
                    'meta' => [
                        'due_at' => optional($checkout->due_at)->toIso8601String(),
                        'checked_in_at' => optional($checkout->checked_in_at)->toIso8601String(),
                        'assignee_type' => $checkout->assignee_type,
                        'assignee_id' => $checkout->assignee_id,
                    ],
                ]);
            });

        $asset->leases()
            ->orderByDesc('start_at')
            ->limit(25)
            ->get()
            ->each(function (Lease $lease) use (&$events) {
                $events->push([
                    'type' => 'lease',
                    'title' => 'Lease scheduled',
                    'status' => $lease->status,
                    'occurred_at' => optional($lease->start_at)->toIso8601String(),
                    'meta' => [
                        'end_at' => optional($lease->end_at)->toIso8601String(),
                        'lessee_type' => $lease->lessee_type,
                        'lessee_id' => $lease->lessee_id,
                    ],
                ]);
            });

        $asset->maintenances()
            ->orderByDesc('scheduled_for')
            ->limit(25)
            ->get()
            ->each(function (Maintenance $maintenance) use (&$events) {
                $events->push([
                    'type' => 'maintenance',
                    'title' => $maintenance->title,
                    'status' => $maintenance->status,
                    'occurred_at' => optional($maintenance->scheduled_for)->toIso8601String(),
                    'meta' => [
                        'completed_at' => optional($maintenance->completed_at)->toIso8601String(),
                        'maintenance_type' => $maintenance->maintenance_type,
                    ],
                ]);
            });

        $asset->reservations()
            ->orderByDesc('start_at')
            ->limit(25)
            ->get()
            ->each(function (Reservation $reservation) use (&$events) {
                $events->push([
                    'type' => 'reservation',
                    'title' => 'Reservation created',
                    'status' => $reservation->status,
                    'occurred_at' => optional($reservation->start_at)->toIso8601String(),
                    'meta' => [
                        'end_at' => optional($reservation->end_at)->toIso8601String(),
                        'requester_id' => $reservation->requester_id,
                    ],
                ]);
            });

        return $events
            ->filter(fn (array $event) => $event['occurred_at'])
            ->sortByDesc('occurred_at')
            ->values()
            ->all();
    }
}
