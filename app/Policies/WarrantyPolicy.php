<?php

namespace App\Policies;

use App\Models\Staff as User;
use App\Models\Warranty;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarrantyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('warranties.view');
    }

    public function view(User $user, Warranty $warranty): bool
    {
        return $user->can('warranties.view');
    }

    public function create(User $user): bool
    {
        return $user->can('warranties.create');
    }

    public function update(User $user, Warranty $warranty): bool
    {
        return $user->can('warranties.update');
    }

    public function delete(User $user, Warranty $warranty): bool
    {
        return $user->can('warranties.delete');
    }
}
