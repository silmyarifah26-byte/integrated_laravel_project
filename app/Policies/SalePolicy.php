<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Determine whether the user can view any sales.
     * Admin and Staff can view all sales; Customers can view sales listing (filtered to their own).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the specific sale.
     * Admin & Staff can view any receipt; Customer can only view their own receipt.
     */
    public function view(User $user, Sale $sale): bool
    {
        if ($user->isAdmin() || $user->isStaff()) {
            return true;
        }

        return $sale->customer_id === $user->id || $sale->user_id === $user->id;
    }

    /**
     * Determine whether the user can create sales.
     * All authenticated users can create sales (Staff/Admin POS or Customer self-order).
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can cancel the sale.
     * Only Admin can cancel sales transactions.
     */
    public function cancel(User $user, Sale $sale): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the sale.
     */
    public function delete(User $user, Sale $sale): bool
    {
        return $user->isAdmin();
    }
}
