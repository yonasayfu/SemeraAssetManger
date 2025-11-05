<?php

namespace App\Policies;

use App\Models\Staff as User;
use App\Models\PurchaseOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy 
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool { return $user->can('purchase-orders.view'); }
    public function view(User $user, PurchaseOrder $model): bool { return $user->can('purchase-orders.view'); }
    public function create(User $user): bool { return $user->can('purchase-orders.create'); }
    public function update(User $user, PurchaseOrder $model): bool { return $user->can('purchase-orders.update'); }
    public function delete(User $user, PurchaseOrder $model): bool { return $user->can('purchase-orders.delete'); }
}
