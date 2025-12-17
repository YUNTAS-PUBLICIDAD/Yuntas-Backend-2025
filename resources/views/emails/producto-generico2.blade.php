<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $data['titulo'] }}</title>
    <style>
        img {
            max-width: 100%;
            display: block;
        }
        table {
            border-collapse: collapse;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        /* Forzamos que todas las imágenes secundarias midan lo mismo */
        .img-secundaria {
            width: 100% !important;
            height: 350px !important; 
            object-fit: cover; 
            border-radius: 14px;
        }
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .img-secundaria { height: 250px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color: #f4f4f4;">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center">
            
            <table width="600" cellpadding="0" cellspacing="0" class="container" style="font-family: 'Segoe UI', Arial, sans-serif;">
                
                <tr>
                    <td style="background-color: #1a2e5a;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="padding:35px 20px 25px; font-size:20px; font-weight:bold; font-style: italic; text-transform:uppercase; color: white; letter-spacing: 1px;">
                                    {{ $data['titulo'] }}
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding:0;">
                                    <img src="{{ $data['imagen_principal'] }}" 
                                         alt="{{ $data['titulo'] }}" 
                                         style="width:100%; border:0; display:block;">
                                </td>
                            </tr>

                            <tr>
                                <td align="center" style="padding:30px 40px 35px; font-size:14px; color:#dbe7ff; font-style: italic; line-height: 1.5;">
                                    {{ $data['parrafo1'] }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="background-color: #ffffff; padding-top: 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            
                            @if(!empty($data['imagenes_secundarias']))
                            <tr>
                                <td align="center" style="padding:0 40px;">
                                    @foreach($data['imagenes_secundarias'] as $img)
                                    <div style="margin-bottom: 30px;">
                                        <img src="{{ $img }}" 
                                             alt="Galería {{ $data['titulo'] }}" 
                                             class="img-secundaria"
                                             style="box-shadow: 0 6px 15px rgba(0,0,0,0.15); width: 100%;">
                                    </div>
                                    @endforeach
                                </td>
                            </tr>
                            @endif

                            <tr>
                                <td align="center" style="background-color: #1a2e5a; padding: 50px 20px;">
                                    <a href="https://yuntaspublicidad.com/contacto" 
                                       style="background-color: #ffffff; 
                                              color: #1a2e5a; 
                                              border-radius: 50px; 
                                              padding: 18px 55px; 
                                              font-size: 18px; 
                                              font-weight: bold; 
                                              text-transform: uppercase; 
                                              text-decoration: none; 
                                              display: inline-block;
                                              box-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                                        ¡COTIZA HOY!
                                    </a>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>