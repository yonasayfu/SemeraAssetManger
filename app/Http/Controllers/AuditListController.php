<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Location;
use App\Models\Site;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditListController extends Controller
{
    use HandlesDataExport;

    private const STATUSES = [
        'Draft',
        'Ongoing',
        'Completed',
    ];

    public function index(Request $request): Response
    {
        $perPage = $this->resolvePerPage($request->integer('per_page', 10));
        $search = trim((string) $request->query('search', ''));

        $query = Audit::query()->with([
            'site:id,name',
            'location:id,name',
        ]);

        $this->applyFilters($query, $request);
        $this->applySearch($query, $search);
        $this->applySorting($query, $request);

        $audits = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Audit $audit) {
                return [
                    'id' => $audit->id,
                    'name' => $audit->name,
                    'status' => $audit->status,
                    'site' => $audit->site?->name,
                    'location' => $audit->location?->name,
                    'started_at' => optional($audit->started_at)->toDateTimeString(),
                    'completed_at' => optional($audit->completed_at)->toDateTimeString(),
                ];
            });

        $statusCounts = Audit::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Lists/Audits/Index', [
            'audits' => $audits,
            'stats' => [
                [
                    'label' => 'Total Audits',
                    'value' => Audit::count(),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Ongoing',
                    'value' => $statusCounts->get('Ongoing', 0),
                    'tone' => 'warning',
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
                'site' => $request->query('site'),
                'location' => $request->query('location'),
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
            'breadcrumbs' => [
                ['title' => 'Lists', 'href' => '/lists'],
                ['title' => 'Audit Tracker', 'href' => route('lists.audits')],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function export(Request $request)
    {
        return $this->handleExport(
            $request,
            Audit::class,
            ExportConfig::audits(),
            [
                'label' => 'Audit Tracker',
                'type' => 'audits',
            ]
        );
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($status = $this->sanitizeStatus($request->query('status'))) {
            $query->where('status', $status);
        }

        if ($siteId = $this->sanitizeId($request->query('site'))) {
            $query->where('site_id', $siteId);
        }

        if ($locationId = $this->sanitizeId($request->query('location'))) {
            $query->where('location_id', $locationId);
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
                ->where('name', 'like', "%{$term}%");
        });
    }

    protected function applySorting(Builder $query, Request $request): void
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $sortable = [
            'name' => 'name',
            'status' => 'status',
            'started_at' => 'started_at',
            'completed_at' => 'completed_at',
        ];

        if ($sort && isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);

            return;
        }

        $query->orderBy('created_at', 'desc');
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

    private function sanitizeId($value): ?int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);

        return $int === false ? null : $int;
    }
}
