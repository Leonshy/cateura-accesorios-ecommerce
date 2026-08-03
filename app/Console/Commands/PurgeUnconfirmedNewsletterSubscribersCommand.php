<?php

namespace App\Console\Commands;

use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;

class PurgeUnconfirmedNewsletterSubscribersCommand extends Command
{
    protected $signature = 'app:purge-unconfirmed-newsletter-subscribers';

    protected $description = 'Elimina suscriptores del newsletter que no confirmaron su correo dentro de las 72 horas.';

    public function handle(): int
    {
        $deleted = NewsletterSubscriber::pendingConfirmation()
            ->where('created_at', '<', now()->subHours(72))
            ->delete();

        $this->info("Eliminados {$deleted} suscriptores sin confirmar (más de 72hs).");

        return self::SUCCESS;
    }
}
