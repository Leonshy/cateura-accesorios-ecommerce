<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recibimos tu pedido ' . $this->order->order_number . ' — Cateura Accesorios',
        );
    }

    public function content(): Content
    {
        $transferMethod = $this->order->payment_method === 'transferencia'
            ? PaymentMethod::where('key', 'transferencia')->first()
            : null;

        return new Content(
            view: 'emails.order-confirmed',
            with: ['transferMethod' => $transferMethod],
        );
    }
}
