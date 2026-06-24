<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register role middleware alias
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Trust all proxies for local PHP server
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Return JSON for API-like routes
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) => $request->expectsJson()
        );
    })
    ->create();
