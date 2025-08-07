<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

// Middleware que vous avez déjà
use App\Http\Middleware\HandleInertiaRequests;

// Middleware Laravel/Core
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
            // Inertia pour vos pages SPA
            HandleInertiaRequests::class,

            // Binding automatique des route-model
            SubstituteBindings::class,
        ],

        'api' => [
            // Limiteur standard Laravel
            'throttle:api',

            // Binding automatique
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
