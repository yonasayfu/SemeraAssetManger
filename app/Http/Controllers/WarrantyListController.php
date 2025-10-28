<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Site;
use App\Models\Warranty;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WarrantyListController extends Controller
{
    use HandlesDataExport;

    public function index(Request $request): Response
    {
        $perPage = $this->resolvePerPage($request->integer('per_page', 10));
        $search = trim((string) $request->query('search', ''));

        $query = Warranty::query()
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

        $warranties = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Warranty $warranty) {
                return [
                    'id' => $warranty->id,
                    'description' => $warranty->description,
                    'provider' => $warranty->provider,
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
                        'category' => $warranty->asset->category?->name,
                        'department' => $warranty->asset->department?->name,
                    ] : null,
                ];
            });

        $activeCount = Warranty::where('active', true)->count();
        $expiringSoon = Warranty::where('active', true)
            ->whereDate('expiry_date', '<=', now()->addMonth())
            ->count();

        return Inertia::render('Lists/Warranties/Index', [
            'warranties' => $warranties,
            'stats' => [
                [
                    'label' => 'Total Warranties',
                    'value' => Warranty::count(),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Active',
                    'value' => $activeCount,
                    'tone' => 'success',
                ],
                [
                    'label' => 'Expiring Soon',
                    'value' => $expiringSoon,
                    'tone' => 'warning',
                ],
            ],
            'filters' => [
                'search' => $search,
                'is_active' => $this->sanitizeBoolean($request->query('is_active')),
                'provider' => $request->query('provider'),
                'site' => $request->query('site'),
                'category' => $request->query('category'),
                'department' => $request->query('department'),
                'expires_from' => $request->query('expires_from'),
                'expires_to' => $request->query('expires_to'),
                'per_page' => $perPage,
                'sort' => $request->query('sort'),
                'direction' => $request->query('direction', 'asc'),
            ],
            'providerOptions' => Warranty::query()
                ->select('provider')
                ->whereNotNull('provider')
                ->distinct()
                ->orderBy('provider')
                ->pluck('provider')
                ->map(fn (string $provider) => [
                    'label' => $provider,
                    'value' => $provider,
                ]),
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
                ['title' => 'Warranty Tracker', 'href' => route('lists.warranties')],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport(
            $request,
            Warranty::class,
            ExportConfig::warranties(),
            [
                'label' => 'Warranty Tracker',
                'type' => 'warranties',
            ]
        );
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if (($active = $this->sanitizeBoolean($request->query('is_active'))) !== null) {
            $query->where('active', $active);
        }

        if ($provider = $request->query('provider')) {
            $query->where('provider', $provider);
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

        if ($from = $this->sanitizeDate($request->query('expires_from'))) {
            $query->whereDate('expiry_date', '>=', $from);
        }

        if ($to = $this->sanitizeDate($request->query('expires_to'))) {
            $query->whereDate('expiry_date', '<=', $to);
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
                ->where('description', 'like', "%{$term}%")
                ->orWhere('provider', 'like', "%{$term}%")
                ->orWhereHas('asset', fn (Builder $assetQuery) => $assetQuery->where('asset_tag', 'like', "%{$term}%"));
        });
    }

    protected function applySorting(Builder $query, Request $request): void
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortable = [
            'provider' => 'provider',
            'expiry_date' => 'expiry_date',
            'start_date' => 'start_date',
            'length_months' => 'length_months',
            'active' => 'active',
        ];

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);

            return;
        }

        $query->orderBy('expiry_date');
    }

    private function resolvePerPage(?int $perPage): int
    {
        $allowed = [10, 25, 50, 100];

        return in_array($perPage, $allowed, true) ? $perPage : 10;
    }

    private function sanitizeBoolean($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (in_array($value, [true, 'true', '1', 1], true)) {
            return true;
        }

        if (in_array($value, [false, 'false', '0', 0], true)) {
            return false;
        }

        return null;
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
