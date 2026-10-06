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
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->trustProxies(at: '*');

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
        $exceptions->report(function (\Throwable $exception): void {
            Log::error('Unhandled application exception.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        });

        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            if ($request->is('profile/verification*')) {
                return response()->view('errors.upload-too-large', status: 413);
            }

            return null;
        });
    })->create();
