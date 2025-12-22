<?php

use Illuminate\Support\Facades\Route;

// ==============================================================================
// 1. AUTENTICACIÓN (IAM)
// ==============================================================================
// ------------------- AUTHENTICATION -------------------
Route::prefix('auth')->group(function () {
    // público
    Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    // Rutas protegidas (Requieren Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [App\Http\Controllers\Auth\AuthController::class, 'me']);
        Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout']);
    });
});
// ==============================================================================
// 2. GESTIÓN DE CONTENIDO (CMS)
// ==============================================================================

// ------------------- BLOGS -------------------
Route::prefix('blogs')->group(function () {
    Route::get('/', [App\Http\Controllers\Blog\BlogController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Blog\BlogController::class, 'store']);
    Route::get('/{slug}', [App\Http\Controllers\Blog\BlogController::class, 'show']);
    Route::delete('/{id}', [App\Http\Controllers\Blog\BlogController::class, 'destroy']);
    Route::put('/{id}', [App\Http\Controllers\Blog\BlogController::class, 'update']);
});

// ------------------- PRODUCTOS -------------------
Route::prefix('productos')->group(function () {
    
    Route::get('/', [App\Http\Controllers\Product\ProductController::class, 'index']); 
    Route::post('/', [App\Http\Controllers\Product\ProductController::class, 'store']);
   //Route::get('/{slug}', [App\Http\Controllers\Product\ProductController::class, 'show']);
    Route::post('/{id}', [App\Http\Controllers\Product\ProductController::class, 'update']); 
    Route::delete('/{id}', [App\Http\Controllers\Product\ProductController::class, 'destroy']);
    Route::get('/{term}', [App\Http\Controllers\Product\ProductController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Product\ProductController::class, 'update']);
    // Ejemplo: Route::post('/', [App\Http\Controllers\ProductoController::class, 'store']);
    // Otros endpoints de productos
});
// ------------------- CATEGORÍAS (Público) -------------------
Route::prefix('categorias')->group(function () {
    // Endpoints públicos de categorías (listado, detalle, etc.)
    Route::get('/', [App\Http\Controllers\Admin\Category\CategoryController::class, 'index']);
});

// ==============================================================================
// 3. FORMULARIOS PÚBLICOS (CRM & SOPORTE)
// ==============================================================================
// ------------------- CRM / LEADS -------------------
Route::prefix('leads')->group(function () {
    Route::post('/', [App\Http\Controllers\CRM\LeadController::class, 'store']);
    Route::get('/', [App\Http\Controllers\CRM\LeadController::class, 'index']);  
    Route::put('/{id}', [App\Http\Controllers\CRM\LeadController::class, 'update']);
});
// ------------------- RECLAMOS (Claims) -------------------
Route::post('claims', [App\Http\Controllers\Support\ClaimController::class, 'store']);
// ------------------- CONTACTO (Soporte) -------------------
Route::prefix('contacto')->group(function () {
Route::post('/', [App\Http\Controllers\Support\ContactMessageController::class, 'store']);
});

// ==============================================================================
// 3. EMAIL PÚBLICO (Mailings, cotizaciones, formularios)
// ==============================================================================
Route::prefix('email')->group(function () {

    // Enviar el correo del formulario principal
    Route::post('/send', [App\Http\Controllers\Email\EmailController::class, 'iniciarSeguimiento']);

    // Enviar Mailing 1 (día 1)
   

    // Si deseas Mailing 2 y 3, solo descomenta:
    // Route::post('/mailing2', [App\Http\Controllers\Email\EmailController::class, 'enviarMailing2']);
    // Route::post('/mailing3', [App\Http\Controllers\Email\EmailController::class, 'enviarMailing3']);
});
Route::prefix('email-productos')->group(function () {
    Route::get('/', [App\Http\Controllers\Email\EmailProductController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Email\EmailProductController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Email\EmailProductController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Email\EmailProductController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Email\EmailProductController::class, 'destroy']);
});





// ==============================================================================
// 4. ADMINISTRACIÓN (ADMIN PANEL)
// ==============================================================================
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
// ------------------- ADMIN: USUARIOS -------------------
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
        
        // Asignar rol manualmente
        Route::post('/{id}/role', [App\Http\Controllers\Admin\UserController::class, 'assignRole']);
    });

    // ------------------- ADMINISTRACIÓN DE CATEGORÍAS -------------------
    Route::prefix('admin/categorias')->group(function () {
        Route::post('/', [App\Http\Controllers\Admin\Category\CategoryController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'destroy']);
        Route::get('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'show']);
    });
    // ------------------- RECLAMOS (Claims) -------------------
    // Admin (Protegido)
    Route::prefix('admin/claims')->group(function () {
        Route::get('/', [App\Http\Controllers\Support\ClaimController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Support\ClaimController::class, 'show']);
        Route::post('/{id}/reply', [App\Http\Controllers\Support\ClaimController::class, 'reply']);
        });
    // ------------------- ADMIN: MENSAJES DE CONTACTO -------------------
    Route::prefix('admin/contacto')->group(function () {
        // Endpoints de contacto
        Route::get('/', [App\Http\Controllers\Support\ContactMessageController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Support\ContactMessageController::class, 'show']);
        Route::delete('/{id}', [App\Http\Controllers\Support\ContactMessageController::class, 'destroy']);
    });

    // ------------------- USUARIOS -------------------   
    Route::prefix('usuarios')->group(function () {
        // Endpoints de usuarios
    });
    

// ------------------- Email  -------------------
    Route::prefix('email')->group(function () {
    //    Route::post('/send', [App\Http\Controllers\Support\EmailController::class, 'send']);
    });
});

