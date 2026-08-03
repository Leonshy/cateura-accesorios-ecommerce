<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Illuminate\Console\Command;

class PruneAbandonedGuestCartsCommand extends Command
{
    protected $signature = 'app:prune-abandoned-guest-carts';

    protected $description = 'Elimina carritos de invitados (sin usuario registrado) que no se actualizaron en los últimos 5 días.';

    public function handle(): int
    {
        // cart_items.cart_id tiene cascadeOnDelete(), así que borrar el
        // carrito ya se lleva sus ítems con él.
        $count = Cart::whereNull('user_id')
            ->where('updated_at', '<', now()->subDays(5))
            ->delete();

        $this->info("Eliminados {$count} carritos de invitado abandonados (más de 5 días sin actividad).");

        return self::SUCCESS;
    }
}
