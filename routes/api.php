<?php

use Illuminate\Support\Facades\Route;

// ------------------- BLOGS -------------------
Route::prefix('blogs')->group(function () {
    Route::post('/', [App\Http\Controllers\BlogController::class, 'store']); // Crear blog
    // Otros endpoints de blogs (listado, detalle, etc.)
});

// ------------------- PRODUCTOS -------------------
Route::prefix('productos')->group(function () {
    // Ejemplo: Route::post('/', [App\Http\Controllers\ProductoController::class, 'store']);
    // Otros endpoints de productos
});

// ------------------- CATEGORÍAS (Público) -------------------
Route::prefix('categorias')->group(function () {
    // Endpoints públicos de categorías (listado, detalle, etc.)
});

// ------------------- ADMINISTRACIÓN DE CATEGORÍAS -------------------
Route::prefix('admin/categorias')->group(function () {
    Route::post('/', [App\Http\Controllers\Admin\CategoryController::class, 'store']);
    Route::put('/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy']);
    Route::get('/', [App\Http\Controllers\Admin\CategoryController::class, 'index']);
    Route::get('/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'show']);
});

// ------------------- CONTACTO -------------------
Route::prefix('contacto')->group(function () {
    // Endpoints de contacto
});

// ------------------- USUARIOS -------------------
Route::prefix('usuarios')->group(function () {
    // Endpoints de usuarios
});
