<?php

namespace Momentum\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/**
 * Trims incoming request values.
 * Middleware provided by laravel.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     * @since 0.2.4
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
