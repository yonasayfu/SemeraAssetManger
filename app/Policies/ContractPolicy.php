<?php

namespace App\Policies;

use App\Models\Staff as User;
use App\Models\Contract;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy 
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool { return $user->can('contracts.view'); }
    public function view(User $user, Contract $model): bool { return $user->can('contracts.view'); }
    public function create(User $user): bool { return $user->can('contracts.create'); }
    public function update(User $user, Contract $model): bool { return $user->can('contracts.update'); }
    public function delete(User $user, Contract $model): bool { return $user->can('contracts.delete'); }
}
