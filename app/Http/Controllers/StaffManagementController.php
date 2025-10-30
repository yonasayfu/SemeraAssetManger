<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffStoreRequest;
use App\Http\Requests\StaffUpdateRequest;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use App\Models\Staff;
use App\Support\Exports\ExportConfig;
use App\Support\Exports\HandlesDataExport;
use App\Support\Users\SyncsStaffAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffManagementController extends Controller
{
    use HandlesDataExport;
    use SyncsStaffAssignment;
    public function index(Request $request): Response
    {
        $this->ensureCanManageStaff();

        $search = trim((string) $request->query('search', ''));
        $allowedPerPage = [5, 10, 25, 50, 100];
        $perPage = $request->integer('per_page', 5);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 5;
        }
        $sort = $this->resolveSort($request);
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = Staff::query()->with([
            'roles:id,name',
            'permissions:id,name',
            'staff:id,first_name,last_name,email,status,user_id',
            'approver:id,name',
        ]);

        $this->applySearch($query, $search);
        $this->applySorting($query, $request);

        $users = $query
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (Staff $staff) {
                return [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'account_status' => $staff->account_status,
                    'account_type' => $staff->account_type,
                    'approved_at' => optional($staff->approved_at)->toIso8601String(),
                    'approved_by' => $staff->approver?->name,
                    'is_pending' => $staff->account_status === Staff::STATUS_PENDING,
                    'roles' => $staff->roles->pluck('name')->values(),
                    'permissions' => $staff->getAllPermissions()->pluck('name')->values(),
                    'has_two_factor' => ! is_null($staff->two_factor_secret),
                    'staff' => $staff->staff ? [
                        'id' => $staff->staff->id,
                        'full_name' => $staff->staff->full_name,
                        'status' => $staff->staff->status,
                    ] : null,
                ];
            });

        $staffLinkedCount = Staff::whereNotNull('user_id')->count();
        $pendingCount = Staff::where('account_status', Staff::STATUS_PENDING)->count();

        return Inertia::render('Staff/Index', [
            'users' => $users,
            'stats' => [
                [
                    'label' => 'Total Staff',
                    'value' => $users->total(),
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Staff Linked',
                    'value' => $staffLinkedCount,
                    'tone' => 'success',
                ],
                [
                    'label' => 'Roles',
                    'value' => Role::count(),
                    'tone' => 'muted',
                ],
                [
                    'label' => 'Pending Approval',
                    'value' => $pendingCount,
                    'tone' => 'primary',
                ],
            ],
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'can' => [
                'create' => $request->user()->can('users.manage'),
                'edit' => $request->user()->can('users.manage'),
                'delete' => $request->user()->can('users.manage'),
                'impersonate' => $request->user()->can('users.impersonate'),
            ],
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function export(Request $request)
    {
        $this->ensureCanManageStaff();

        return $this->handleExport($request, Staff::class, ExportConfig::staff(), [
            'label' => 'Staff Roster',
            'type' => 'staff',
        ]);
    }

    public function create(): Response
    {
        $this->ensureCanManageStaff();

        return Inertia::render('Staff/Create', [
            'roles' => $this->availableRoles(),
            'permissions' => $this->availablePermissions(),
            'staff' => $this->availableStaff(),
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
                ['title' => 'Create', 'href' => route('staff.create')],
            ],
        ]);
    }

    public function show(Request $request, Staff $staff): Response
    {
        $this->ensureCanManageStaff();

        $staff->load([
            'roles:id,name',
            'permissions:id,name',
            'staff:id,first_name,last_name,status,user_id',
            'approver:id,name',
        ]);

        $activity = $staff->activityLogs()
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Staff/Show', [
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'account_status' => $staff->account_status,
                'account_type' => $staff->account_type,
                'approved_at' => optional($staff->approved_at)->toIso8601String(),
                'approved_by' => $staff->approver?->name,
                'roles' => $staff->roles->pluck('name')->values(),
                'permissions' => $staff->getAllPermissions()->pluck('name')->values(),
                'has_two_factor' => ! is_null($staff->two_factor_secret),
                'staff' => $staff->staff ? [
                    'id' => $staff->staff->id,
                    'full_name' => $staff->staff->full_name,
                    'status' => $staff->staff->status,
                ] : null,
                'created_at' => optional($staff->created_at)->toDateTimeString(),
                'updated_at' => optional($staff->updated_at)->toDateTimeString(),
            ],
            'activity' => ActivityLogResource::collection($activity),
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
                ['title' => $staff->name, 'href' => route('staff.show', $staff)],
            ],
            'print' => (bool) $request->boolean('print'),
        ]);
    }

    public function store(StaffStoreRequest $request): RedirectResponse
    {
        $this->ensureCanManageStaff();

        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $status = $data['account_status'];
            $accountType = $data['account_type'];
            $isActive = $status === Staff::STATUS_ACTIVE;

            $staff = Staff::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
                'account_status' => $status,
                'account_type' => $accountType,
                'approved_at' => $isActive ? now() : null,
                'approved_by' => $isActive ? $request->user()->id : null,
            ]);

            $staff->syncRoles($data['roles'] ?? []);
            $staff->syncPermissions($data['permissions'] ?? []);

            $this->syncStaffAssignment($staff, $data['staff_id'] ?? null);
        });

        return redirect()
            ->route('staff.index')
            ->with('bannerStyle', 'success')
            ->with('banner', 'Staff created successfully.');
    }

    public function edit(Staff $staff): Response
    {
        $this->ensureCanManageStaff();

        $staff->load(['roles:id,name', 'permissions:id,name', 'staff:id', 'approver:id,name']);

        $activity = $staff->activityLogs()
            ->with('causer')
            ->latest()
            ->take(20)
            ->get();

        return Inertia::render('Staff/Edit', [
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'account_status' => $staff->account_status,
                'account_type' => $staff->account_type,
                'approved_at' => optional($staff->approved_at)->toIso8601String(),
                'approved_by' => $staff->approver?->name,
                'roles' => $staff->roles->pluck('name')->values(),
                'permissions' => $staff->getAllPermissions()->pluck('name')->values(),
                'staff_id' => $staff->staff?->id,
            ],
            'roles' => $this->availableRoles(),
            'permissions' => $this->availablePermissions(),
            'staff' => $this->availableStaff($staff),
            'activity' => ActivityLogResource::collection($activity),
            'breadcrumbs' => [
                ['title' => 'Staff', 'href' => route('staff.index')],
                ['title' => $staff->name, 'href' => route('staff.edit', $staff)],
            ],
        ]);
    }

    public function update(StaffUpdateRequest $request, Staff $staff): RedirectResponse
    {
        $this->ensureCanManageStaff();

        $oldRoles = $staff->roles->pluck('name')->sort()->values()->toArray();
        $oldPermissions = $staff->getAllPermissions()->pluck('name')->sort()->values()->toArray();
        $oldStatus = $staff->account_status;
        $oldType = $staff->account_type;
        $oldApprovedAt = $staff->approved_at;

        $newRoles = $oldRoles;
        $newPermissions = $oldPermissions;
        $newStatus = $oldStatus;
        $newType = $oldType;
        $newApprovedAt = $oldApprovedAt;

        DB::transaction(function () use ($request, $staff, &$newRoles, &$newPermissions, &$newStatus, &$newType, &$newApprovedAt) {
            $data = $request->validated();
            $status = $data['account_status'];
            $accountType = $data['account_type'];

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'account_status' => $status,
                'account_type' => $accountType,
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            if ($staff->account_status !== $status) {
                if ($status === Staff::STATUS_ACTIVE) {
                    $payload['approved_at'] = now();
                    $payload['approved_by'] = $request->user()->id;
                } else {
                    $payload['approved_at'] = null;
                    $payload['approved_by'] = null;
                }
            }

            $staff->update($payload);

            $staff->syncRoles($data['roles'] ?? []);
            $staff->syncPermissions($data['permissions'] ?? []);

            $this->syncStaffAssignment($staff, $data['staff_id'] ?? null);

            $staff->refresh()->load('roles');
            $newRoles = $staff->roles->pluck('name')->sort()->values()->toArray();
            $newPermissions = $staff->getAllPermissions()->pluck('name')->sort()->values()->toArray();
            $newStatus = $staff->account_status;
            $newType = $staff->account_type;
            $newApprovedAt = $staff->approved_at;
        });

        if ($oldRoles !== $newRoles || $oldPermissions !== $newPermissions) {
            ActivityLog::record(
                auth()->id(),
                $staff->fresh(),
                'roles.updated',
                'Roles or permissions updated',
                [
                    'before' => [
                        'roles' => $oldRoles,
                        'permissions' => $oldPermissions,
                    ],
                    'after' => [
                        'roles' => $newRoles,
                        'permissions' => $newPermissions,
                    ],
                ]
            );
        }

        if ($oldStatus !== $newStatus || $oldType !== $newType) {
            ActivityLog::record(
                auth()->id(),
                $staff->fresh(),
                'staff.account_status.updated',
                'Account status updated',
                [
                    'before' => [
                        'status' => $oldStatus,
                        'type' => $oldType,
                        'approved_at' => optional($oldApprovedAt)->toDateTimeString(),
                    ],
                    'after' => [
                        'status' => $newStatus,
                        'type' => $newType,
                        'approved_at' => optional($newApprovedAt)->toDateTimeString(),
                    ],
                ]
            );
        }

        return redirect()
            ->route('staff.index')
            ->with('bannerStyle', 'success')
            ->with('banner', 'Staff updated successfully.');
    }

    public function destroy(Request $request, Staff $staff): RedirectResponse
    {
        $this->ensureCanManageStaff();

        if ($request->user()->is($staff)) {
            return back()
                ->with('bannerStyle', 'warning')
                ->with('banner', 'You cannot delete your own account.');
        }

        DB::transaction(function () use ($staff) {
            $this->syncStaffAssignment($staff, null);
            $staff->delete();
        });

        return redirect()
            ->route('staff.index')
            ->with('bannerStyle', 'info')
            ->with('banner', 'Staff removed.');
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
        $sort = $this->resolveSort($request);
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        if ($sort) {
            $query->orderBy($sort, $direction);

            return;
        }

        $query->orderBy('name');
    }

    protected function resolveSort(Request $request): ?string
    {
        $sort = $request->query('sort');
        $sortable = ['name', 'email', 'created_at'];

        return in_array($sort, $sortable, true) ? $sort : null;
    }

    protected function availableRoles(): array
    {
        return Role::orderBy('name')
            ->get()
            ->map(fn (Role $role) => $role->name)
            ->values()
            ->toArray();
    }

    protected function availablePermissions(): array
    {
        return Permission::orderBy('name')
            ->get()
            ->map(fn (Permission $permission) => $permission->name)
            ->values()
            ->toArray();
    }

    protected function availableStaff(?Staff $staffModel = null): array
    {
        return Staff::orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Staff $staff) use ($staffModel) {
                return [
                    'id' => $staff->id,
                    'label' => $staff->full_name,
                    'status' => $staff->status,
                    'linked_user_id' => $staff->user_id,
                    'linked_to_current_user' => $staffModel ? $staff->user_id === $staffModel->id : false,
                ];
            })
            ->values()
            ->toArray();
    }

    protected function ensureCanManageStaff(): void
    {
        abort_unless(auth()->user()?->can('staff.manage'), 403);
    }
}
