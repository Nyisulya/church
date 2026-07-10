<?php

namespace App\Policies;

use App\Models\Pledge;
use App\Models\User;

class PledgePolicy
{
    /**
     * Determine whether the user can view any pledges.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('pledge-view');
    }

    /**
     * Determine whether the user can view the pledge.
     */
    public function view(User $user, Pledge $pledge): bool
    {
        return $user->hasPermissionTo('pledge-view') || $user->id === $pledge->user_id;
    }

    /**
     * Determine whether the user can create pledges.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('pledge-create');
    }

    public function update(User $user, Pledge $pledge): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'pastor']);
    }

    /**
     * Determine whether the user can delete the pledge.
     */
    public function delete(User $user, Pledge $pledge): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'pastor']);
    }
}
