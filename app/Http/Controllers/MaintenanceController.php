<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\Site;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceController extends Controller
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Maintenance::class);
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'type' => $request->string('type')->toString(),
            'site' => $request->integer('site'),
            'asset' => $request->integer('asset'),
            'is_recurring' => $request->boolean('is_recurring', null),
        ];

        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $query = Maintenance::query()
            ->with([
                'asset:id,asset_tag,description,site_id,location_id',
                'asset.site:id,name',
                'asset.location:id,name',
            ])
            ->when($filters['search'], function ($builder, $search) {
                $builder->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('asset', fn ($assetQuery) => $assetQuery->where('asset_tag', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'], fn ($builder, $status) => $builder->where('status', $status))
            ->when($filters['type'], fn ($builder, $type) => $builder->where('maintenance_type', $type))
            ->when($filters['site'], fn ($builder, $site) => $builder->whereHas('asset', fn ($assetQuery) => $assetQuery->where('site_id', $site)))
            ->when($filters['asset'], fn ($builder, $assetId) => $builder->where('asset_id', $assetId))
            ->when(! is_null($filters['is_recurring']), fn ($builder) => $builder->where('is_recurring', $filters['is_recurring']))
            ->orderByDesc('scheduled_for')
            ->orderByDesc('created_at');

        $maintenances = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Maintenance $maintenance) {
                return [
                    'id' => $maintenance->id,
                    'title' => $maintenance->title,
                    'status' => $maintenance->status,
                    'maintenance_type' => $maintenance->maintenance_type,
                    'scheduled_for' => optional($maintenance->scheduled_for)->toIso8601String(),
                    'completed_at' => optional($maintenance->completed_at)->toIso8601String(),
                    'is_recurring' => (bool) $maintenance->is_recurring,
                    'asset' => $maintenance->asset ? [
                        'id' => $maintenance->asset->id,
                        'asset_tag' => $maintenance->asset->asset_tag,
                        'description' => $maintenance->asset->description,
                        'site' => $maintenance->asset->site?->name,
                        'location' => $maintenance->asset->location?->name,
                    ] : null,
                ];
            });

        $stats = [
            [
                'label' => 'Open',
                'value' => Maintenance::whereIn('status', ['Open', 'Scheduled', 'In Progress'])->count(),
                'tone' => 'warning',
            ],
            [
                'label' => 'Completed',
                'value' => Maintenance::where('status', 'Completed')->count(),
                'tone' => 'success',
            ],
            [
                'label' => 'Recurring',
                'value' => Maintenance::where('is_recurring', true)->count(),
                'tone' => 'primary',
            ],
        ];

        return Inertia::render('Maintenance/Index', [
            'maintenances' => $maintenances,
            'filters' => array_merge($filters, ['per_page' => $perPage]),
            'stats' => $stats,
            'statusOptions' => $this->statusOptions(),
            'typeOptions' => $this->typeOptions(),
            'assetOptions' => Asset::select('id', 'asset_tag')->orderBy('asset_tag')->get(),
            'siteOptions' => Site::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Maintenance::class);
        return Inertia::render('Maintenance/Create', [
            'assets' => Asset::select('id', 'asset_tag')->orderBy('asset_tag')->get(),
            'defaults' => [
                'asset_id' => $request->integer('asset_id'),
                'scheduled_for' => now()->toDateTimeString(),
            ],
            'statusOptions' => $this->statusOptions(),
            'typeOptions' => $this->typeOptions(),
            'frequencyOptions' => $this->recurrenceFrequencyOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Maintenance::class);
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'maintenance_type' => 'required|in:Preventive,Corrective',
            'status' => 'required|in:Open,Scheduled,In Progress,Completed,Cancelled,Overdue',
            'scheduled_for' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'vendor' => 'nullable|string|max:150',
            'cost' => 'nullable|numeric',
            'labor_cost' => 'nullable|numeric',
            'parts_cost' => 'nullable|numeric',
            'is_recurring' => 'boolean',
            'recurrence_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:12',
        ]);

        $this->maintenanceService->create($validated);

        return Redirect::route('maintenance.index')->with('success', 'Maintenance record created.');
    }

    public function show(Maintenance $maintenance): Response
    {
        $this->authorize('view', $maintenance);
        $maintenance->load([
            'asset:id,asset_tag,description',
            'asset.site:id,name',
            'asset.location:id,name',
        ]);

        return Inertia::render('Maintenance/Show', [
            'maintenance' => [
                'id' => $maintenance->id,
                'title' => $maintenance->title,
                'description' => $maintenance->description,
                'status' => $maintenance->status,
                'maintenance_type' => $maintenance->maintenance_type,
                'scheduled_for' => optional($maintenance->scheduled_for)->toIso8601String(),
                'completed_at' => optional($maintenance->completed_at)->toIso8601String(),
                'vendor' => $maintenance->vendor,
                'cost' => $maintenance->cost,
                'labor_cost' => $maintenance->labor_cost,
                'parts_cost' => $maintenance->parts_cost,
                'is_recurring' => (bool) $maintenance->is_recurring,
                'recurrence_frequency' => $maintenance->recurrence_frequency,
                'recurrence_interval' => $maintenance->recurrence_interval,
                'next_scheduled_for' => optional($maintenance->next_scheduled_for)->toIso8601String(),
                'asset' => $maintenance->asset ? [
                    'id' => $maintenance->asset->id,
                    'asset_tag' => $maintenance->asset->asset_tag,
                    'description' => $maintenance->asset->description,
                    'site' => $maintenance->asset->site?->name,
                    'location' => $maintenance->asset->location?->name,
                ] : null,
            ],
        ]);
    }

    public function edit(Maintenance $maintenance): Response
    {
        $this->authorize('update', $maintenance);
        return Inertia::render('Maintenance/Edit', [
            'maintenance' => [
                'id' => $maintenance->id,
                'asset_id' => $maintenance->asset_id,
                'title' => $maintenance->title,
                'description' => $maintenance->description,
                'status' => $maintenance->status,
                'maintenance_type' => $maintenance->maintenance_type,
                'scheduled_for' => optional($maintenance->scheduled_for)->toDateTimeLocalString(),
                'completed_at' => optional($maintenance->completed_at)->toDateTimeLocalString(),
                'vendor' => $maintenance->vendor,
                'cost' => $maintenance->cost,
                'labor_cost' => $maintenance->labor_cost,
                'parts_cost' => $maintenance->parts_cost,
                'is_recurring' => (bool) $maintenance->is_recurring,
                'recurrence_frequency' => $maintenance->recurrence_frequency,
                'recurrence_interval' => $maintenance->recurrence_interval,
            ],
            'assets' => Asset::select('id', 'asset_tag')->orderBy('asset_tag')->get(),
            'statusOptions' => $this->statusOptions(),
            'typeOptions' => $this->typeOptions(),
            'frequencyOptions' => $this->recurrenceFrequencyOptions(),
        ]);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $this->authorize('update', $maintenance);
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'maintenance_type' => 'required|in:Preventive,Corrective',
            'status' => 'required|in:Open,Scheduled,In Progress,Completed,Cancelled,Overdue',
            'scheduled_for' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'vendor' => 'nullable|string|max:150',
            'cost' => 'nullable|numeric',
            'labor_cost' => 'nullable|numeric',
            'parts_cost' => 'nullable|numeric',
            'is_recurring' => 'boolean',
            'recurrence_frequency' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:12',
        ]);

        $this->maintenanceService->update($maintenance, $validated);

        return Redirect::route('maintenance.index')->with('success', 'Maintenance record updated.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $this->authorize('delete', $maintenance);
        $maintenance->delete();

        return Redirect::back()->with('success', 'Maintenance record removed.');
    }

    protected function statusOptions(): array
    {
        return [
            ['label' => 'Open', 'value' => 'Open'],
            ['label' => 'Scheduled', 'value' => 'Scheduled'],
            ['label' => 'In Progress', 'value' => 'In Progress'],
            ['label' => 'Completed', 'value' => 'Completed'],
            ['label' => 'Overdue', 'value' => 'Overdue'],
            ['label' => 'Cancelled', 'value' => 'Cancelled'],
        ];
    }

    protected function typeOptions(): array
    {
        return [
            ['label' => 'Preventive', 'value' => 'Preventive'],
            ['label' => 'Corrective', 'value' => 'Corrective'],
        ];
    }

    protected function recurrenceFrequencyOptions(): array
    {
        return [
            ['label' => 'Daily', 'value' => 'daily'],
            ['label' => 'Weekly', 'value' => 'weekly'],
            ['label' => 'Monthly', 'value' => 'monthly'],
            ['label' => 'Yearly', 'value' => 'yearly'],
        ];
    }
}
