<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Yuntas Publicidad' }}</title>
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#f3f4f6;
    font-family:Arial, Helvetica, sans-serif;
">

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="
            background:#f3f4f6;
            padding:40px 16px;
        "
    >
        <tr>
            <td align="center">

                <!-- CONTAINER -->

                <table
                    width="100%"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    style="
                        max-width:640px;
                        background:#ffffff;
                        border-radius:18px;
                        overflow:hidden;
                        box-shadow:0 10px 30px rgba(0,0,0,0.08);
                    "
                >

                    <!-- HERO IMAGE -->

                    @if($image_url)

                        <tr>
                            <td>

                                <img
                                    src="{{ $image_url }}"
                                    alt="Yuntas Publicidad"
                                    width="640"
                                    style="
                                        width:100%;
                                        height:auto;
                                        display:block;
                                        object-fit:cover;
                                    "
                                >

                            </td>
                        </tr>

                    @endif

                    <!-- CONTENT -->

                    <tr>
                        <td
                            style="
                                padding:40px 36px 30px;
                            "
                        >

                            <!-- BRAND -->

                            <p style="
                                margin:0 0 14px;
                                font-size:12px;
                                letter-spacing:1.4px;
                                text-transform:uppercase;
                                color:#6b7280;
                                font-weight:700;
                            ">
                                Yuntas Publicidad
                            </p>

                            <!-- BODY -->

                            <div style="
                                font-size:16px;
                                line-height:1.8;
                                color:#111827;
                            ">

                                {!! $content !!}

                            </div>

                            <!-- CTA -->

                            @if($cta_url)

                                <table
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        margin-top:32px;
                                    "
                                >
                                    <tr>
                                        <td
                                            align="center"
                                            bgcolor="#111827"
                                            style="
                                                border-radius:12px;
                                            "
                                        >

                                            <a
                                                href="{{ $cta_url }}"
                                                target="_blank"
                                                style="
                                                    display:inline-block;
                                                    padding:14px 28px;
                                                    font-size:14px;
                                                    font-weight:700;
                                                    color:#ffffff;
                                                    text-decoration:none;
                                                "
                                            >
                                                {{ $cta_text ?? 'Ver más' }}
                                            </a>

                                        </td>
                                    </tr>
                                </table>

                            @endif

                        </td>
                    </tr>

                    <!-- DIVIDER -->

                    <tr>
                        <td style="padding:0 36px;">

                            <div style="
                                height:1px;
                                background:#e5e7eb;
                                width:100%;
                            "></div>

                        </td>
                    </tr>

                    <!-- FOOTER -->

                    <tr>
                        <td
                            style="
                                padding:24px 36px 36px;
                            "
                        >

                            <p style="
                                margin:0;
                                font-size:12px;
                                line-height:1.7;
                                color:#6b7280;
                            ">
                                Este mensaje fue enviado automáticamente por
                                <strong style="color:#111827;">
                                    Yuntas Publicidad
                                </strong>.
                            </p>

                            <p style="
                                margin:12px 0 0;
                                font-size:11px;
                                color:#9ca3af;
                            ">
                                © {{ date('Y') }} Yuntas Publicidad.
                                Todos los derechos reservados.
                            </p>

                        </td>
                    </tr>

                </table>

                <!-- END CONTAINER -->

            </td>
        </tr>
    </table>

</body>
</html>
