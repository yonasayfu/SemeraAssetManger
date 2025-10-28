<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Warranty;
use App\Services\WarrantyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class WarrantyController extends Controller
{
    public function __construct(
        protected WarrantyService $warrantyService
    ) {
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Warranty::class);
        $filters = [
            'search' => $request->string('search')->toString(),
            'is_active' => $request->boolean('is_active', null),
            'provider' => $request->string('provider')->toString(),
            'asset' => $request->integer('asset'),
        ];

        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $warranties = Warranty::query()
            ->with([
                'asset:id,asset_tag,description',
                'asset.site:id,name',
                'asset.location:id,name',
            ])
            ->when($filters['search'], function ($builder, $search) {
                $builder->where(function ($inner) use ($search) {
                    $inner->where('provider', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('asset', fn ($assetQuery) => $assetQuery->where('asset_tag', 'like', "%{$search}%"));
                });
            })
            ->when(! is_null($filters['is_active']), fn ($builder) => $builder->where('active', $filters['is_active']))
            ->when($filters['provider'], fn ($builder, $provider) => $builder->where('provider', 'like', "%{$provider}%"))
            ->when($filters['asset'], fn ($builder, $assetId) => $builder->where('asset_id', $assetId))
            ->orderByDesc('expiry_date')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Warranty $warranty) {
                return [
                    'id' => $warranty->id,
                    'provider' => $warranty->provider,
                    'description' => $warranty->description,
                    'length_months' => $warranty->length_months,
                    'start_date' => optional($warranty->start_date)->toDateString(),
                    'expiry_date' => optional($warranty->expiry_date)->toDateString(),
                    'active' => (bool) $warranty->active,
                    'asset' => $warranty->asset ? [
                        'id' => $warranty->asset->id,
                        'asset_tag' => $warranty->asset->asset_tag,
                        'description' => $warranty->asset->description,
                        'site' => $warranty->asset->site?->name,
                        'location' => $warranty->asset->location?->name,
                    ] : null,
                ];
            });

        $stats = [
            [
                'label' => 'Active',
                'value' => Warranty::where('active', true)->count(),
                'tone' => 'success',
            ],
            [
                'label' => 'Expired',
                'value' => Warranty::where('active', false)->count(),
                'tone' => 'warning',
            ],
        ];

        return Inertia::render('Warranty/Index', [
            'warranties' => $warranties,
            'filters' => array_merge($filters, ['per_page' => $perPage]),
            'stats' => $stats,
            'assetOptions' => Asset::select('id', 'asset_tag')->orderBy('asset_tag')->get(),
            'activeOptions' => [
                ['label' => 'Any', 'value' => null],
                ['label' => 'Active', 'value' => true],
                ['label' => 'Expired', 'value' => false],
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Warranty::class);
        return Inertia::render('Warranty/Create', [
            'assets' => Asset::select('id', 'asset_tag')->orderBy('asset_tag')->get(),
            'defaults' => [
                'asset_id' => $request->integer('asset_id'),
                'start_date' => now()->toDateString(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Warranty::class);
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'description' => 'required|string|max:150',
            'provider' => 'nullable|string|max:150',
            'length_months' => 'required|integer|min:0|max:120',
            'start_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $this->warrantyService->create($validated);

        return Redirect::route('warranties.index')->with('success', 'Warranty created.');
    }

    public function show(Warranty $warranty): Response
    {
        $this->authorize('view', $warranty);
        $warranty->load([
            'asset:id,asset_tag,description',
            'asset.site:id,name',
            'asset.location:id,name',
        ]);

        return Inertia::render('Warranty/Show', [
            'warranty' => [
                'id' => $warranty->id,
                'provider' => $warranty->provider,
                'description' => $warranty->description,
                'length_months' => $warranty->length_months,
                'start_date' => optional($warranty->start_date)->toDateString(),
                'expiry_date' => optional($warranty->expiry_date)->toDateString(),
                'active' => (bool) $warranty->active,
                'notes' => $warranty->notes,
                'asset' => $warranty->asset ? [
                    'id' => $warranty->asset->id,
                    'asset_tag' => $warranty->asset->asset_tag,
                    'description' => $warranty->asset->description,
                    'site' => $warranty->asset->site?->name,
                    'location' => $warranty->asset->location?->name,
                ] : null,
            ],
        ]);
    }

    public function edit(Warranty $warranty): Response
    {
        $this->authorize('update', $warranty);
        return Inertia::render('Warranty/Edit', [
            'warranty' => [
                'id' => $warranty->id,
                'asset_id' => $warranty->asset_id,
                'description' => $warranty->description,
                'provider' => $warranty->provider,
                'length_months' => $warranty->length_months,
                'start_date' => optional($warranty->start_date)->toDateString(),
                'expiry_date' => optional($warranty->expiry_date)->toDateString(),
                'active' => (bool) $warranty->active,
                'notes' => $warranty->notes,
            ],
            'assets' => Asset::select('id', 'asset_tag')->orderBy('asset_tag')->get(),
        ]);
    }

    public function update(Request $request, Warranty $warranty)
    {
        $this->authorize('update', $warranty);
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'description' => 'required|string|max:150',
            'provider' => 'nullable|string|max:150',
            'length_months' => 'required|integer|min:0|max:120',
            'start_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $this->warrantyService->update($warranty, $validated);

        return Redirect::route('warranties.index')->with('success', 'Warranty updated.');
    }

    public function destroy(Warranty $warranty)
    {
        $this->authorize('delete', $warranty);
        $warranty->delete();

        return Redirect::back()->with('success', 'Warranty removed.');
    }
}
