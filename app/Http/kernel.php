<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;


use App\Http\Middleware\HandleInertiaRequests;


use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class Kernel extends HttpKernel
{
    /**
     * Groupes de middleware.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            HandleInertiaRequests::class,
            SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',

            SubstituteBindings::class,
        ],
    ];

    /**
     * Alias de middleware utilisables dans les routes.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // Authentification (web guard ou API token)
        'auth'         => \Illuminate\Auth\Middleware\Authenticate::class,

        // Authentification Sanctum pour SPA
        'auth:sanctum' => EnsureFrontendRequestsAreStateful::class,

        // Empêche d’accéder à la page de login si déjà connecté
        'guest'        => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
    ];
}
