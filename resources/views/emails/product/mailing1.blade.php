@component('mail::message')

# {{ $seccion->titulo }}

@if($seccion->imagen_principal_url)
<p style="text-align:center">
    <img src="{{ $seccion->imagen_principal_url }}" style="width:100%;max-width:450px;">
</p>
@endif

<p style="text-align:center;font-size:15px;">
    {{ $seccion->parrafo1 }}
</p>

<table width="100%">
<tr>
    <td align="center">
        @if($seccion->imagen_secundaria1_url)
        <img src="{{ $seccion->imagen_secundaria1_url }}" style="width:90%;max-width:200px;">
        @endif
    </td>
    <td align="center">
        @if($seccion->imagen_secundaria2_url)
        <img src="{{ $seccion->imagen_secundaria2_url }}" style="width:90%;max-width:200px;">
        @endif
    </td>
</tr>
</table>

<p style="text-align:center;font-size:18px;font-weight:bold;margin-top:20px;">
    🚚 Envío Gratis A Todo Lima
</p>

<p style="text-align:center;font-size:18px;font-weight:bold;">
    📩 Cotiza Hoy
</p>

<hr>

### Datos del cliente:
- **Nombre:** {{ $cliente['nombre'] }}
- **Teléfono:** {{ $cliente['telefono'] }}
- **Correo:** {{ $cliente['correo'] }}

@endcomponent
