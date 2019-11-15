<?php

namespace Momentum\Http\Middleware;

use Closure;
use Auth;
use App;

/**
 * Checks if there is a user authenticated to set the application's active report.
 * Request is aborted if no user nor report are found.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class SetActiveReport
{
    /**
     * Handle an incoming request.
     * @since 0.2.4
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * 
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $report = Auth::user()->getActiveReport();

        // if the user is authenticated but doesn't have an active report, we don't want them to login
        if (!$report) {
            Auth::logout();
            return redirect()->route('login', ['message' => trans('login.inactive')]);
        }

        App::instance('ActiveReport', $report);

        return $next($request);
    }
}
