## 🧩 1. Contexto general del sistema

El sistema está diseñado para **empresas que venden productos**, con presencia web informativa, sin carrito de compras ni pasarela de pago.  
El objetivo principal es permitir la **gestión completa de productos, blogs y contactos**, ofreciendo una experiencia rápida y confiable tanto en el frontend (Next.js) como en el backend (Laravel).

### 🔹 Módulos principales
- **Productos:** registro, actualización, listado y detalle de productos.  
- **Categorías:** organización de productos por tipo.  
- **Blog:** artículos informativos sobre la empresa o sus servicios.  
- **Contacto:** gestión de mensajes enviados desde el sitio web.  
- **Usuarios:** autenticación, roles y permisos para el panel de administración.

### 🔹 Entorno de despliegue
- Hosting compartido (Hostinger), sin procesos Node.js activos.  
- Frontend generado con **Next.js exportado (estático + dinámico)**.  
- Backend con **Laravel 11** y **MySQL** como base de datos.  
- Comunicación vía **API RESTful** en formato JSON.

---


# 🧱 Arquitectura del Backend – Sistema Empresarial (Laravel 11)

## 🏷️ Integración DDD (Domain-Driven Design)

Además de Clean Architecture y Service–Repository, el sistema puede aprovechar DDD para organizar el núcleo del negocio y el lenguaje ubicuo. DDD ayuda a definir claramente las reglas, entidades y procesos del dominio, separando el "qué" del negocio del "cómo" técnico.

### 🔹 ¿Qué aporta DDD?
- **Entidades:** Objetos con identidad propia (ej: Producto, Usuario).
- **Value Objects:** Objetos sin identidad, solo valor (ej: Email, Precio).
- **Agregados:** Conjuntos de entidades y reglas (ej: Pedido con sus líneas).
- **Servicios de Dominio:** Lógica de negocio que no pertenece a una entidad específica.
- **Repositorios (contratos):** Interfaces para acceder a las entidades del dominio.

### 🔹 Estructura recomendada con DDD

```
app/
┣ Domain/                # Núcleo del negocio (DDD)
┃ ┣ Entities/            # Entidades del dominio
┃ ┣ ValueObjects/        # Objetos de valor
┃ ┣ Aggregates/          # Agregados (opcional)
┃ ┣ Services/            # Servicios de dominio
┃ ┗ Repositories/        # Contratos de repositorio
┣ Application/           # Casos de uso y lógica de aplicación
┃ ┣ DTOs/                # Data Transfer Objects
┃ ┣ Services/            # Casos de uso (orquestan el dominio)
┃ ┗ Exceptions/          # Excepciones de aplicación
┣ Infrastructure/        # Implementaciones técnicas
┃ ┣ Persistence/         # Repositorios concretos (Eloquent, SQL)
┃ ┣ Rules/               # Validaciones personalizadas
┃ ┗ Providers/           # Integraciones externas
┣ Http/
┃ ┣ Controllers/         # Entrada de la API
┃ ┣ Requests/            # Validación de datos
┃ ┗ Resources/           # Formateo de respuestas
┣ Models/                # Modelos Eloquent (pueden ir en Infrastructure)
```

