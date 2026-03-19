<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(Login::class, function (Login $event): void {
            activity('auth')
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->event('login')
                ->withProperties([
                    'guard' => $event->guard,
                    'ip' => request()->ip(),
                ])
                ->log('User logged in');
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if (! $event->user) {
                return;
            }

            activity('auth')
                ->causedBy($event->user)
                ->performedOn($event->user)
                ->event('logout')
                ->withProperties([
                    'guard' => $event->guard,
                    'ip' => request()->ip(),
                ])
                ->log('User logged out');
        });

        Event::listen(Failed::class, function (Failed $event): void {
            $activity = activity('auth')
                ->event('login_failed')
                ->withProperties([
                    'guard' => $event->guard,
                    'email' => $event->credentials['email'] ?? null,
                    'ip' => request()->ip(),
                ]);

            if ($event->user) {
                $activity
                    ->causedBy($event->user)
                    ->performedOn($event->user);
            }

            $activity->log('User login failed');
        });
    }
}
