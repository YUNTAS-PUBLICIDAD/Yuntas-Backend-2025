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
//                      AUTENTICACIÓN
// ==============================================================================
Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    // público
    Route::post('login', [AuthController::class, 'login']);
    // Rutas protegidas (Requieren Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
// ==============================================================================
//                       GESTIÓN DE CONTENIDO 
// ==============================================================================

// ------------------- BLOGS -------------------
Route::prefix('blogs')->middleware('throttle:public')->group(function () {
    Route::get('/', [App\Http\Controllers\Blog\BlogController::class, 'index']);
    Route::get('/{slug}', [App\Http\Controllers\Blog\BlogController::class, 'show']);
});

// ------------------- PRODUCTOS -------------------
Route::prefix('productos')->middleware('throttle:public')->group(function () {
    Route::get('/', [App\Http\Controllers\Product\ProductController::class, 'index']); 
    Route::get('/{slug}', [App\Http\Controllers\Product\ProductController::class, 'show']);
});

// ------------------- CATEGORÍAS (Público) -------------------
Route::prefix('categorias')->middleware('throttle:public')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\Category\CategoryController::class, 'index']);
});

// ==============================================================================
//                         FORMULARIOS PÚBLICOS
// ==============================================================================
// ------------------- RECLAMOS (Claims) -------------------
Route::post('claims', [App\Http\Controllers\Support\ClaimController::class, 'store'])->middleware('throttle:forms');
// ------------------- CONTACTO (Soporte) -------------------
Route::prefix('contacto')->middleware('throttle:forms')->group(function () {
    Route::post('/', [App\Http\Controllers\Support\ContactMessageController::class, 'store']);
});

// ------------------- POPUP: EMAIL -------------------
Route::prefix('email-popup')->middleware('throttle:forms')->group(function () {
    Route::post('/enviar', [App\Http\Controllers\Email\EmailPopupController::class, 'enviar']);
});

// ------------------- POPUP: WHATSAPP -------------------
Route::prefix('whatsapp-popup')->middleware('throttle:forms')->group(function () { 
    Route::post('/enviar', [App\Http\Controllers\Whatsapp\WhatsappPopupController::class, 'enviar']);
});

// ==============================================================================
//                              WEBHOOKS
// ==============================================================================
// ------------------- DEPLOY FRONTEND -------------------
Route::prefix('webhooks')->middleware('throttle:webhooks')->group(function () {
    Route::post('/deploy-frontend-complete', [App\Http\Controllers\Webhooks\WebhooksController::class, 'deployFrontend']);
});

// ==============================================================================
//                          ADMINISTRACIÓN (ADMIN PANEL)
// ==============================================================================
Route::middleware(['auth:sanctum', 'role:admin|marketing|ventas', 'throttle:admin'])->group(function () {

    // ------------------- USUARIOS -------------------
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show']);
        Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
        Route::post('/{id}/role', [App\Http\Controllers\Admin\UserController::class, 'assignRole']);
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

    // ------------------- BLOGS -------------------
    Route::prefix('admin/blogs')->group(function () {
        Route::post('/', [App\Http\Controllers\Blog\BlogController::class, 'store']);
        Route::delete('/{id}', [App\Http\Controllers\Blog\BlogController::class, 'destroy']);
        Route::put('/{id}', [App\Http\Controllers\Blog\BlogController::class, 'update']);
    });

    // ------------------- PRODUCTOS -------------------
    Route::prefix('admin/productos')->middleware('throttle:uploads')->group(function () {
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

    // ------------------- DEPLOY FRONTEND -------------------
    Route::prefix('admin/deploy')->group(function () {
        Route::post('/trigger', [App\Http\Controllers\Deploy\DeployController::class, 'trigger']);
    });
});
