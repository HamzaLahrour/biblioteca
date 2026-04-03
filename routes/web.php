<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\EspacioController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\TipoEspacioController;


// Auth
Route::prefix('usuarios')->group(function(){
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'authenticate'])->name('usuarios.authenticate');
    Route::post('/logout', [UserController::class, 'logout'])->name('usuarios.logout');
});
Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal (opcional, puedes redirigir a categorías)
    Route::get('/dashboard', function () {
        return view('layouts.admin'); // O una vista de bienvenida
    })->name('dashboard');

    // CRUD de Categorías (7 rutas en una sola línea)
    Route::resource('categorias', CategoriaController::class);
    Route::resource('espacios', EspacioController::class);
    Route::resource('tipos_espacios', TipoEspacioController::class)->parameters([
        'tipos_espacios' => 'tipoEspacio'
    ]);

    Route::resource('libros', LibroController::class);


    
    // Aquí irán más adelante:
    // Route::resource('libros', LibroController::class);
    // Route::resource('espacios', EspacioController::class);
});

