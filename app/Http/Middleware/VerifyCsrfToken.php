<?php

namespace Momentum\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

/**
 * Verifies CSRF tokens in requests.
 * Middleware provided by laravel.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * @since 0.2.4
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
