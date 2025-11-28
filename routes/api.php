<?php

use Illuminate\Support\Facades\Route;



// ------------------- AUTHENTICATION -------------------
Route::prefix('auth')->group(function () {
    // Login es público
    Route::post('login', [App\Http\Controllers\Auth\AuthController::class, 'login']);

    // Rutas protegidas (Requieren Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [App\Http\Controllers\Auth\AuthController::class, 'me']);
        Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout']);
    });
});


// ------------------- BLOGS -------------------
Route::prefix('blogs')->group(function () {
    Route::get('/', [App\Http\Controllers\Blog\BlogController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Blog\BlogController::class, 'store']);
    Route::get('/{slug}', [App\Http\Controllers\Blog\BlogController::class, 'show']);
    Route::delete('/{id}', [App\Http\Controllers\Blog\BlogController::class, 'destroy']);
    Route::post('/{id}', [App\Http\Controllers\Blog\BlogController::class, 'update']);
    // Otros endpoints de blogs (listado, detalle, etc.)
});

// ------------------- PRODUCTOS -------------------
Route::prefix('productos')->group(function () {
    
    Route::get('/', [App\Http\Controllers\Product\ProductController::class, 'index']); 
    Route::post('/', [App\Http\Controllers\Product\ProductController::class, 'store']);
    Route::get('/{slug}', [App\Http\Controllers\Product\ProductController::class, 'show']);
    Route::post('/{id}', [App\Http\Controllers\Product\ProductController::class, 'update']); 
    Route::delete('/{id}', [App\Http\Controllers\Product\ProductController::class, 'destroy']);
    
    // Ejemplo: Route::post('/', [App\Http\Controllers\ProductoController::class, 'store']);
    // Otros endpoints de productos
});

// ------------------- CATEGORÍAS (Público) -------------------
Route::prefix('categorias')->group(function () {
    // Endpoints públicos de categorías (listado, detalle, etc.)
});

// ------------------- ADMINISTRACIÓN DE CATEGORÍAS -------------------
Route::prefix('admin/categorias')->group(function () {
    Route::post('/', [App\Http\Controllers\Admin\Category\CategoryController::class, 'store']);
    Route::put('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'destroy']);
    Route::get('/', [App\Http\Controllers\Admin\Category\CategoryController::class, 'index']);
    Route::get('/{id}', [App\Http\Controllers\Admin\Category\CategoryController::class, 'show']);
});

// ------------------- CONTACTO (Soporte) -------------------
Route::prefix('contacto')->group(function () {
Route::post('/', [App\Http\Controllers\Support\ContactMessageController::class, 'store']);
});
Route::prefix('contacto')->group(function () {
    // Endpoints de contacto
    Route::get('/', [App\Http\Controllers\Support\ContactMessageController::class, 'index']);
    Route::get('/{id}', [App\Http\Controllers\Support\ContactMessageController::class, 'show']);
    Route::delete('/{id}', [App\Http\Controllers\Support\ContactMessageController::class, 'destroy']);
});
// ------------------- ADMINISTRACIÓN DE USUARIOS -------------------
Route::prefix('admin/users')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy']);
    
    // Asignar rol manualmente
    Route::post('/{id}/role', [App\Http\Controllers\Admin\UserController::class, 'assignRole']);
});
// ------------------- USUARIOS -------------------
Route::prefix('usuarios')->group(function () {
    // Endpoints de usuarios
});
// ------------------- CRM / LEADS -------------------
Route::prefix('leads')->group(function () {
    Route::post('/', [App\Http\Controllers\CRM\LeadController::class, 'store']);
    Route::get('/', [App\Http\Controllers\CRM\LeadController::class, 'index']);  
});

// ------------------- RECLAMOS (Claims) -------------------
// Público
Route::post('claims', [App\Http\Controllers\Support\ClaimController::class, 'store']);

// Admin (Protegido)
Route::prefix('admin/claims')->group(function () {
    Route::get('/', [App\Http\Controllers\Support\ClaimController::class, 'index']);
    Route::get('/{id}', [App\Http\Controllers\Support\ClaimController::class, 'show']);
    Route::post('/{id}/reply', [App\Http\Controllers\Support\ClaimController::class, 'reply']);
    });