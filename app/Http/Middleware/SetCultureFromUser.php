<?php

namespace Momentum\Http\Middleware;

use Closure;
use Auth;
use Momentum\Utilities\Localization;

/**
 * Sets application's culture (locale) based on user's culture.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class SetCultureFromUser
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

        $culture = Auth::user()->culture;

        if (!$culture || !$culture->enabled) {
            Localization::setApplicationLocale(Culture::where('code', 'en_US')->get()->first());
            return $next($request);
        }

        Localization::setApplicationLocale($culture);

        return $next($request);
    }
}
