<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
{
    /**
     * Determine whether the user can view any purchases.
     * Only Admin and Staff can view Stock-In transactions.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine whether the user can view the specific purchase.
     */
    public function view(User $user, Purchase $purchase): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine whether the user can create purchases.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine whether the user can cancel the purchase.
     * Only Admin can cancel purchases.
     */
    public function cancel(User $user, Purchase $purchase): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the purchase.
     */
    public function delete(User $user, Purchase $purchase): bool
    {
        return $user->isAdmin();
    }
}
