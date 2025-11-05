<?php

namespace App\Policies;

use App\Models\Staff as User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy 
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool { return $user->can('products.view'); }
    public function view(User $user, Product $model): bool { return $user->can('products.view'); }
    public function create(User $user): bool { return $user->can('products.create'); }
    public function update(User $user, Product $model): bool { return $user->can('products.update'); }
    public function delete(User $user, Product $model): bool { return $user->can('products.delete'); }
}
