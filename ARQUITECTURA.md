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

## 🧩 3. Estructura general de carpetas

app/
┣ Http/
┃ ┣ Controllers/
┃ ┣ Requests/
┃ ┗ Resources/
┣ Services/
┣ Repositories/
┃ ┣ Interfaces/
┃ ┗ Eloquent/
┣ Models/
┣ DTOs/
┣ Rules/
┗ Exceptions/

---

## ⚙️ 4. Descripción por carpeta

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

## 🧠 5. Flujo interno simplificado

Controller (recibe request)  
↓  
Request (valida datos)  
↓  
Service (lógica de negocio + cache)  
↓  
Repository (consulta o guarda en DB)  
↓  
Model (representa entidad)  
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

**Resumen final:**  
La arquitectura se basa en **Clean Architecture con patrón Service–Repository**,  
distribuyendo el código por capas: *presentación*, *negocio* y *persistencia*.  
Cada carpeta tiene una responsabilidad clara, lo que garantiza **rendimiento, organización y fácil mantenimiento**.
