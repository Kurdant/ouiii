<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        // Redirige les invités vers /login pour toute route protégée.
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Trust Traefik/nginx en frontal : récupère scheme/host/IP via X-Forwarded-*.
        // Sans cette ligne, Laravel génère des URLs en http:// derrière un proxy HTTPS.
        // PHP-FPM n'est joignable que depuis le réseau interne Docker → '*' est sûr.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
