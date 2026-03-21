<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine ?? 'Mensaje' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;font-family:Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;">
                @if(!empty($imageUrl))
                    <tr>
                        <td style="padding:0;">
                            <img src="{{ $imageUrl }}" alt="Imagen" style="display:block;width:100%;height:auto;border:0;">
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding:24px;line-height:1.6;font-size:16px;">
                        {!! $bodyHtml !!}
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 24px 24px 24px;font-size:12px;line-height:1.5;color:#6b7280;">
                        Este correo fue enviado automaticamente por Yuntas Publicidad.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
