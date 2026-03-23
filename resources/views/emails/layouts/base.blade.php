<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">

  <style>
    @media only screen and (max-width: 600px) {
      .container {
        width: 100% !important;
      }

      .padding {
        padding: 20px !important;
      }

      .text {
        font-size: 14px !important;
      }

      .header-text {
        font-size: 18px !important;
      }
    }
  </style>

</head>

<body style="margin:0; padding:0; background:#f4f6f9; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">

      <!-- CONTENEDOR -->
      <table class="container" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">

        <!-- HEADER -->
        <tr>
          <td style="background:#0b1c3f; padding:20px; text-align:center;">
            <h1 class="header-text" style="color:#ffffff; margin:0; font-size:22px;">
              YUNTAS PUBLICIDAD
            </h1>
            <p style="color:#8fd3ff; margin:5px 0 0; font-size:13px;">
              Impulsamos tu negocio
            </p>
          </td>
        </tr>

        <!-- IMAGEN -->
        @if ($imagenUrl)
        <tr>
          <td style="text-align:center; background:#f9fafc;">
            <img src="{{ $imagenUrl }}" style="width:100%; max-width:600px; display:block;">
          </td>
        </tr>
        @endif

        <!-- CONTENIDO -->
        <tr>
          <td class="padding text" style="padding:30px; color:#333; font-size:15px; line-height:1.6;">
            {!! $contenido !!}
          </td>
        </tr>

        <!-- BOTÓN (IMPORTANTE para conversión) -->
        <tr>
          <td align="center" style="padding:0 20px 30px;">
            <a href="#"
              style="background:#0b1c3f; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:5px; display:inline-block;">
              Ver servicios
            </a>
          </td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="background:#0b1c3f; padding:20px; text-align:center;">
            <p style="color:#ffffff; font-size:12px; margin:0;">
              © {{ date('Y') }} Yuntas Publicidad
            </p>

            <p style="color:#8fd3ff; font-size:11px; margin:6px 0;">
              Urb. Alameda La Rivera Mz F Lt 30
            </p>

            <p style="color:#8fd3ff; font-size:11px; margin:0;">
              +51 912 849 782
            </p>
          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>

</body>
</html>