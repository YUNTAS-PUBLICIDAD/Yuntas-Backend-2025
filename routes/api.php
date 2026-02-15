<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Admin\Category\CategoryController;
use App\Http\Controllers\Support\ClaimController;
use App\Http\Controllers\Support\ContactMessageController;
use App\Http\Controllers\Email\EmailPopupController;
use App\Http\Controllers\Whatsapp\WhatsappPopupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Support\RoleController;
use App\Http\Controllers\CRM\LeadController;
use App\Http\Controllers\Email\EmailProductController;
use App\Http\Controllers\Email\EmailCampanaController;
use App\Http\Controllers\Whatsapp\WhatsappProductController;
use App\Http\Controllers\Whatsapp\WhatsappCampanaController;
use App\Http\Controllers\Admin\MessageStatsController;

// ==============================================================================
// 1. AUTENTICACIÓN (IAM)
// ==============================================================================
// ------------------- AUTHENTICATION -------------------
Route::prefix('auth')->group(function () {
    // público
    Route::post('login', [AuthController::class, 'login']);
    // Rutas protegidas (Requieren Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
// ==============================================================================
// 2. GESTIÓN DE CONTENIDO (CMS)
// ==============================================================================

// ------------------- BLOGS -------------------
Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{slug}', [BlogController::class, 'show']);
});

// ------------------- PRODUCTOS -------------------
Route::prefix('productos')->group(function () {
    Route::get('/', [ProductController::class, 'index']); 
    Route::get('/{slug}', [ProductController::class, 'show']);
});

// ------------------- CATEGORÍAS (Público) -------------------
Route::prefix('categorias')->group(function () {
    // Endpoints públicos de categorías (listado, detalle, etc.)
    Route::get('/', [CategoryController::class, 'index']);
});

// ==============================================================================
// 3. FORMULARIOS PÚBLICOS (CRM & SOPORTE)
// ==============================================================================
// ------------------- RECLAMOS (Claims) -------------------
Route::post('claims', [ClaimController::class, 'store']);
// ------------------- CONTACTO (Soporte) -------------------
Route::prefix('contacto')->group(function () {
    Route::post('/', [ContactMessageController::class, 'store']);
});

// ------------------- POPUP: EMAIL -------------------
Route::prefix('email-popup')->group(function () {
    Route::post('/enviar', [EmailPopupController::class, 'enviar']);
});

// ------------------- POPUP: WHATSAPP -------------------
Route::prefix('whatsapp-popup')->group(function () { 
    Route::post('/enviar', [WhatsappPopupController::class, 'enviar']);
});

// ==============================================================================
//                          ADMINISTRACIÓN (ADMIN PANEL)
// ==============================================================================
Route::middleware(['auth:sanctum', 'role:admin|marketing|ventas'])->group(function () {

    // ------------------- USUARIOS -------------------
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        
        // Asignar rol manualmente
        Route::post('/{id}/role', [UserController::class, 'assignRole']);
    });

    // ------------------- CATEGORÍAS -------------------
    Route::prefix('admin/categorias')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::get('/{id}', [CategoryController::class, 'show']);
    });

    // ------------------- RECLAMOS (Claims) -------------------
    Route::prefix('admin/claims')->group(function () {
        Route::get('/', [ClaimController::class, 'index']);
        Route::get('/{id}', [ClaimController::class, 'show']);
        Route::post('/{id}/reply', [ClaimController::class, 'reply']);
        Route::put('/{id}/status', [ClaimController::class, 'updateStatus']);
    });

    // ------------------- ROLES -------------------
    Route::prefix('admin/roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
    });

    // ------------------- MENSAJES DE CONTACTO -------------------
    Route::prefix('admin/contacto')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index']);
        Route::get('/{id}', [ContactMessageController::class, 'show']);
        Route::delete('/{id}', [ContactMessageController::class, 'destroy']);
    });

    // ------------------- LEADS -------------------
    Route::prefix('admin/leads')->group(function () {
        Route::post('/', [LeadController::class, 'store']);
        Route::get('/', [LeadController::class, 'index']);  
        Route::put('/{id}', [LeadController::class, 'update']);
        Route::delete('/{id}', [LeadController::class, 'destroy']);
    });

    // ------------------- PRODUCTOS -------------------
    Route::prefix('admin/productos')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/{id}', [ProductController::class, 'update']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });

    // ------------------- BLOGS -------------------
    Route::prefix('admin/blogs')->group(function () {
        Route::post('/', [BlogController::class, 'store']);
        Route::put('/{id}', [BlogController::class, 'update']);
        Route::delete('/{id}', [BlogController::class, 'destroy']);
    });

    // ------------------- PRODUCTOS: EMAIL -------------------
    Route::prefix('admin/email-productos')->group(function () { // gestión de plantillas de email para productos
        Route::get('/', [EmailProductController::class, 'indexByProduct']);
        Route::post('/', [EmailProductController::class, 'store']);
    });
    Route::prefix('admin/email-campanas')->group(function () {
        Route::post('/enviar-campana', [EmailCampanaController::class, 'enviarCampana']);
    });

    // ------------------- PRODUCTOS: WHATSAPP -------------------
    Route::prefix('admin/whatsapp-productos')->group(function () { // gestión de plantillas de whatsapp para productos
        Route::get('/', [WhatsappProductController::class, 'indexByProduct']);
        Route::post('/', [WhatsappProductController::class, 'store']);
    });
    Route::prefix('admin/whatsapp-campanas')->group(function () { 
        Route::get('/status', [WhatsappCampanaController::class, 'getStatus']);
        Route::post('/reset', [WhatsappCampanaController::class, 'resetSession']);
        Route::post('/pedir-qr', [WhatsappCampanaController::class, 'pedirQR']);
        Route::post('/enviar-campana', [WhatsappCampanaController::class, 'enviarCampana']);
    });

    // ------------------- STATS DE MENSAJES (WHATSAPP Y EMAIL) -------------------
    Route::prefix('admin/message-stats')->group(function () {
        Route::get('/', [MessageStatsController::class, 'index']); // Stats por lead
        Route::get('/totals', [MessageStatsController::class, 'totals']); // Stats globales
    });
});
