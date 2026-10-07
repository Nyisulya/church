<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts users holding only the "accountant" (Mhasibu) role to the
 * income/contributions and members modules.
 *
 * The accountant role is meant to be a lightweight, income-only experience:
 * it can record and review giving (sadaka/zaka) and view members, but must
 * not be able to reach any other part of the system, even by typing a URL.
 */
class RestrictAccountantAccess
{
    /**
     * Route name prefixes the accountant role is allowed to access.
     *
     * @var array<int, string>
     */
    protected array $allowedPrefixes = [
        'profile.',
        'inbox.',
        'contributions.',
        'financial.',
    ];

    /**
     * Exact route names the accountant role is allowed to access.
     *
     * @var array<int, string>
     */
    protected array $allowedRoutes = [
        'dashboard',
        'members.index',
        'members.show',
        'logout',
        'lang.switch',
        'language.switch',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Guests are handled by the auth middleware.
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Only applies to the accountant role.
        if (!$user->hasRole('accountant')) {
            return $next($request);
        }

        // Users who also hold a broader role keep their full access.
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor', 'treasurer', 'department_leader'])) {
            return $next($request);
        }

        $routeName = $request->route() ? $request->route()->getName() : null;

        // Allow unnamed routes (e.g. Livewire endpoints) to pass through.
        if ($routeName === null) {
            return $next($request);
        }

        if (in_array($routeName, $this->allowedRoutes, true)) {
            return $next($request);
        }

        foreach ($this->allowedPrefixes as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                return $next($request);
            }
        }

        return redirect()->route('financial.dashboard')
            ->with('warning', __('You do not have permission to access that section.'));
    }
}
