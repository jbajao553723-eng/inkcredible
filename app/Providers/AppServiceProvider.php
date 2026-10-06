<?php

namespace App\Providers;

use App\Filesystems\DatabaseFilesystemAdapter;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Filesystem\FilesystemAdapter as LaravelFilesystemAdapter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('database', function ($app, array $config): LaravelFilesystemAdapter {
            $adapter = new DatabaseFilesystemAdapter($config['name']);

            return new LaravelFilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config,
            );
        });

        Paginator::useBootstrapFive();

        Event::listen(Login::class, function (Login $event): void {
            AuditLog::record('login', 'Signed in successfully', $event->user instanceof User ? $event->user : null);
        });

        Event::listen(Failed::class, function (Failed $event): void {
            AuditLog::record('login_failed', 'Failed sign-in attempt', null, [
                'email' => mb_substr((string) ($event->credentials['email'] ?? ''), 0, 255),
            ]);
        });

        Event::listen(Logout::class, function (Logout $event): void {
            AuditLog::record('logout', 'Signed out', $event->user instanceof User ? $event->user : null);
        });

        Event::listen(Registered::class, function (Registered $event): void {
            AuditLog::record('account_registered', 'Created a client account', $event->user instanceof User ? $event->user : null);
        });

        Event::listen(PasswordReset::class, function (PasswordReset $event): void {
            AuditLog::record('password_reset', 'Reset account password through email recovery', $event->user instanceof User ? $event->user : null);
        });
    }
}
