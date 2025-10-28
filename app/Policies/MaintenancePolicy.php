<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MaintenancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('maintenances.view');
    }

    public function view(User $user, Maintenance $maintenance): bool
    {
        return $user->can('maintenances.view');
    }

    public function create(User $user): bool
    {
        return $user->can('maintenances.create');
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->can('maintenances.update');
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->can('maintenances.delete');
    }
}
