<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SitePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('sites.view');
    }

    public function view(User $user, Site $site): bool
    {
        return $user->can('sites.view');
    }

    public function create(User $user): bool
    {
        return $user->can('sites.create');
    }

    public function update(User $user, Site $site): bool
    {
        return $user->can('sites.update');
    }

    public function delete(User $user, Site $site): bool
    {
        return $user->can('sites.delete');
    }
}
