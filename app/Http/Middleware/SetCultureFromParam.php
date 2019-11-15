<?php

namespace Momentum\Http\Middleware;

use Closure;
use Momentum\Culture;
use Momentum\Utilities\Localization;

/**
 * Sets application's culture (locale) based on request parameter.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class SetCultureFromParam
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
        if ($request->has('lang')) {
            $culture = Culture::enabled()->where('code', $request->get('lang'))->first();

            if (!$culture) {
                return $next($request);
            }

            Localization::setApplicationLocale($culture);
        }
        return $next($request);
    }
}
