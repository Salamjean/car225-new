<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
             '/payment/callback', // <--- AJOUTER CETTE LIGNE
            '/api/*', // Exclu toutes les routes API du CSRF (pour mobile app)
            '/user/payment/notify',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'compagnie' => \App\Http\Middleware\CompagnieMiddleware::class,
            'sapeur_pompier' => \App\Http\Middleware\SapeurPompierMiddleware::class,
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
            'caisse' => \App\Http\Middleware\CaisseMiddleware::class,
            'hotesse' => \App\Http\Middleware\HotesseMiddleware::class,
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
