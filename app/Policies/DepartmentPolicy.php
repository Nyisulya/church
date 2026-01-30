<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    /**
     * Determine whether the user can view any departments.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('department-view');
    }

    /**
     * Determine whether the user can view the department.
     */
    public function view(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('department-view');
    }

    /**
     * Determine whether the user can create departments.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('department-create');
    }

    /**
     * Determine whether the user can update the department.
     */
    public function update(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('department-edit');
    }

    /**
     * Determine whether the user can delete the department.
     */
    public function delete(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('department-delete');
    }
}
