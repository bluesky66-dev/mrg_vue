<?php

namespace Momentum\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

/**
 * Laravel middleware that determines if the response should receive a fresh token.
 * 
 * @author prev-team
 * @copyright MRG <https://www.mrg.com/>
 * @version 0.2.4 
 */
class CreateFreshApiToken extends \Laravel\Passport\Http\Middleware\CreateFreshApiToken
{
    /**
     * Determine if the response should receive a fresh token.
     * @since 0.2.4
     *
     * @param \Illuminate\Http\Response $response
     * 
     * @return bool
     */
    protected function responseShouldReceiveFreshToken($response)
    {
        return $response instanceof Response && !$this->alreadyContainsToken($response);
    }
}
