<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Cateura Accesorios' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#fdf6f0; font-family: Arial, Helvetica, sans-serif; color:#292524;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fdf6f0; padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; max-width:600px; width:100%;">
                <tr>
                    <td style="background-color:#b5521a; padding:24px 32px;">
                        <span style="color:#ffffff; font-size:20px; font-weight:bold;">Cateura Accesorios</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        {{ $slot }}
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 32px; background-color:#f5f0e8; font-size:12px; color:#78716c;">
                        Asociación Mujeres Unidas del Bañado Sur · Este correo fue enviado automáticamente, por favor no respondas directamente a esta dirección.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
