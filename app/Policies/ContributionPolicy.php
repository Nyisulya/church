<?php

namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContributionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view the index (filtered by controller)
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contribution $contribution): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer'])) {
            return true;
        }
        
        // Members can only view their own
        return $user->member && $user->member->id === $contribution->member_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contribution $contribution): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'treasurer']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contribution $contribution): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }
}
