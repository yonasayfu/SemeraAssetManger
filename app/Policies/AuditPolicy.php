<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\Staff as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('audits.view');
    }

    public function view(User $user, Audit $audit): bool
    {
        return $user->can('audits.view');
    }

    public function create(User $user): bool
    {
        return $user->can('audits.create');
    }

    public function update(User $user, Audit $audit): bool
    {
        return $user->can('audits.update');
    }

    public function delete(User $user, Audit $audit): bool
    {
        return $user->can('audits.delete');
    }
}
