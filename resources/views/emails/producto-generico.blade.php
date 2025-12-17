<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ $data['titulo'] }}</title>
    <style>
        /* Ajuste para que las imágenes secundarias midan lo mismo sin recortar en exceso */
        .img-grid {
            width: 100% !important;
            height: 250px !important; 
            object-fit: cover; 
            object-position: center; 
            display: block;
            border-radius: 12px;
        }

        @media only screen and (max-width: 600px) {
            .img-grid {
                height: 180px !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; width: 100%; max-width: 600px;">

                    {{-- Header dinámico con estilo Cursiva --}}
                    <tr>
                        <td align="center" style="background-color: #1e3a5f; color: white; padding: 25px 20px; font-size: 24px; font-weight: bold; font-style: italic; letter-spacing: 1px;">
                            {{ $data['titulo'] }}
                        </td>
                    </tr>

                    {{-- Imagen principal dinámica --}}
                    <tr>
                        <td style="padding: 0;">
                            <img src="{{ $data['imagen_principal'] }}" alt="{{ $data['titulo'] }}" width="600" style="width: 100%; height: auto; display: block; border: 0;">
                        </td>
                    </tr>

                    {{-- Descripción / Tagline --}}
                    <tr>
                        <td align="center" style="padding: 30px 20px 20px 20px; color: #333333; font-size: 20px; font-weight: bold;">
                            {{ $data['parrafo1'] }}
                        </td>
                    </tr>

                    {{-- Imágenes secundarias dinámicas (2 columnas igualadas) --}}
                    @if(isset($data['imagenes_secundarias']) && count($data['imagenes_secundarias']) > 0)
                    <tr>
                        <td align="center" style="padding: 10px 20px 30px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    @foreach($data['imagenes_secundarias'] as $index => $imagen)
                                        @if($index % 2 === 0 && $index !== 0)
                                            </tr><tr>
                                        @endif
                                        <td align="center" width="50%" style="padding: 8px;">
                                            <img src="{{ $imagen }}" 
                                                 alt="Galeía {{ $index }}" 
                                                 class="img-grid"
                                                 style="width: 100%; height: 250px; object-fit: cover; border-radius: 12px; display: block; border:0;">
                                        </td>
                                    @endforeach
                                    
                                    {{-- Celda vacía para mantener estructura si el total es impar --}}
                                    @if(count($data['imagenes_secundarias']) % 2 !== 0)
                                        <td width="50%" style="padding: 8px;">&nbsp;</td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    {{-- Separador Negro --}}
                    <tr>
                        <td style="padding: 0 20px;">
                            <div style="height: 2px; background-color: #000000; width: 100%; margin: 0 auto 20px auto;"></div>
                        </td>
                    </tr>

                    {{-- Sección estática: Envío gratis --}}
                    <tr>
                        <td align="center" style="background-color: #ffffff; padding: 15px 20px 20px 20px;">
                            <div style="color: #1e3a5f; font-size: 22px; font-weight: bold; margin-bottom: 5px;">ENVÍO GRATIS</div>
                            <div style="color: #666666; font-size: 14px; text-transform: uppercase;">A TODO LIMA</div>
                        </td>
                    </tr>

                    {{-- Botón final dinámico --}}
                    <tr>
                        <td align="center" style="background-color: #1e3a5f; padding: 30px 20px;">
                            <a href="https://yuntaspublicidad.com/contacto" 
                               style="background-color: #ffffff; color: #1e3a5f; border-radius: 30px; padding: 15px 45px; font-size: 16px; font-weight: bold; text-transform: uppercase; text-decoration: none; display: inline-block;">
                                ¡COTIZA HOY!
                            </a>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>