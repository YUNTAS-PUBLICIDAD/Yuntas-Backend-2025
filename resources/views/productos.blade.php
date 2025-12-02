<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f6f6f6;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .product {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            box-shadow: 0 2px 6px #ccc;
        }
        img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .price {
            font-size: 22px;
            font-weight: bold;
            color: #008000;
        }
    </style>
</head>
<body>

    <h1>Listado de Productos</h1>

    <div id="productList">Cargando...</div>

    <script>
        async function loadProducts() {
            const container = document.getElementById("productList");

            try {
                const res = await fetch("{{ url('/api/productos') }}");
                const json = await res.json();

                if (!json.success) {
                    container.innerHTML = "Error al cargar productos";
                    return;
                }

                const productos = json.data.data;
                container.innerHTML = "";

                productos.forEach(p => {
                    container.innerHTML += `
                        <div class="product">
                            <img src="${p.imagen_principal?.url}" alt="${p.nombre}">
                            <div>
                                <h2>${p.nombre}</h2>
                                <p class="price">S/ ${p.precio}</p>
                                <p><strong>Estado:</strong> ${p.estado}</p>
                                <p><strong>Especificaciones:</strong></p>
                                <ul>
                                    ${p.especificaciones.map(e => `<li>${e}</li>`).join("")}
                                </ul>
                            </div>
                        </div>
                    `;
                });

            } catch (error) {
                container.innerHTML = "No se pudo conectar con la API.";
            }
        }

        loadProducts();
    </script>

</body>
</html>
