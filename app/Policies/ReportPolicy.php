<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\Staff as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('reports.view');
    }

    public function view(User $user, Report $report): bool
    {
        return $user->can('reports.view');
    }

    public function create(User $user): bool
    {
        return $user->can('reports.create');
    }

    public function update(User $user, Report $report): bool
    {
        return $user->can('reports.update');
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->can('reports.delete');
    }
}
