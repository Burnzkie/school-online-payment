<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (required for Render/Cloudflare HTTPS to work correctly)
        $middleware->trustProxies(at: '*');
        $middleware->trustHosts(at: ['school-online-payment-1.onrender.com']);

        // ── Custom middleware aliases ──────────────────────────────────────
        $middleware->alias([
            'ensure.hs'        => \App\Http\Middleware\EnsureHighSchoolStudent::class,
            'ensure.college'   => \App\Http\Middleware\EnsureCollegeStudent::class,
            'ensure.admin'     => \App\Http\Middleware\EnsureAdmin::class,
            'ensure.cashier'   => \App\Http\Middleware\EnsureCashier::class,
            'ensure.treasurer' => \App\Http\Middleware\EnsureTreasurer::class,
            'ensure.parent'    => \App\Http\Middleware\EnsureParent::class,
            'ensure.active'    => \App\Http\Middleware\EnsureStudentIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();