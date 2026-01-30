<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any transactions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('finance-view');
    }

    /**
     * Determine whether the user can view the transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('finance-view');
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('finance-create');
    }

    /**
     * Determine whether the user can update the transaction.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('finance-edit');
    }

    /**
     * Determine whether the user can delete the transaction.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('finance-delete');
    }
}
