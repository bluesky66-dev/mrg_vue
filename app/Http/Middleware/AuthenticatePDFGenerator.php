<?php

namespace Momentum\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Momentum\PdfToken;
use Momentum\User;

/**
 * Validates if the request contains a valid pdf-token.
 * Request is validated if exists and token is deleted.
 * Invalid tokens will abort the request.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class AuthenticatePDFGenerator
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
        if (!$request->has('pdf-token')) {
            abort(401);
        }

        $token = $request->get('pdf-token');

        $route = $request->route()->getName();

        $auth_token = PdfToken::where('token', $token)->where('route', $route)->whereNull('deleted_at')->first();

        if (!$auth_token) {
            abort(401);
        }

        $auth_token->used_at = Carbon::now();
        $auth_token->save();

        // the user is authorized, so let them through and then delete the auth token
        $auth_token->delete();

        return $next($request);
    }
}
