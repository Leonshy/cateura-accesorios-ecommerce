<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Los suscriptores del newsletter tienen 72hs para confirmar su correo; se
// revisa cada hora para que ninguno quede colgado mucho más de ese plazo.
Schedule::command('app:purge-unconfirmed-newsletter-subscribers')->hourly();

// Los carritos de invitados (sin cuenta) no deben acumularse indefinidamente;
// se revisan una vez al día y se eliminan los que llevan más de 5 días
// sin actividad.
Schedule::command('app:prune-abandoned-guest-carts')->daily();
