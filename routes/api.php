<?php

use App\Http\Controllers\Chatbot\ChatbotController;
use App\Http\Controllers\Popup\PopupController;
use App\Http\Controllers\PopupImage\PopupImageController;
use App\Http\Controllers\Template\TemplateController;
use Illuminate\Support\Facades\Route;

// Importación de Controladores
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
use App\Http\Controllers\Chatbot\ChatbotAdminController;
use App\Http\Controllers\Webhooks\WebhooksController;
use App\Http\Controllers\Deploy\DeployController;
use App\Http\Controllers\Settings\SettingsController;
use App\Http\Controllers\Template\ProductOverrideAssetController;
use App\Http\Controllers\Template\TemplateAssetController;
use App\Http\Controllers\Template\TemplateProductAssetController;
use App\Http\Controllers\Template\TemplateVariantController;

// ==============================================================================
//                              AUTENTICACIÓN
// ==============================================================================
Route::prefix('auth')->middleware('throttle:auth')->group(function () {
  // Público
  Route::post('login', [AuthController::class, 'login']);
  Route::post('refresh', [AuthController::class, 'refresh']);

  // Protegidas (Requieren Token)
  Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
  });
});

// ==============================================================================
//                          GESTIÓN DE CONTENIDO
// ==============================================================================

// ------------------- BLOGS -------------------
Route::prefix('blogs')->middleware('throttle:public')->group(function () {
  Route::get('/', [BlogController::class, 'index']);
  Route::get('/{slug}', [BlogController::class, 'show']);
});

// ------------------- PRODUCTOS -------------------
Route::prefix('productos')->middleware('throttle:public')->group(function () {
  Route::get('/', [ProductController::class, 'index']);
  Route::get('/{slug}', [ProductController::class, 'show']);
});

// ------------------- CATEGORÍAS (Público) -------------------
Route::prefix('categorias')->middleware('throttle:public')->group(function () {
  Route::get('/', [CategoryController::class, 'index']);
});

// ==============================================================================
//                          FORMULARIOS PÚBLICOS
// ==============================================================================

// ------------------- RECLAMOS (Claims) -------------------
Route::post('claims', [ClaimController::class, 'store'])->middleware('throttle:forms');

// ------------------- CONTACTO (Soporte) -------------------
Route::prefix('contacto')->middleware('throttle:forms')->group(function () {
  Route::post('/', [ContactMessageController::class, 'store']);
  });

  // ------------------- POPUP: EMAIL -------------------
  Route::prefix('email-popup')->middleware('throttle:forms')->group(function () {
    Route::post('/enviar', [EmailPopupController::class, 'enviar']);
    });

    // ------------------- POPUP: WHATSAPP -------------------
    Route::prefix('whatsapp-popup')->middleware('throttle:forms')->group(function () {
      Route::post('/enviar', [WhatsappPopupController::class, 'enviar']);
      });

      // ==============================================================================
      //                                WEBHOOKS
      // ==============================================================================
      // ------------------- DEPLOY FRONTEND -------------------
      Route::prefix('webhooks')->middleware('throttle:webhooks')->group(function () {
        Route::post('/deploy-frontend-complete', [WebhooksController::class, 'deployFrontend']);
        });

        // Chatbot
        Route::post('/chatbot/message', [ChatbotController::class, 'handle'])->middleware('throttle:forms');

        // Chatbot Whatsapp (entrada desde Baileys)
        Route::post('/chatbot/whatsapp', [ChatbotController::class, 'whatsapp'])
        ->middleware('throttle:webhooks');

        // ==============================================================================
        //                          ADMINISTRACIÓN (ADMIN PANEL)
        // ==============================================================================
