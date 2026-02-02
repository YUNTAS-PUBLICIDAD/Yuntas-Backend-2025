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
    Route::get('/{slug}', [App\Http\Controllers\Product\ProductController::class, 'show']);
});

// ------------------- CATEGORÍAS (Público) -------------------
Route::prefix('categorias')->group(function () {
    // Endpoints públicos de categorías (listado, detalle, etc.)
    Route::get('/', [App\Http\Controllers\Admin\Category\CategoryController::class, 'index']);
});

// ==============================================================================
// 3. FORMULARIOS PÚBLICOS (CRM & SOPORTE)
// ==============================================================================
// ------------------- RECLAMOS (Claims) -------------------
Route::post('claims', [App\Http\Controllers\Support\ClaimController::class, 'store']);
// ------------------- CONTACTO (Soporte) -------------------
Route::prefix('contacto')->group(function () {
Route::post('/', [App\Http\Controllers\Support\ContactMessageController::class, 'store']);
});

// ------------------- POPUP: EMAIL -------------------
Route::prefix('email-popup')->group(function () {
    Route::post('/enviar', [App\Http\Controllers\Email\EmailPopupController::class, 'enviar']);
});

// ------------------- POPUP: WHATSAPP -------------------
Route::prefix('whatsapp-popup')->group(function () { 
    Route::post('/enviar', [App\Http\Controllers\Whatsapp\WhatsappPopupController::class, 'enviar']);
});

// ==============================================================================
//                          ADMINISTRACIÓN (ADMIN PANEL)
// ==============================================================================
Route::middleware(['auth:sanctum', 'role:admin|marketing|ventas'])->group(function () {

    // ------------------- USUARIOS -------------------
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
        
        // Asignar rol manualmente
        Route::post('/{id}/role', [App\Http\Controllers\Admin\UserController::class, 'assignRole']);
    });

    // ------------------- CATEGORÍAS -------------------
    Route::prefix('admin/categorias')->group(function () {
        Route::post('/', [App\Http\Controllers\Admin\Category\CategoryController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'destroy']);
        Route::get('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'show']);
    });

    // ------------------- RECLAMOS (Claims) -------------------
    Route::prefix('admin/claims')->group(function () {
        Route::get('/', [App\Http\Controllers\Support\ClaimController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Support\ClaimController::class, 'show']);
        Route::post('/{id}/reply', [App\Http\Controllers\Support\ClaimController::class, 'reply']);
        Route::put('/{id}/status', [App\Http\Controllers\Support\ClaimController::class, 'updateStatus']);
    });

    // ------------------- ROLES -------------------
    Route::prefix('admin/roles')->group(function () {
        Route::get('/', [App\Http\Controllers\Support\RoleController::class, 'index']);
    });

    // ------------------- MENSAJES DE CONTACTO -------------------
    Route::prefix('admin/contacto')->group(function () {
        Route::get('/', [App\Http\Controllers\Support\ContactMessageController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Support\ContactMessageController::class, 'show']);
        Route::delete('/{id}', [App\Http\Controllers\Support\ContactMessageController::class, 'destroy']);
    });

    // ------------------- LEADS -------------------
    Route::prefix('admin/leads')->group(function () {
        Route::post('/', [App\Http\Controllers\CRM\LeadController::class, 'store']);
        Route::get('/', [App\Http\Controllers\CRM\LeadController::class, 'index']);  
        Route::put('/{id}', [App\Http\Controllers\CRM\LeadController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\CRM\LeadController::class, 'destroy']);
    });

    // ------------------- PRODUCTOS -------------------
    Route::prefix('admin/productos')->group(function () {
        Route::post('/', [App\Http\Controllers\Product\ProductController::class, 'store']);
        Route::post('/{id}', [App\Http\Controllers\Product\ProductController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Product\ProductController::class, 'destroy']);
    });

    // ------------------- PRODUCTOS: EMAIL -------------------
    Route::prefix('admin/email-productos')->group(function () { // gestión de plantillas de email para productos
        Route::get('/', [App\Http\Controllers\Email\EmailProductController::class, 'indexByProduct']);
        Route::post('/', [App\Http\Controllers\Email\EmailProductController::class, 'store']);
        Route::delete('/', [App\Http\Controllers\Email\EmailProductController::class, 'destroy']);
});
    Route::prefix('admin/email-campanas')->group(function () {
        Route::post('/enviar-campana', [App\Http\Controllers\Email\EmailCampanaController::class, 'enviarCampana']);
    });

    // ------------------- PRODUCTOS: WHATSAPP -------------------
    Route::prefix('admin/whatsapp-productos')->group(function () { // gestión de plantillas de whatsapp para productos
        Route::get('/', [App\Http\Controllers\Whatsapp\WhatsappProductController::class, 'indexByProduct']);
        Route::post('/', [App\Http\Controllers\Whatsapp\WhatsappProductController::class, 'store']);
    });
    Route::prefix('admin/whatsapp-campanas')->group(function () { 
        Route::get('/status', [App\Http\Controllers\Whatsapp\WhatsappCampanaController::class, 'getStatus']);
        Route::post('/reset', [App\Http\Controllers\Whatsapp\WhatsappCampanaController::class, 'resetSession']);
        Route::post('/pedir-qr', [App\Http\Controllers\Whatsapp\WhatsappCampanaController::class, 'pedirQR']);
        Route::post('/enviar-campana', [App\Http\Controllers\Whatsapp\WhatsappCampanaController::class, 'enviarCampana']);
    });
});
