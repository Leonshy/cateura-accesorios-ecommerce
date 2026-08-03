@component('emails.layout')
    <h1 style="font-size:20px; margin:0 0 8px;">¡Ya casi! Confirmá tu suscripción</h1>
    <p style="font-size:14px; color:#57534e; margin:0 0 24px;">Pediste suscribirte al newsletter de Cateura Accesorios. Para empezar a recibir novedades, confirmá tu correo tocando el botón de abajo.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <a href="{{ $confirmUrl }}" style="display:inline-block; background-color:#b5521a; color:#ffffff; text-decoration:none; font-weight:bold; font-size:15px; padding:14px 32px; border-radius:6px;">
                    Confirmar mi suscripción
                </a>
            </td>
        </tr>
    </table>

    <p style="font-size:13px; color:#78716c; margin:0 0 8px;">Este enlace vence en <strong>72 horas</strong>. Si no confirmás dentro de ese plazo, tu correo se elimina automáticamente de la lista y no vas a recibir nada.</p>
    <p style="font-size:13px; color:#78716c; margin:0;">Si no pediste esta suscripción, simplemente ignorá este correo.</p>
@endcomponent
