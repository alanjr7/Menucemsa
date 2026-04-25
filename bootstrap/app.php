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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'user.status' => \App\Http\Middleware\CheckUserStatus::class,
            'force.http' => \App\Http\Middleware\ForceHttp::class,
            'ip.access' => \App\Http\Middleware\CheckIpAccess::class,
        ]);
        
        // Aplicar el middleware de estado a todas las rutas web
        $middleware->web(\App\Http\Middleware\CheckUserStatus::class);
        
        // Forzar HTTP en desarrollo
        $middleware->web(\App\Http\Middleware\ForceHttp::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
