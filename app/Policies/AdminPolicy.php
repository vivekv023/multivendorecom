<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function manageVendors(User $user): bool
    {
        return $user->isAdmin();
    }

    public function manageProducts(User $user): bool
    {
        return $user->isAdmin();
    }

    public function manageUsers(User $user): bool
    {
        return $user->isAdmin();
    }
}
