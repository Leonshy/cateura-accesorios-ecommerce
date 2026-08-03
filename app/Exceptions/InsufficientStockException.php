<?php

namespace App\Exceptions;

use Exception;

/**
 * Se lanza al confirmar un pedido cuando, al bloquear la fila del producto
 * (lockForUpdate), el stock real ya no alcanza para la cantidad pedida —
 * típicamente porque otra compra concurrente se lo llevó primero.
 */
class InsufficientStockException extends Exception
{
    public function __construct(public readonly string $productName)
    {
        parent::__construct("Ya no queda stock suficiente de \"{$productName}\".");
    }
}
