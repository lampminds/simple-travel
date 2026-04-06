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
        $middleware->web(prepend: [
            \App\Http\Middleware\ResolveTenantWebsiteAccount::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetPermissionsTeamForRequest::class,
            \App\Http\Middleware\SetLocaleFromSession::class,
            \App\Http\Middleware\TrackUserPresence::class,
            \App\Http\Middleware\RecordLastLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
