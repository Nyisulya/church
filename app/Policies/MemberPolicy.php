<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any members.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('member-view');
    }

    /**
     * Determine whether the user can view a specific member.
     */
    public function view(User $user, Member $member)
    {
        // All authenticated users can view member profiles
        return true;
    }

    /**
     * Determine whether the user can create members.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('member-create');
    }

    /**
     * Determine whether the user can update the member.
     */
    public function update(User $user, Member $member)
    {
        // Global edit permission (covers Super Admin, Admin, Pastor if assigned)
        if ($user->hasPermissionTo('member-edit')) {
            return true;
        }

        // Department leader can update members in their department
        // We keep this logic but ensure they have the role (or we could add a specific permission for this)
        if ($user->hasRole('department_leader')) {
            $userDepartmentIds = $user->member ? $user->member->departments->pluck('id') : collect();
            if ($member->departments->whereIn('id', $userDepartmentIds)->isNotEmpty()) {
                return true;
            }
        }

        // Member can update own profile (limited fields)
        return $user->id === $member->user_id;
    }

    /**
     * Determine whether the user can delete the member.
     */
    public function delete(User $user, Member $member)
    {
        return $user->hasPermissionTo('member-delete');
    }
}
