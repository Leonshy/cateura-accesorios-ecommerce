<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * El stock se descuenta al crearse el pedido (ver CheckoutController::store),
     * no al confirmarse el pago. Si un pedido termina cancelado —ya sea porque el
     * pago fue rechazado o porque un administrador lo cancela manualmente— ese
     * stock hay que devolverlo. Se protege contra restaurarlo dos veces
     * comparando el estado anterior: una vez que el pedido ya está "cancelado",
     * una actualización repetida (reintento de webhook, doble clic, etc.) no
     * debe volver a sumar el stock.
     */
    public static function restoreIfNewlyCancelled(Order $order, string $previousStatus, string $newStatus): void
    {
        if ($newStatus !== 'cancelado' || $previousStatus === 'cancelado') {
            return;
        }

        $order->loadMissing('items');

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                Product::whereKey($item->product_id)->lockForUpdate()->increment('stock', $item->quantity);
            }
        });
    }
}
