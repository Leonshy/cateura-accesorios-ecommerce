<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
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
        // Laravel 11+ ya no trae un EventServiceProvider por defecto, así que
        // este listener (que envía el correo de verificación al registrarse)
        // hay que engancharlo a mano; sin esto, User::implements MustVerifyEmail
        // no alcanza para que el correo realmente se envíe.
        Event::listen(Registered::class, SendEmailVerificationNotification::class);
    }
}
