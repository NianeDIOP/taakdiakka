<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Derrière un proxy / tunnel / load-balancer (Cloudflare, ngrok, Nginx…) :
        // lit X-Forwarded-Proto pour générer les bonnes URLs HTTPS (CSS, images, liens).
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\MaintenanceMode::class,
            \App\Http\Middleware\UpdateLastSeen::class,
            \App\Http\Middleware\RequireProfileComplete::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
            'super_admin' => \App\Http\Middleware\EnsureIsSuperAdmin::class,
            'module' => \App\Http\Middleware\EnsureModuleEnabled::class,
            'members_only' => \App\Http\Middleware\DenyAdminFromMemberArea::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
