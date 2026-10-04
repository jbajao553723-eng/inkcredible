<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuditUserActions;
use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureClientIsVerified;
use App\Http\Middleware\EnsureEmailIsVerifiedWithOtp;
use App\Http\Middleware\StandardAdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->appendToGroup('web', AuditUserActions::class);
        $middleware->appendToGroup('web', EnsureAccountIsActive::class);

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'standard.admin' => StandardAdminMiddleware::class,
            'superadmin' => SuperAdminMiddleware::class,
            'client.verified' => EnsureClientIsVerified::class,
            'email.verified' => EnsureEmailIsVerifiedWithOtp::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
