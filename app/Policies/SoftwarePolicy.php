<?php

namespace App\Policies;

use App\Models\Staff as User;
use App\Models\Software;
use Illuminate\Auth\Access\HandlesAuthorization;

class SoftwarePolicy 
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool { return $user->can('software.view'); }
    public function view(User $user, Software $model): bool { return $user->can('software.view'); }
    public function create(User $user): bool { return $user->can('software.create'); }
    public function update(User $user, Software $model): bool { return $user->can('software.update'); }
    public function delete(User $user, Software $model): bool { return $user->can('software.delete'); }
}
