@php
$frontendUrl = 'https://yuntaspublicidad.com';
@endphp

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
    }
  </style>
</head>

<body style="margin:0; padding:0; background:#f4f6f9; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">

      <!-- CONTENEDOR -->
      <table class="container" width="600" cellpadding="0" cellspacing="0"
             style="background:#ffffff; border-radius:8px; overflow:hidden;">

        <!-- TOP BAR (simula cliente email) -->
        <!--<tr>
          <td style="background:#f2f2f2; padding:10px 15px;">
            <div style="display:flex; gap:6px;">
              <div style="width:10px; height:10px; background:#ff5f57; border-radius:50%;"></div>
              <div style="width:10px; height:10px; background:#ffbd2e; border-radius:50%;"></div>
              <div style="width:10px; height:10px; background:#28c840; border-radius:50%;"></div>
            </div>
          </td>
        </tr>-->
        <!-- HEADER SIMPLE (branding real, no fake UI) -->
               <tr>
                 <td style="padding:20px; text-align:center; border-bottom:1px solid #eee;">
                   <strong style="font-size:18px; color:#0b1c3f;">
                     YUNTAS PUBLICIDAD
                   </strong>
                 </td>
               </tr>

        <!-- SUBJECT -->
        <!--<tr>
          <td style="padding:10px 20px; border-bottom:1px solid #eee;">
            <p style="font-size:11px; color:#999; margin:0;">
              De: no-reply@yuntaspublicidad.com
            </p>

            <p style="font-size:14px; font-weight:bold; margin:4px 0 0; color:#111;">
              {{ $subject ?? 'Mensaje' }}
            </p>
          </td>
        </tr>-->

        <!-- IMAGEN -->
        @if ($imagenUrl)
        <tr>
          <td style="text-align:center;">
            <img src="{{ $imagenUrl }}"
                 style="width:100%; max-width:600px; display:block;">
          </td>
        </tr>
        @endif

        <!-- CONTENIDO -->
        <tr>
          <td class="padding text"
              style="padding:24px; color:#333; font-size:14px; line-height:1.6;">
            {!! $contenido !!}
          </td>
        </tr>

        <!-- CTA -->
        @if (!empty($cta_url))
        <tr>
          <td align="center" style="padding:0 20px 30px;">
            <a href="{{ $cta_url }}"
               target="_blank"
               style="
                background:#111;
                color:#fff;
                padding:10px 20px;
                text-decoration:none;
                border-radius:6px;
                font-size:13px;
                font-weight:600;
                display:inline-block;
               ">
              {{ $cta_text ?? 'Ver más' }}
            </a>
          </td>
        </tr>
        @endif

        <!-- FOOTER (minimalista como preview) -->
        <tr>
          <td style="padding:15px; text-align:center; font-size:11px; color:#aaa; border-top:1px solid #f0f0f0;">
            © {{ date('Y') }} YuntasPublicidad
          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>

</body>
</html>