### 🔹 ¿Cómo se comporta cada capa?
- **Domain/**: Define el modelo de negocio puro, independiente de Laravel. Aquí viven las reglas, entidades y contratos.
- **Application/**: Orquesta los casos de uso, recibe DTOs y lanza excepciones. Llama a los servicios y repositorios del dominio.
- **Infrastructure/**: Implementa los contratos definidos en Domain, usando Eloquent, SQL, APIs externas, etc.
- **Http/**: Controladores, Requests y Resources, que reciben la petición, validan, llaman al caso de uso y devuelven la respuesta.
- **Models/**: Modelos Eloquent, pueden estar en Infrastructure si prefieres separar el ORM del dominio.

### 🔹 Relación entre DDD y Clean Architecture
- **DDD** define el "qué" (modelo de dominio y reglas).
- **Clean Architecture** define el "cómo" (organización y flujo entre capas).

**Ejemplo de flujo:**
Controller → Request → Application Service (caso de uso) → Domain Service/Entity → Repository (contrato) → Infrastructure Repository (implementación) → Model (Eloquent) → Resource

---

## 🧭 2. Tipo de arquitectura

**Arquitectura:** Clean Architecture adaptada a Laravel  
**Patrón de diseño:** Service–Repository Pattern

> Esta arquitectura separa la lógica de negocio del framework y organiza el código en capas independientes.  
> Permite tener un sistema más rápido, fácil de mantener y escalable para agregar nuevas funcionalidades.

### ¿Cómo funciona Clean Architecture?

Imagina el sistema como una serie de capas, donde cada una tiene una responsabilidad clara y no depende directamente de las demás.  
- **Presentación:** Recibe las peticiones del usuario (controladores).
- **Negocio:** Procesa la lógica principal (servicios).
- **Persistencia:** Accede y gestiona los datos (repositorios y modelos).

Cada capa solo se comunica con la siguiente, lo que permite cambiar la base de datos, el framework o el frontend sin afectar la lógica principal.

---


## 🧩 3. Estructura general de carpetas (Clean Architecture + DDD)

app/
┣ Domain/
┃ ┣ Entities/
┃ ┣ ValueObjects/
┃ ┣ Aggregates/
┃ ┣ Services/
┃ ┗ Repositories/
┣ Application/
┃ ┣ DTOs/
┃ ┣ Services/
┃ ┗ Exceptions/
┣ Infrastructure/
┃ ┣ Persistence/
┃ ┣ Rules/
┃ ┗ Providers/
┣ Http/
┃ ┣ Controllers/
┃ ┣ Requests/
┃ ┗ Resources/
┣ Models/


---


## ⚙️ 4. Descripción por carpeta (con DDD)

### 🟣 **app/Domain/**
Núcleo del negocio y lenguaje ubicuo.
- **Entities/**: Objetos con identidad propia (ej: Producto, Usuario).
- **ValueObjects/**: Objetos de valor (ej: Email, Precio).
- **Aggregates/**: Conjuntos de entidades y reglas (opcional).
- **Services/**: Lógica de negocio que no pertenece a una entidad específica.
- **Repositories/**: Contratos para acceder a las entidades del dominio.

### 🟡 **app/Application/**
Casos de uso y lógica de aplicación.
- **DTOs/**: Estructuras para transportar datos entre capas.
- **Services/**: Casos de uso, orquestan el dominio y coordinan los repositorios.
- **Exceptions/**: Excepciones de aplicación y dominio.

### 🟠 **app/Infrastructure/**
Implementaciones técnicas y dependencias externas.
- **Persistence/**: Repositorios concretos (Eloquent, SQL, APIs externas).
- **Rules/**: Validaciones personalizadas.
- **Providers/**: Integraciones externas y servicios.

### 🟢 **app/Http/**
Capa de interacción con el cliente (API).
- **Controllers/**: Reciben las peticiones HTTP y llaman a los casos de uso.
- **Requests/**: Validan los datos que llegan desde el cliente.
- **Resources/**: Transforman los modelos en respuestas JSON limpias y seguras.

### 🔵 **app/Models/**
Define las **entidades** del sistema.

- Cada modelo representa una tabla de la base de datos.  
- Aquí se configuran los campos `fillable`, relaciones y scopes.  
- Ejemplo: `Producto`, `Categoria`, `Blog`, `Usuario`.

---

### 🟢 **app/Http/**
Contiene toda la capa de **interacción con el cliente (API)**.

- **Controllers/**  
  - Reciben las peticiones HTTP (GET, POST, PUT, DELETE).  
  - No contienen lógica de negocio, solo llaman a los *Services*.  
  - Devuelven respuestas JSON al frontend.

- **Requests/**  
  - Validan los datos que llegan desde el cliente antes de llegar al servicio.  
  - Permite centralizar reglas (`required`, `email`, `min`, etc.).  
  - Ejemplo: `StoreProductoRequest.php`.

- **Resources/**  
  - Transforman los modelos en respuestas JSON limpias y seguras.  
  - Evitan exponer campos innecesarios de la base de datos.  
  - Ejemplo: `ProductoResource.php`.

---

### 🟠 **app/Services/**
Capa de **lógica de negocio**.

- Contiene la funcionalidad principal del sistema.  
- Aplica validaciones adicionales, cache y llamadas a otros servicios.  
- Orquesta la comunicación entre el controlador y el repositorio.  
- Ejemplo: `ProductoService.php`, `BlogService.php`.

**Ejemplo:**  
> Al listar productos, aquí se decide si leer desde cache o desde la base de datos.

---

### 🟡 **app/Repositories/**
Capa de **acceso a datos**.

- Contiene toda la comunicación con la base de datos (ORM Eloquent o SQL).  
- Encapsula consultas, filtros y joins.  
- Separa la lógica de negocio del motor de base de datos.

**Subcarpetas opcionales:**
- **Interfaces/** → Define contratos para implementar distintos repositorios.  
- **Eloquent/** → Implementaciones usando Eloquent ORM.

**Ejemplo:**  
> `ProductoRepository.php` obtiene productos activos, ordenados y con relaciones.

---

### 🔵 **app/Models/**
Define las **entidades** del sistema.

- Cada modelo representa una tabla de la base de datos.  
- Aquí se configuran los campos `fillable`, relaciones y scopes.  
- Ejemplo: `Producto`, `Categoria`, `Blog`, `Usuario`.

---

### 🟣 **app/DTOs/**
(Data Transfer Objects)  
Estructuras de datos usadas para **transportar información entre capas** sin depender de modelos o requests.

- Garantizan que los servicios reciban datos tipados y controlados.  
- Evitan errores al pasar arreglos con datos incompletos.

**Ejemplo:**  
> `ProductoDTO` con propiedades `nombre`, `descripcion`, `precio`.

---

### 🔴 **app/Rules/**
Validaciones personalizadas que extienden el sistema de validación de Laravel.

- Se usan cuando las reglas `required`, `unique`, `email`, etc. no son suficientes.  
- Ejemplo: `ValidarPrecioDecimal.php` o `ValidarStockDisponible.php`.

---

### ⚫ **app/Exceptions/**
Manejo de **errores personalizados**.

- Permiten devolver errores con mensajes claros en formato JSON.  
- Ejemplo:  
  - `NotFoundException.php` → para registros no encontrados.  
  - `BusinessRuleException.php` → para violaciones de reglas de negocio.

---


## 🚦 Orden recomendado para implementar un flujo completo

Para mantener la coherencia y aprovechar al máximo la arquitectura, cada funcionalidad debe seguir este orden de implementación. Se indica la carpeta donde debe ir cada archivo:

1. **Definir el endpoint en `routes/api.php`**  
   (Carpeta: `routes/`)  
   Permite saber cómo se va a consumir la funcionalidad desde el frontend y qué método del controlador se invoca.

2. **Crear el Request para validación**  
   (Carpeta: `app/Http/Requests/`)  
   Garantiza que los datos recibidos cumplen las reglas antes de llegar a la lógica de negocio.

3. **Crear el Controller y su método**  
   (Carpeta: `app/Http/Controllers/`)  
   Recibe la petición, la valida y delega el procesamiento al Service.

4. **Crear el DTO (Data Transfer Object)**  
   (Carpeta: `app/Application/DTOs/`)  
   Transporta los datos validados de forma tipada y segura entre capas.

5. **Crear el Service de aplicación**  
   (Carpeta: `app/Application/Services/`)  
   Orquesta el caso de uso, aplica reglas de negocio y coordina el acceso a datos.

6. **Definir la interfaz del Repository (contrato)**  
   (Carpeta: `app/Domain/Repositories/`)  
   Permite desacoplar la lógica de negocio de la implementación técnica de persistencia.

7. **Implementar el Repository concreto**  
   (Carpeta: `app/Infrastructure/Persistence/`)  
   Realiza la persistencia usando Eloquent, SQL, o cualquier tecnología.

8. **Crear la entidad de dominio**  
   (Carpeta: `app/Domain/Entities/`)  
   Representa el objeto principal del negocio y sus reglas.

9. **Crear el modelo Eloquent**  
   (Carpeta: `app/Models/`)  
   Permite interactuar con la base de datos de forma sencilla y segura.

10. **Crear el Resource para formatear la respuesta**  
    (Carpeta: `app/Http/Resources/`)  
    Devuelve los datos al frontend en el formato adecuado, ocultando información sensible o innecesaria.

---

## 🗂️ Otras carpetas importantes y su función

Además de las carpetas anteriores, existen otras carpetas de configuración y soporte que ayudan al funcionamiento y organización del sistema:

- **config/**  
  Configuración de servicios, base de datos, correo, cache, etc.  
  Ayuda a los puntos 5, 7 y 9 (Services, Repositories, Models) al definir parámetros globales.

- **database/**  
  Migraciones, seeders y factories para la base de datos.  
  Relacionado con el punto 9 (Models) y el 7 (Repositories), ya que define la estructura y datos iniciales.

- **bootstrap/**  
  Inicialización y configuración de Laravel.  
  Soporte general para todos los puntos, especialmente para el arranque y registro de providers.

- **public/**  
  Archivos accesibles públicamente (index.php, assets).  
  Relacionado con la presentación y acceso externo, indirectamente con el punto 1 (endpoint).

- **resources/**  
  Vistas Blade y archivos estáticos.  
  Apoya la presentación y el formateo de respuestas, aunque en API REST se usa más el Resource.

- **storage/**  
  Archivos generados, logs, cache y uploads.  
  Soporte para persistencia y debugging, útil para los puntos 7 y 9.

- **tests/**  
  Pruebas automatizadas para asegurar calidad.  
  Permite validar el correcto funcionamiento de todos los puntos anteriores.

- **vendor/**  
  Dependencias externas PHP instaladas por Composer.  
  Soporte técnico para todos los puntos, especialmente para Models, Repositories y Services.

---

**¿Por qué este orden y organización?**
- Permite construir el flujo de datos de forma natural, desde la entrada (API) hasta la salida (respuesta).
- Cada archivo tiene una responsabilidad clara y está alineado con la arquitectura.
- Facilita el trabajo en equipo y la escalabilidad del sistema.
- Las carpetas de soporte y configuración aseguran que cada capa funcione correctamente y sea fácil de mantener.

---

## 🧠 5. Flujo interno simplificado (Clean Architecture + DDD)

Controller (recibe request)
↓
Request (valida datos)
↓
Application Service (caso de uso)
↓
Domain Service/Entity (reglas de negocio)
↓
Repository (contrato en Domain)
↓
Infrastructure Repository (implementación)
↓
Model (Eloquent)
↓
Resource (formatea respuesta)

---

## 📝 Explicación sencilla de la arquitectura y el patrón

Imagina que el sistema es como una fábrica:

- **Controller:** Recibe el pedido del cliente (frontend).
- **Request:** Revisa que el pedido esté bien hecho (datos correctos).
- **Service:** Decide cómo procesar el pedido (lógica de negocio).
- **Repository:** Busca o guarda los productos en la base de datos.
- **Model:** Representa cada producto encontrado.
- **Resource:** Prepara el producto para que llegue bonito y seguro al cliente.

**Patrón Service–Repository:**  
El Service se encarga de la lógica de negocio y llama al Repository, que se encarga de acceder a los datos. Así, cada parte tiene su responsabilidad y el código es más fácil de mantener y escalar.

---

## ⚙️ 6. Ventajas de esta organización

| Ventaja | Descripción |
|----------|-------------|
| **Rendimiento** | Cada capa hace solo lo necesario; se puede aplicar cache donde corresponde. |
| **Mantenibilidad** | Código limpio y dividido por responsabilidades. |
| **Escalabilidad** | Fácil agregar nuevos módulos sin romper el resto. |
| **Corrección de errores** | Si algo falla, se identifica en qué capa está el problema. |
| **Reutilización** | Los repositorios y servicios pueden ser usados en otras partes del sistema o microservicios futuros. |

---

## 🧱 7. Estructura recomendada del proyecto completo

project-root/
┣ app/
┃ ┣ Http/
┃ ┣ Services/
┃ ┣ Repositories/
┃ ┣ Models/
┃ ┣ DTOs/
┃ ┣ Rules/
┃ ┗ Exceptions/
┣ bootstrap/
┣ config/
┣ database/
┣ public/
┣ resources/
┣ routes/
┃ ┣ api.php
┃ ┗ web.php
┣ storage/
┣ tests/
┗ vendor/

---

## 🧩 Carpetas opcionales y especializadas en la arquitectura

Estas carpetas pueden aparecer en tu proyecto según las necesidades del dominio y la complejidad de la lógica de negocio. No son obligatorias, pero pueden aportar claridad y robustez si tu sistema lo requiere.

- **Domain/ValueObjects/**  
  Objetos de valor como Email, Precio, etc.  
  Sirven para encapsular reglas y validaciones de tipos simples pero importantes.  
  Interactúan principalmente con Entities y Domain/Services.

- **Domain/Aggregates/**  
  Agregados que agrupan varias entidades bajo una raíz (ej: Pedido con líneas).  
  Útil para dominios complejos donde varias entidades se gestionan como una sola unidad.  
  Interactúan con Entities, Domain/Services y Repositories.

- **Domain/Services/**  
  Servicios de dominio para lógica de negocio que no pertenece a una entidad específica.  
  Ejemplo: cálculos, validaciones transversales, reglas de negocio complejas.  
  Interactúan con Entities, ValueObjects y Repositories.

- **Infrastructure/Providers/**  
  Service Providers personalizados o integraciones externas (APIs, servicios de terceros).  
  Permiten registrar servicios en el contenedor de Laravel o conectar con sistemas externos.  
  Interactúan con Application/Services y, a veces, con Domain/Services.

- **Infrastructure/Rules/**  
  Validaciones personalizadas que no van en los Requests.  
  Útil para reglas complejas o reutilizables en varios puntos del sistema.  
  Interactúan con Http/Requests y Application/Services.

- **Application/Exceptions/**  
  Excepciones personalizadas para errores de negocio y aplicación.  
  Permiten manejar y devolver mensajes claros en la API.  
  Interactúan con Application/Services, Domain/Services y Controllers.

---

**¿Cuándo usar estas carpetas?**
- Solo si tu dominio o lógica de negocio lo requiere.
- Si tienes reglas, tipos o procesos complejos que no encajan en las carpetas principales.
- Si necesitas claridad, reutilización y robustez en el diseño.

**¿Con qué interactúan?**
- Cada carpeta interactúa con las capas principales según su función, como se indica arriba.
- Así mantienes la arquitectura limpia y solo agregas complejidad cuando realmente aporta valor.
