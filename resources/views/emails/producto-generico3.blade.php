<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ $data['titulo'] }}</title>
    <style>
        .img-grid {
            width: 100% !important;
            height: 280px !important; /* Altura ligeramente mayor para este diseño */
            object-fit: cover; 
            object-position: center; 
            display: block;
            border-radius: 15px;
        }

        .icon-text {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            margin-top: 8px;
        }

        @media only screen and (max-width: 600px) {
            .img-grid { height: 200px !important; }
            .container { width: 100% !important; }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container" style="background-color: #ffffff; width: 100%; max-width: 600px;">

                    {{-- Header: Título con tamaño ajustado --}}
                    <tr>
                        <td align="center" style="background-color: #1e3a5f; color: white; padding: 20px; font-size: 20px; font-weight: bold; font-style: italic; letter-spacing: 1px; text-transform: uppercase;">
                            {{ $data['titulo'] }}
                        </td>
                    </tr>

                    {{-- Imagen Principal --}}
                    <tr>
                        <td style="padding: 0;">
                            <img src="{{ $data['imagen_principal'] }}" alt="Principal" width="600" style="width: 100%; height: auto; display: block; border: 0;">
                        </td>
                    </tr>

                    {{-- Subtítulo Azul (Estilo Lumina) --}}
                    <tr>
                        <td align="center" style="background-color: #2c467a; color: white; padding: 15px 20px; font-size: 18px; font-weight: bold; font-style: italic;">
                            {{ $data['parrafo1'] }}
                            
                        </td>
                    </tr>

                    {{-- Párrafo Informativo --}}
                    <tr>
                        <td align="center" style="padding: 25px 40px; color: #333333; font-size: 15px; line-height: 1.4;">
                            Haz que tu negocio atraiga más clientes y destaque con nuestros productos; escríbenos para cotizar y aprovecha el 10% de descuento en tu primera compra.
                        </td>
                    </tr>

                    {{-- Imágenes Secundarias --}}
                    @if(isset($data['imagenes_secundarias']) && count($data['imagenes_secundarias']) > 0)
                    <tr>
                        <td align="center" style="padding: 0 20px 30px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    @foreach($data['imagenes_secundarias'] as $index => $imagen)
                                        @if($index % 2 === 0 && $index !== 0)
                                            </tr><tr>
                                        @endif
                                        <td align="center" width="50%" style="padding: 8px;">
                                            <img src="{{ $imagen }}" class="img-grid" style="width: 100%; height: 280px; object-fit: cover; border-radius: 15px; display: block;">
                                        </td>
                                    @endforeach
                                    @if(count($data['imagenes_secundarias']) % 2 !== 0)
                                        <td width="50%">&nbsp;</td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    {{-- Sección de Iconos (Innovación, Clientes, Ventas) --}}
                    <tr>
                        <td align="center" style="padding: 10px 20px 40px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" width="33.3%">
                                        <img src="https://cdn-icons-png.flaticon.com/512/2103/2103633.png" width="40" alt="Innovación">
                                        <div class="icon-text">Innovación</div>
                                    </td>
                                    <td align="center" width="33.3%">
                                        <img src="https://cdn-icons-png.flaticon.com/512/1256/1256650.png" width="40" alt="Clientes">
                                        <div class="icon-text">Clientes</div>
                                    </td>
                                    <td align="center" width="33.3%">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3121/3121768.png" width="40" alt="Ventas">
                                        <div class="icon-text">Ventas</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Botón Final --}}
                    <tr>
                        <td align="center" style="background-color: #1e3a5f; padding: 30px 20px;">
                            <a href="https://yuntaspublicidad.com/contacto" 
                               style="background-color: #ffffff; color: #1e3a5f; border-radius: 50px; padding: 15px 50px; font-size: 18px; font-weight: bold; text-transform: uppercase; text-decoration: none; display: inline-block;">
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