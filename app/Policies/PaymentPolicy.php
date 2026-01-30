<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any payments.
     */
    public function viewAny(User $user): bool
    {
        // Members can view only their own payments, staff can view all
        return $user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader']) || $user->hasRole('member');
    }

    /**
     * Determine whether the user can view a specific payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader'])) {
            return true;
        }
        // Member can view only their own payment
        return $user->hasRole('member') && $user->member && $payment->member_id === $user->member->id;
    }

    /**
     * Determine whether the user can create a payment.
     */
    public function create(User $user): bool
    {
        // Only regular members can create their own payments via the online form
        return $user->hasRole('member');
    }

    /**
     * Determine whether the user can delete a payment.
     */
    public function delete(User $user, Payment $payment): bool
    {
        // Only staff can delete payments (e.g., refunds)
        return $user->hasAnyRole(['super_admin', 'admin', 'treasurer']);
    }
}
