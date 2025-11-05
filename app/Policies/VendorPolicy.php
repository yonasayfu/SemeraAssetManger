<?php

namespace App\Policies;

use App\Models\Staff as User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool { return $user->can('vendors.view'); }
    public function view(User $user, Vendor $model): bool { return $user->can('vendors.view'); }
    public function create(User $user): bool { return $user->can('vendors.create'); }
    public function update(User $user, Vendor $model): bool { return $user->can('vendors.update'); }
    public function delete(User $user, Vendor $model): bool { return $user->can('vendors.delete'); }
}
