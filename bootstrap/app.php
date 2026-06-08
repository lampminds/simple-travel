<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Livewire\Mechanisms\HandleComponents\MaxNestingDepthExceededException;
use Symfony\Component\HttpFoundation\Response;

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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (MaxNestingDepthExceededException $e, Request $request): ?Response {
            if (! $request->is('livewire/update')) {
                return null;
            }

            return response()->json([
                'message' => __('filament.resources.cat_helper_fields.text_nesting_depth_exceeded'),
            ], 422);
        });
    })->create();
