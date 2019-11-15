<?php

namespace Momentum\Http\Middleware;

use Closure;
use Auth;
use Momentum\Culture;
use Momentum\Utilities\Localization;

/**
 * Sets application's culture (locale) based on cookie.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class SetCultureFromCookie
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
        $cookie = $request->cookie('culture');
        if (!$cookie) {
            return $next($request);
        }

        $culture = Culture::find($cookie);

        if (!$culture) {
            return $next($request);
        }

        Localization::setApplicationLocale($culture);

        return $next($request);
    }
}
