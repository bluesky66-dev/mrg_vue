<?php

namespace Momentum\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Validates if there is a user authenticated and redirects it to the dashboard.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * @since 0.2.4
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     * 
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
