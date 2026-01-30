<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('user-view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user-view') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('user-create');
    }

    public function update(User $user, User $model): bool
    {
        // Users can update themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Otherwise check permission
        if ($user->hasPermissionTo('user-edit')) {
            // Prevent non-super-admins from editing super-admins
            if ($model->hasRole('super_admin') && !$user->hasRole('super_admin')) {
                return false;
            }
            return true;
        }

        return false;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->hasPermissionTo('user-delete')) {
            // Prevent deleting yourself
            if ($user->id === $model->id) {
                return false;
            }

            // Prevent non-super-admins from deleting super-admins
            if ($model->hasRole('super_admin') && !$user->hasRole('super_admin')) {
                return false;
            }
            
            return true;
        }

        return false;
    }
}