Route::middleware(['auth:sanctum', 'role:admin|marketing|ventas', 'throttle:admin'])->group(function () {

  // ------------------- USUARIOS -------------------
  Route::prefix('admin/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::post('/{id}/role', [UserController::class, 'assignRole']);
  });

  // ------------------- CATEGORÍAS -------------------
  Route::prefix('admin/categorias')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
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
    Route::put('/{id}', [ContactMessageController::class, 'update']);
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
    Route::post('/', [BlogController::class, 'store']);
    Route::post('/{id}', [BlogController::class, 'update']);
    Route::delete('/{id}', [BlogController::class, 'destroy']);
  });

  // ------------------- PRODUCTOS -------------------
  Route::prefix('admin/productos')->middleware('throttle:uploads')->group(function () {
    Route::post('/', [ProductController::class, 'store']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
  });

  // ------------------- PRODUCTOS: EMAIL -------------------
  Route::prefix('admin/email-productos')->group(function () {
    Route::get('/', [EmailProductController::class, 'indexByProduct']);
    Route::post('/', [EmailProductController::class, 'store']);
    Route::delete('/', [EmailProductController::class, 'destroy']);
  });

  Route::prefix('admin/email-campanas')->group(function () {
    Route::post('/enviar-campana', [EmailCampanaController::class, 'enviarCampana']);
  });

  // ------------------- PRODUCTOS: WHATSAPP -------------------
  Route::prefix('admin/whatsapp-productos')->group(function () {
    Route::get('/', [WhatsappProductController::class, 'indexByProduct']);
    Route::post('/', [WhatsappProductController::class, 'store']);
  });

  Route::prefix('admin/whatsapp-campanas')->group(function () {
    Route::get('/status', [WhatsappCampanaController::class, 'getStatus']);
    Route::post('/reset', [WhatsappCampanaController::class, 'resetSession']);
    Route::post('/pedir-qr', [WhatsappCampanaController::class, 'pedirQR']);
    Route::post('/enviar-campana', [WhatsappCampanaController::class, 'enviarCampana']);
  });

  // ------------------- STATS DE MENSAJES -------------------
  // Route::prefix('admin/message-stats')->group(function () {
  //   Route::get('/', [MessageStatsController::class, 'index']);
  //   Route::get('/totals', [MessageStatsController::class, 'totals']);
  // });

  // ------------------- DEPLOY FRONTEND -------------------
  Route::prefix('admin/deploy')->group(function () {
    Route::post('/trigger', [DeployController::class, 'trigger']);
  });


  Route::prefix('admin/popups')->group(function () {
    Route::get('/', [PopupController::class, 'index']);
    Route::post('/', [PopupController::class, 'store']);

    Route::get('{id}', [PopupController::class, 'show']);
    Route::patch('{id}', [PopupController::class, 'update']);
    Route::delete('{id}', [PopupController::class, 'destroy']);
  });
  Route::prefix('admin/templates')->group(function() {

      Route::get('/variables', [TemplateController::class, 'variables']);
       Route::post('/upload-image', [TemplateAssetController::class, 'store']);
       Route::post('/product-assets/upload', [TemplateProductAssetController::class, 'upload']);
       Route::delete('/product-assets', [TemplateProductAssetController::class, 'destroy']);
       Route::post(
          '/product-overrides/upload',
          [ProductOverrideAssetController::class, 'store']
       );
       Route::get('/', [TemplateController::class, 'index']);
       Route::post('/', [TemplateController::class, 'store']);
       Route::get('/{id}', [TemplateController::class, 'show']);
       Route::put('/{id}', [TemplateController::class, 'update']);
       Route::delete('/{id}', [TemplateController::class, 'destroy']);
  });
  Route::prefix('admin/popup-images')->group(function(){
    Route::post('{id}', [PopupImageController::class, 'update']);
    Route::patch('{id}', [PopupImageController::class, 'update']);
  });

  Route::prefix('admin/settings')->group(function () {
    Route::patch('/general', [SettingsController::class, 'updateGeneral']);
    Route::patch('/contact', [SettingsController::class, 'updateContact']);
    Route::patch('/chatbot', [SettingsController::class, 'updateChatbot']);
  });

  Route::prefix('admin/chatbot/flows')->group(function () {

      Route::get('/', [ChatbotAdminController::class, 'index']);
      Route::post('/', [ChatbotAdminController::class, 'store']);
      Route::get('/{id}', [ChatbotAdminController::class, 'show']);
      Route::put('/{id}', [ChatbotAdminController::class, 'update']);
      Route::delete('/{id}', [ChatbotAdminController::class, 'destroy']);

      // 🔥 GRAPH EDITOR
      Route::get('/{id}/graph', [ChatbotAdminController::class, 'getGraph']);
      Route::post('/{id}/graph', [ChatbotAdminController::class, 'saveGraph']);
  });
});

Route::post('/leads/capture', [LeadController::class, 'capture']);

Route::get('popup', [PopupController::class, 'getPopup'])->middleware('throttle:public');

Route::get('settings', [SettingsController::class, 'index']);
