<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for guests
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Skip check if no member profile
        if (!$user->member) {
            return $next($request);
        }

        // Whitelist routes that should not trigger the check
        $whitelistedRoutes = [
            'profile.index',
            'profile.edit',
            'profile.update',
            'logout',
        ];

        // Skip check if on whitelisted routes
        if ($request->routeIs($whitelistedRoutes)) {
            return $next($request);
        }

        // Check if profile is complete
        if (!$user->member->isProfileComplete()) {
            $missingFields = $user->member->getMissingFields();
            
            return redirect()->route('profile.edit')
                ->with('warning', 'Please complete your profile before accessing the system. Missing: ' . implode(', ', $missingFields));
        }

        return $next($request);
    }
}
