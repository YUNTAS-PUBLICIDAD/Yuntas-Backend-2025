<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $data['titulo'] }}</title>
    <style>
        /* Reset y ajuste básico */
        img {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 16px;
        }
        table {
            border-collapse: collapse;
        }
        /* Contenedor principal */
        .container {
            width: 100%;
            max-width: 600px;
            background-color: #0b1b3a;
            color: white;
            margin: 0 auto;
        }
        /* Imágenes secundarias pequeñas */
        .secondary-img {
            width: 100%;
            border-radius: 14px;
            display: block;
        }
        /* Media queries para pantallas pequeñas */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            table[class="container"] {
                width: 100% !important;
            }
            td[class="title"] {
                font-size: 20px !important;
                padding: 20px 10px 10px !important;
            }
            td[class="paragraph"] {
                font-size: 14px !important;
                padding: 10px 15px 30px !important;
            }
            td[class="button-container"] a {
                padding: 12px 30px !important;
                font-size: 14px !important;
            }
            td[class="secondary-img-cell"] {
                width: 100% !important;
                padding: 5px 0 !important;
            }
            /* imágenes secundarias en filas separadas */
            .secondary-row tr {
                display: block !important;
                width: 100% !important;
            }
            .secondary-row td {
                display: block !important;
                width: 100% !important;
                padding: 5px 0 !important;
            }
        }
    </style>
</head>


<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0b1b3a;">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" class="container" style="background-color:#0b1b3a; color:white;">

    <!-- TÍTULO -->
    <tr>
        <td align="center" class="title" style="padding:30px 20px 10px; font-size:24px; font-weight:bold; text-transform:uppercase;">
            {{ $data['titulo'] }}
        </td>
    </tr>

    <!-- IMAGEN PRINCIPAL -->
    <tr>
        <td style="padding:20px;">
            <img src="{{ $data['imagen_principal'] }}" alt="Imagen principal">
        </td>
    </tr>

    <!-- TEXTO -->
    <tr>
        <td align="center" class="paragraph" style="padding:10px 30px 30px; font-size:16px; color:#dbe7ff;">
            {{ $data['parrafo1'] }}
        </td>
    </tr>

    <!-- IMÁGENES SECUNDARIAS -->
    <!-- IMÁGENES SECUNDARIAS -->
@if(!empty($data['imagenes_secundarias']))
<tr>
    <td align="center" style="padding:10px 20px;">
        <table width="100%" style="background:white; border-radius:16px; padding:10px 15px;">
            @foreach($data['imagenes_secundarias'] as $img)
            <tr>
                <td style="padding:10px 0;">
                    <img src="{{ $img }}" alt="Imagen secundaria" style="width:100%; border-radius:14px; display:block;">
                </td>
            </tr>
            @endforeach
        </table>
    </td>
</tr>
@endif


    <!-- BOTÓN -->
    <tr>
        <td align="center" class="button-container" style="padding:30px;">
            <a href="https://yuntaspublicidad.com/contacto"
               style="background:white;
                      color:#0b1b3a;
                      padding:14px 40px;
                      border-radius:30px;
                      text-decoration:none;
                      font-weight:bold;
                      font-size:16px;">
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