<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Página de productos para el frontend
Route::get('/productos-frontend', function () {
    return view('productos');
});

