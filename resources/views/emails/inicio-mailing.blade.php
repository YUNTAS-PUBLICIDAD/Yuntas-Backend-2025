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
        
        <p>Estimado/a <strong>{{ $cliente['nombre'] }}</strong>:</p>
        
        <p>Gracias por contactarnos y por su interés en nuestros servicios.</p>
        
        <p>En <strong>Yuntas Publicidad</strong> somos su aliado en publicidad. Nos especializamos en brindar soluciones publicitarias personalizadas que ayudan a destacar su marca y potenciar su presencia en el mercado.</p>
        
        <p>🔹 <strong>¿En qué podemos ayudarle?</strong></p>
        <ul>
            <li>Productos publicitarios personalizados</li>
            <li>Cotizaciones sin compromiso</li>
        </ul>
        
        <p>En breve recibirá información detallada en este correo. Si tiene alguna consulta adicional, no dude en escribirnos. Estaremos encantados de atenderle.</p>
        
        <p>Saludos cordiales,<br>
        <strong>Yuntas Publicidad ✨</strong><br>
        912 849 782
    </div>
</body>
</html>