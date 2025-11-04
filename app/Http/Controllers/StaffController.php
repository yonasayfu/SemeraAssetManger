<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffStoreRequest;
use App\Http\Requests\StaffUpdateRequest;
use App\Http\Resources\ActivityLogResource;
use App\Models\Staff;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use App\Support\Storage\StoragePath;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    use HandlesDataExport;

    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowedPerPage = [5, 10, 25, 50, 100];
        $perPage = $request->integer('per_page', 5);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }
        $status = $this->extractStatusFilter($request);

        $query = Staff::query();

        $this->applyFilters($query, $request);
        $this->applySearch($query, $search);
        $this->applySorting($query, $request);

        $staff = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Staff $staff) => [
                'id' => $staff->id,
                'full_name' => $staff->full_name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'status' => $staff->status,
                'avatar_url' => $staff->avatar_url,
                'user' => null,
            ]);

        return Inertia::render('Staff/Index', [
            'staff' => $staff,
            'stats' => [
                [
                    'label' => 'Total Staff',
                    'value' => Staff::count(),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Active',
                    'value' => Staff::where('status', 'active')->count(),
                    'tone' => 'success',
                ],
                [
                    'label' => 'Inactive',
                    'value' => Staff::where('status', 'inactive')->count(),
                    'tone' => 'muted',
                ],
            ],
            'filters' => [
                'search' => $search,
                'status' => $status,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => $perPage,
            ],
            'statuses' => [
                ['label' => 'All', 'value' => null],
                ['label' => 'Active', 'value' => 'active'],
                ['label' => 'Inactive', 'value' => 'inactive'],
            ],
            'can' => [
                'create' => $request->user()->can('staff.create'),
                'edit' => $request->user()->can('staff.update'),
                'delete' => $request->user()->can('staff.delete'),
            ],
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function export(Request $request)
    {
        // Inline minimal config to avoid external parse issues
        $config = [
            'label' => 'Staff Directory',
            'type' => 'staff',
            'filename_prefix' => 'staff-directory',
            'csv' => [
                'headers' => ['#', 'Full Name', 'Email', 'Phone', 'Job Title', 'Status'],
                'fields' => [
                    'index',
                    'full_name',
                    'email',
                    'phone',
                    'job_title',
                    [
                        'field' => 'status',
                        'transform' => fn ($value) => Str::of($value ?? '')->replace('_', ' ')->title(),
                        'default' => 'Inactive',
                    ],
                ],
                'filename_prefix' => 'staff-directory',
            ],
        ];

        return $this->handleExport($request, Staff::class, $config, [
            'label' => 'Staff Directory',
            'type' => 'staff',
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Staff::class);

        return Inertia::render('Staff/Create', [
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
                ['title' => 'Create', 'href' => route('staff.create')],
            ],
        ]);
    }

    public function show(Request $request, Staff $staff): Response
    {
        $this->authorize('view', $staff);

        // no linked user in current model

        $activity = $staff->activityLogs()
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Staff/Show', [
            'staff' => [
                'id' => $staff->id,
                'full_name' => $staff->full_name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'status' => $staff->status,
                'avatar_url' => $staff->avatar_url,
                'avatar_label' => $staff->avatar_path ? basename($staff->avatar_path) : null,
                'user' => null,
            ],
            'activity' => ActivityLogResource::collection($activity),
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
                ['title' => $staff->full_name ?: $staff->name, 'href' => route('staff.show', $staff)],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Staff::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:staff,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store(
                StoragePath::staffAvatars(),
                'public'
            );
        }

        Staff::create($data);

        return redirect()
            ->route('staff.index')
            ->with('bannerStyle', 'success')
            ->with('banner', 'Staff member created successfully.');
    }

    public function edit(Staff $staff): Response
    {
        $this->authorize('update', $staff);

        $activity = $staff->activityLogs()
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Staff/Edit', [
            'staff' => [
                'id' => $staff->id,
                'full_name' => $staff->full_name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'status' => $staff->status,
                'avatar_url' => $staff->avatar_url,
                'avatar_label' => $staff->avatar_path ? basename($staff->avatar_path) : null,
            ],
            'activity' => ActivityLogResource::collection($activity),
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
                ['title' => $staff->full_name ?: $staff->name, 'href' => route('staff.edit', $staff)],
            ],
        ]);
    }

    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $this->authorize('update', $staff);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique('staff', 'email')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($staff->avatar_path) {
                Storage::disk('public')->delete($staff->avatar_path);
            }

            $data['avatar_path'] = $request->file('avatar')->store(
                StoragePath::staffAvatars(),
                'public'
            );
        } elseif ($request->boolean('remove_avatar')) {
            if ($staff->avatar_path) {
                Storage::disk('public')->delete($staff->avatar_path);
            }

            $data['avatar_path'] = null;
        }

        $staff->update($data);

        return redirect()
            ->route('staff.index')
            ->with('bannerStyle', 'success')
            ->with('banner', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff): RedirectResponse
    {
        $this->authorize('delete', $staff);

        if ($staff->avatar_path) {
            Storage::disk('public')->delete($staff->avatar_path);
        }

        $staff->delete();

        return redirect()
            ->route('staff.index')
            ->with('bannerStyle', 'info')
            ->with('banner', 'Staff member removed.');
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        $status = $this->extractStatusFilter($request);

        if ($status) {
            $query->where('status', $status);
        }
    }

    protected function applySearch(Builder $query, ?string $search): void
    {
        $term = trim((string) $search);

        if ($term === '') {
            return;
        }

        $query->where(function (Builder $builder) use ($term) {
            $builder
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }

    protected function applySorting(Builder $query, Request $request): void
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $sortable = ['name', 'email', 'status', 'created_at'];

        if ($sort && in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $direction);

            return;
        }

        $query->orderBy('name');
    }

    protected function extractStatusFilter(Request $request): ?string
    {
        $status = $request->query('status');

        return in_array($status, ['active', 'inactive'], true) ? $status : null;
    }
}
