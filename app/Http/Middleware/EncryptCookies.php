<?php

namespace Momentum\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/**
 * Laravel middleware used to encrypt cookies.
 * Middleware provided by laravel.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
