<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header-image { width: 100%; max-width: 600px; height: auto; margin-bottom: 20px; }
        h1 { color: #2c3e50; }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('storage/plantillas/yuntas-bienvenida.webp') }}" alt="Yuntas Bienvenida" class="header-image">
        
        <p>Estimado(a) <strong>{{ $cliente['nombre'] }}</strong>:</p>
        
        <p>Gracias por contactarnos.</p>
        
        <p>En <strong>Yuntas Publicidad</strong> te ayudamos a destacar con productos publicitarios personalizados y cotizaciones rápidas sin compromiso.</p>
        
        <p>Cuéntanos qué necesitas y con gusto te asesoramos.</p>
        
        <p>Saludos cordiales,<br>
        <strong>Yuntas Publicidad ✨</strong><br>
        912 849 782</p>
    </div>
</body>
</html>