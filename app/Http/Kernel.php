<?php

namespace Momentum\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Momentum\Http\Middleware\AuthenticatePDFGenerator;
use Momentum\Http\Middleware\CreateFreshApiToken;
use Momentum\Http\Middleware\SetActiveReport;
use Momentum\Http\Middleware\SetCultureFromParam;
use Momentum\Http\Middleware\SetCultureFromUser;
use Momentum\Http\Middleware\SetCultureFromCookie;
use Momentum\Http\Middleware\RedirectSubdomain;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Momentum\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Momentum\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Momentum\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Momentum\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            CreateFreshApiToken::class,
            SetActiveReport::class,
            SetCultureFromCookie::class,
            SetCultureFromUser::class,
            SetCultureFromParam::class,
            RedirectSubdomain::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
            SetActiveReport::class,
            SetCultureFromUser::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'              => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic'        => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'          => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'               => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'             => \Momentum\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'          => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'permission'        => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role'              => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'report'            => SetActiveReport::class,
        'culture.auth'      => SetCultureFromUser::class,
        'culture.cookie'    => SetCultureFromCookie::class,
        'culture.param'     => SetCultureFromParam::class,
        'auth.pdf'          => AuthenticatePDFGenerator::class,
    ];
}
