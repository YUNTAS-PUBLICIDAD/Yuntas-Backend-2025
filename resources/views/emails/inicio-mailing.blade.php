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
        
        <h1>¡Bienvenido/a a Yuntas!</h1>
        
        <p>Gracias por registrarte, <strong>{{ $cliente['nombre'] }}</strong>. Nos alegra tenerte con nosotros y que formes parte de nuestra comunidad.</p>
        
        <p>Desde ahora tendrás acceso a información sobre nuestros productos, novedades y soluciones en letreros acrílicos y señalización diseñadas para potenciar la imagen de tu negocio con un estilo moderno, profesional y duradero.</p>
        
        <p>Si tienes alguna consulta o necesitas asesoría personalizada, no dudes en escribirnos. Estaremos encantados de ayudarte a encontrar la mejor opción para tu proyecto.</p>
        
        <p>Gracias por confiar en Yuntas.<br>
        Tu marca merece destacar.</p>
        
        <p><strong>Equipo Yuntas</strong></p>
    </div>
</body>
</html>