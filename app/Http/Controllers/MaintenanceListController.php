<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Maintenance;
use App\Models\Site;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceListController extends Controller
{
    use HandlesDataExport;

    private const STATUSES = [
        'Open',
        'Scheduled',
        'In Progress',
        'Completed',
        'Cancelled',
        'Overdue',
    ];

    private const TYPES = [
        'Preventive',
        'Corrective',
    ];

    public function index(Request $request): Response
    {
        $perPage = $this->resolvePerPage($request->integer('per_page', 10));
        $search = trim((string) $request->query('search', ''));

        $query = Maintenance::query()
            ->with([
                'asset:id,asset_tag,description,site_id,location_id,category_id,department_id',
                'asset.site:id,name',
                'asset.location:id,name',
                'asset.category:id,name',
                'asset.department:id,name',
            ]);

        $this->applyFilters($query, $request);
        $this->applySearch($query, $search);
        $this->applySorting($query, $request);

        $maintenances = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Maintenance $maintenance) {
                return [
                    'id' => $maintenance->id,
                    'title' => $maintenance->title,
                    'description' => $maintenance->description,
                    'status' => $maintenance->status,
                    'maintenance_type' => $maintenance->maintenance_type,
                    'scheduled_for' => optional($maintenance->scheduled_for)->toDateString(),
                    'completed_at' => optional($maintenance->completed_at)->toDateString(),
                    'cost' => $maintenance->cost,
                    'asset' => $maintenance->asset ? [
                        'id' => $maintenance->asset->id,
                        'asset_tag' => $maintenance->asset->asset_tag,
                        'description' => $maintenance->asset->description,
                        'site' => $maintenance->asset->site?->name,
                        'location' => $maintenance->asset->location?->name,
                        'category' => $maintenance->asset->category?->name,
                        'department' => $maintenance->asset->department?->name,
                    ] : null,
                ];
            });

        $statusCounts = Maintenance::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Lists/Maintenances/Index', [
            'maintenances' => $maintenances,
            'stats' => [
                [
                    'label' => 'Total Tickets',
                    'value' => Maintenance::count(),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Open / In Progress',
                    'value' => $statusCounts->get('Open', 0) + $statusCounts->get('In Progress', 0),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Due Soon',
                    'value' => Maintenance::whereNull('completed_at')
                        ->whereDate('scheduled_for', '<=', now()->addWeek())
                        ->count(),
                    'tone' => 'muted',
                ],
                [
                    'label' => 'Completed',
                    'value' => $statusCounts->get('Completed', 0),
                    'tone' => 'success',
                ],
            ],
            'filters' => [
                'search' => $search,
                'status' => $this->sanitizeStatus($request->query('status')),
                'type' => $this->sanitizeType($request->query('type')),
                'site' => $request->query('site'),
                'category' => $request->query('category'),
                'department' => $request->query('department'),
                'scheduled_from' => $request->query('scheduled_from'),
                'scheduled_to' => $request->query('scheduled_to'),
                'per_page' => $perPage,
                'sort' => $request->query('sort'),
                'direction' => $request->query('direction', 'asc'),
            ],
            'statusOptions' => collect(self::STATUSES)->map(fn (string $status) => [
                'label' => $status,
                'value' => $status,
            ])->prepend(['label' => 'All statuses', 'value' => null])->values(),
            'typeOptions' => collect(self::TYPES)->map(fn (string $type) => [
                'label' => $type,
                'value' => $type,
            ])->prepend(['label' => 'All types', 'value' => null])->values(),
            'siteOptions' => Site::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get()
                ->map(fn (Site $site) => [
                    'label' => $site->name,
                    'value' => (string) $site->id,
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
                ['title' => 'Maintenance Tickets', 'href' => route('lists.maintenances')],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport(
            $request,
            Maintenance::class,
            ExportConfig::maintenances(),
            [
                'label' => 'Maintenance Tickets',
                'type' => 'maintenances',
            ]
        );
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($status = $this->sanitizeStatus($request->query('status'))) {
            $query->where('status', $status);
        }

        if ($type = $this->sanitizeType($request->query('type'))) {
            $query->where('maintenance_type', $type);
        }

        if ($siteId = $this->sanitizeId($request->query('site'))) {
            $query->whereHas('asset', fn (Builder $builder) => $builder->where('site_id', $siteId));
        }

        if ($categoryId = $this->sanitizeId($request->query('category'))) {
            $query->whereHas('asset', fn (Builder $builder) => $builder->where('category_id', $categoryId));
        }

        if ($departmentId = $this->sanitizeId($request->query('department'))) {
            $query->whereHas('asset', fn (Builder $builder) => $builder->where('department_id', $departmentId));
        }

        if ($from = $this->sanitizeDate($request->query('scheduled_from'))) {
            $query->whereDate('scheduled_for', '>=', $from);
        }

        if ($to = $this->sanitizeDate($request->query('scheduled_to'))) {
            $query->whereDate('scheduled_for', '<=', $to);
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
                ->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhereHas('asset', fn (Builder $assetQuery) => $assetQuery->where('asset_tag', 'like', "%{$term}%"));
        });
    }

    protected function applySorting(Builder $query, Request $request): void
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortable = [
            'title' => 'title',
            'status' => 'status',
            'scheduled_for' => 'scheduled_for',
            'completed_at' => 'completed_at',
            'cost' => 'cost',
        ];

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);

            return;
        }

        $query->orderByDesc('scheduled_for');
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

    private function sanitizeType(?string $type): ?string
    {
        return in_array($type, self::TYPES, true) ? $type : null;
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

    private function sanitizeDate(?string $date): ?string
    {
        if (! $date) {
            return null;
        }

        $parsed = date_create($date);

        return $parsed ? $parsed->format('Y-m-d') : null;
    }
}
