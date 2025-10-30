<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Models\Site;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssetListController extends Controller
{
    use HandlesDataExport;

    private const STATUSES = [
        'Available',
        'Checked Out',
        'Under Repair',
        'Leased',
        'Disposed',
        'Lost',
        'Donated',
        'Sold',
    ];

    private const CONDITIONS = [
        'New',
        'Good',
        'Fair',
        'Poor',
        'Broken',
    ];

    public function index(Request $request): Response
    {
        $perPage = $this->resolvePerPage($request->integer('per_page', 10));
        $search = trim((string) $request->query('search', ''));

        $query = Asset::query()
            ->with([
                'site:id,name',
                'location:id,name',
                'category:id,name',
                'department:id,name',
                'assignee:id,name',
                'activeWarranty:id,asset_id,provider,expiry_date',
                'upcomingMaintenance:id,asset_id,title,scheduled_for,status',
            ]);

        $this->applyFilters($query, $request);
        $this->applySearch($query, $search);
        $this->applySorting($query, $request);

        $assets = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Asset $asset) {
                return [
                    'id' => $asset->id,
                    'asset_tag' => $asset->asset_tag,
                    'description' => $asset->description,
                    'status' => $asset->status,
                    'condition' => $asset->asset_condition,
                    'site' => $asset->site?->name,
                    'location' => $asset->location?->name,
                    'category' => $asset->category?->name,
                    'department' => $asset->department?->name,
                    'assignee' => $asset->assignee ? [
                        'id' => $asset->assignee->id,
                        'name' => $asset->assignee->name,
                    ] : null,
                    'purchase_date' => optional($asset->purchase_date)->toDateString(),
                    'cost' => $asset->cost,
                    'currency' => $asset->currency,
                    'warranty' => $asset->activeWarranty ? [
                        'provider' => $asset->activeWarranty->provider,
                        'expiry_date' => optional($asset->activeWarranty->expiry_date)->toDateString(),
                    ] : null,
                    'upcoming_maintenance' => $asset->upcomingMaintenance ? [
                        'title' => $asset->upcomingMaintenance->title,
                        'scheduled_for' => optional($asset->upcomingMaintenance->scheduled_for)->toDateString(),
                        'status' => $asset->upcomingMaintenance->status,
                    ] : null,
                    'updated_at' => optional($asset->updated_at)->toDateTimeString(),
                ];
            });

        $statusCounts = Asset::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Lists/Assets/Index', [
            'assets' => $assets,
            'stats' => [
                [
                    'label' => 'Total Assets',
                    'value' => Asset::count(),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Available',
                    'value' => $statusCounts->get('Available', 0),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Checked Out',
                    'value' => $statusCounts->get('Checked Out', 0),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Needs Attention',
                    'value' => $statusCounts->only(['Under Repair', 'Disposed', 'Lost'])->sum(),
                    'tone' => 'muted',
                ],
            ],
            'filters' => [
                'search' => $search,
                'status' => $this->sanitizeStatus($request->query('status')),
                'condition' => $this->sanitizeCondition($request->query('condition')),
                'site' => $request->query('site'),
                'location' => $request->query('location'),
                'category' => $request->query('category'),
                'department' => $request->query('department'),
                'per_page' => $perPage,
                'sort' => $request->query('sort'),
                'direction' => $request->query('direction', 'asc'),
            ],
            'statusOptions' => collect(self::STATUSES)->map(fn (string $status) => [
                'label' => $status,
                'value' => $status,
            ])->prepend([
                'label' => 'All statuses',
                'value' => null,
            ])->values(),
            'conditionOptions' => collect(self::CONDITIONS)->map(fn (string $condition) => [
                'label' => $condition,
                'value' => $condition,
            ])->prepend([
                'label' => 'All conditions',
                'value' => null,
            ])->values(),
            'siteOptions' => Site::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
                ->map(fn (Site $site) => [
                    'label' => $site->name,
                    'value' => (string) $site->id,
                ]),
            'locationOptions' => Location::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
                ->map(fn (Location $location) => [
                    'label' => $location->name,
                    'value' => (string) $location->id,
                ]),
            'categoryOptions' => Category::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
                ->map(fn (Category $category) => [
                    'label' => $category->name,
                    'value' => (string) $category->id,
                ]),
            'departmentOptions' => Department::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
                ->map(fn (Department $department) => [
                    'label' => $department->name,
                    'value' => (string) $department->id,
                ]),
            'breadcrumbs' => [
                ['title' => 'Lists', 'href' => '/lists'],
                ['title' => 'Asset Inventory', 'href' => route('lists.assets')],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport(
            $request,
            Asset::class,
            ExportConfig::assets(),
            [
                'label' => 'Asset Inventory',
                'type' => 'assets',
            ]
        );
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($status = $this->sanitizeStatus($request->query('status'))) {
            $query->where('status', $status);
        }

        if ($condition = $this->sanitizeCondition($request->query('condition'))) {
            $query->where('asset_condition', $condition);
        }

        if ($siteId = $this->sanitizeId($request->query('site'))) {
            $query->where('site_id', $siteId);
        }

        if ($locationId = $this->sanitizeId($request->query('location'))) {
            $query->where('location_id', $locationId);
        }

        if ($categoryId = $this->sanitizeId($request->query('category'))) {
            $query->where('category_id', $categoryId);
        }

        if ($departmentId = $this->sanitizeId($request->query('department'))) {
            $query->where('department_id', $departmentId);
        }

        if ($ids = $this->sanitizeIds($request->query('selected'))) {
            $query->whereIn('id', $ids);
        }
    }

    protected function applySearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $builder) use ($term) {
            $builder
                ->where('asset_tag', 'ilike', "%{$term}%")
                ->orWhere('description', 'ilike', "%{$term}%")
                ->orWhere('serial_no', 'ilike', "%{$term}%")
                ->orWhere('brand', 'ilike', "%{$term}%")
                ->orWhere('model', 'ilike', "%{$term}%")
                ->orWhere('project_code', 'ilike', "%{$term}%");
        });
    }

    protected function applySorting(Builder $query, Request $request): void
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortable = [
            'asset_tag' => 'asset_tag',
            'status' => 'status',
            'purchase_date' => 'purchase_date',
            'cost' => 'cost',
            'updated_at' => 'updated_at',
        ];

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);

            return;
        }

        $query->orderBy('asset_tag');
    }

    private function resolvePerPage(?int $perPage): int
    {
        $allowed = [10, 25, 50, 100];

        return in_array($perPage, $allowed, true) ? $perPage : 10;
    }

    private function sanitizeStatus(?string $status): ?string
    {
        return in_array($status, self::STATUSES, true) ? $status : null;
    }

    private function sanitizeCondition(?string $condition): ?string
    {
        return in_array($condition, self::CONDITIONS, true) ? $condition : null;
    }

    private function sanitizeId($value): ?int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);

        return $int === false ? null : $int;
    }

    private function sanitizeIds(null|string|array $value): array
    {
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($id) => filter_var($id, FILTER_VALIDATE_INT))
                ->filter(fn ($id) => $id !== false)
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        if (is_string($value)) {
            return collect(explode(',', $value))
                ->map(fn ($id) => filter_var(trim($id), FILTER_VALIDATE_INT))
                ->filter(fn ($id) => $id !== false)
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return [];
    }
}
