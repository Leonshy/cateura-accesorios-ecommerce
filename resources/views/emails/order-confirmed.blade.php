@component('emails.layout')
    <h1 style="font-size:20px; margin:0 0 8px;">¡Gracias por tu compra, {{ $order->customer_name }}!</h1>
    <p style="font-size:14px; color:#57534e; margin:0 0 20px;">Recibimos tu pedido y ya lo estamos procesando.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf6f0; border-radius:6px; margin-bottom:20px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 4px; font-size:12px; color:#78716c; text-transform:uppercase; letter-spacing:.05em;">Número de pedido</p>
                <p style="margin:0; font-size:18px; font-weight:bold; font-family:monospace;">{{ $order->order_number }}</p>
            </td>
        </tr>
    </table>

    <h2 style="font-size:15px; margin:0 0 10px; border-bottom:1px solid #e7e0d5; padding-bottom:8px;">Productos</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:16px; font-size:14px;">
        @foreach($order->items as $item)
        <tr>
            <td style="padding:6px 0; border-bottom:1px solid #f0ece2;">
                {{ $item->product_name }}
                @if($item->color)<span style="color:#78716c;"> ({{ $item->color }})</span>@endif
                <span style="color:#78716c;"> × {{ $item->quantity }}</span>
            </td>
            <td style="padding:6px 0; border-bottom:1px solid #f0ece2; text-align:right; white-space:nowrap;">
                Gs. {{ number_format($item->subtotal, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        <tr>
            <td style="padding:10px 0 0; text-align:right; font-weight:bold;">Total</td>
            <td style="padding:10px 0 0; text-align:right; font-weight:bold; color:#b5521a;">Gs. {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($order->shipping_method === 'retiro_tienda')
    <p style="font-size:14px;"><strong>Entrega:</strong> retiro en el local.</p>
    @else
    <p style="font-size:14px; margin-bottom:4px;"><strong>Dirección de envío:</strong></p>
    <p style="font-size:14px; color:#57534e; margin-top:0;">{{ $order->address_line1 }}, {{ $order->address_city }}, {{ $order->address_department }}</p>
    @endif

    @if($transferMethod)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef3e2; border-radius:6px; margin-top:20px;">
        <tr>
            <td style="padding:16px 20px; font-size:13px; color:#92400e;">
                <p style="margin:0 0 8px; font-weight:bold;">📋 Datos para transferencia bancaria</p>
                <p style="margin:0;"><strong>Banco:</strong> {{ $transferMethod->credentials['bank_name'] ?? '—' }}</p>
                <p style="margin:0;"><strong>Cuenta:</strong> {{ $transferMethod->credentials['bank_account'] ?? '—' }}</p>
                <p style="margin:0;"><strong>Titular:</strong> {{ $transferMethod->credentials['bank_titular'] ?? '—' }}</p>
                <p style="margin:0;"><strong>CI titular:</strong> {{ $transferMethod->credentials['bank_ci'] ?? '—' }}</p>
                <p style="margin:8px 0 0;">Ya recibimos tu comprobante y confirmaremos el pago a la brevedad.</p>
            </td>
        </tr>
    </table>
    @endif

    <p style="font-size:13px; color:#78716c; margin-top:24px;">Nos pondremos en contacto para coordinar el envío. Ante cualquier consulta, escribinos respondiendo a este correo o por WhatsApp.</p>
@endcomponent
