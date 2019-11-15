<?php

namespace Momentum\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;

/**
 * Validates current request's agent and checks if a redirection
 * is needed based on the provided subdomain.
 * 
 * @author Ale Mostajo <info@10quality.com>
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.5
 */
class RedirectSubdomain
{
    /**
     * Handle an incoming request.
     * @since 0.2.5
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * 
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $agent = new Agent();
        // Obtain subdomain on development environments.
        if (config('app.debug')
            && !isset($request->subdomain)
            && preg_match(
                '/'.preg_replace(['/http(|s)\:\/\//', '/\//', '/\{subdomain\}/'], ['','','[a-zA-Z0-9]+'], config('app.domains.subdomain')).'/',
                $request->server('HTTP_HOST'),
                $matches
            )
        ) {
            $subdomain = str_replace(
                preg_replace('/http(|s)\:\/\/|\/|\{subdomain\}/', '', config('app.domains.subdomain')),
                '', 
                $matches[0]
            );
            if ($subdomain !== 'www')
                $request->subdomain = $subdomain;
        }
        $http = $request->isSecure() ? 'https://' : 'http://';
        // Redirect to desktop
        if ($agent->isDesktop()
            && isset($request->subdomain)
            && $request->subdomain === config('app.subdomains.mobile')
        ) {
            return redirect($http.config('app.domains.www').'/'.$request->path());
        }
        // Redirect to mobile
        if (!$agent->isDesktop()
            && (
                !isset($request->subdomain)
                || $request->subdomain !== config('app.subdomains.mobile')
            )
        ) {
            return redirect(
                $http.str_replace('{subdomain}', config('app.subdomains.mobile'), config('app.domains.subdomain'))
                    .'/'.$request->path()
            );
        }
        // Process request
        return $next($request);
    }
}
