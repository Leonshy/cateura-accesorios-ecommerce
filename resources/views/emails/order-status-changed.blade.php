@component('emails.layout')
    <h1 style="font-size:20px; margin:0 0 8px;">Hola {{ $order->customer_name }},</h1>
    <p style="font-size:14px; color:#57534e; margin:0 0 20px;">Tu pedido tuvo una actualización.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf6f0; border-radius:6px; margin-bottom:20px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 4px; font-size:12px; color:#78716c; text-transform:uppercase; letter-spacing:.05em;">Pedido</p>
                <p style="margin:0 0 12px; font-size:18px; font-weight:bold; font-family:monospace;">{{ $order->order_number }}</p>
                <p style="margin:0 0 4px; font-size:12px; color:#78716c; text-transform:uppercase; letter-spacing:.05em;">Nuevo estado</p>
                <p style="margin:0; font-size:16px; font-weight:bold; color:#b5521a;">{{ $order->status_label }}</p>
                <p style="margin:8px 0 0; font-size:13px; color:#57534e;">Estado del pago: {{ $order->payment_status_label }}</p>
            </td>
        </tr>
    </table>

    <p style="font-size:13px; color:#78716c;">Podés ver el detalle completo de tu pedido ingresando a tu cuenta. Ante cualquier consulta, escribinos respondiendo a este correo o por WhatsApp.</p>
@endcomponent
