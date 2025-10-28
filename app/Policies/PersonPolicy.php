<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('persons.view');
    }

    public function view(User $user, Person $person): bool
    {
        return $user->can('persons.view');
    }

    public function create(User $user): bool
    {
        return $user->can('persons.create');
    }

    public function update(User $user, Person $person): bool
    {
        return $user->can('persons.update');
    }

    public function delete(User $user, Person $person): bool
    {
        return $user->can('persons.delete');
    }
}
