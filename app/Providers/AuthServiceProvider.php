<?php

namespace App\Providers;

use App\Models\Member;
use App\Policies\MemberPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Member::class => MemberPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Event::class => \App\Policies\EventPolicy::class,
        \App\Models\Department::class => \App\Policies\DepartmentPolicy::class,
        \App\Models\Transaction::class => \App\Policies\TransactionPolicy::class,
        \App\Models\Pledge::class => \App\Policies\PledgePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Role‑based gates
        // Role‑based gates -> Permission-based gates
        Gate::define('approve-members', function ($user) {
            return $user->hasPermissionTo('member-edit');
        });

        Gate::define('manage-departments', function ($user) {
            return $user->hasPermissionTo('department-edit');
        });

        Gate::define('view-finances', function ($user) {
            return $user->hasPermissionTo('finance-view');
        });

        Gate::define('send-messages', function ($user) {
            return $user->hasPermissionTo('communication-create');
        });
    }
}
