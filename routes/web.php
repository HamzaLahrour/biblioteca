<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\EspacioController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\TipoEspacioController;
use App\Http\Controllers\FestivoController;


// Auth
Route::prefix('usuarios')->group(function () {
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'authenticate'])->name('usuarios.authenticate')->middleware('throttle:5,1');;
    Route::post('/logout', [UserController::class, 'logout'])->name('usuarios.logout');
});
Route::middleware(['auth'])->group(function () {

    // Dashboard principal (opcional, puedes redirigir a categorías)
    Route::get('/dashboard', function () {
        return view('layouts.admin'); // O una vista de bienvenida
    })->name('dashboard');

    Route::get('/generar-password', function () {
        return response()->json([
            'password' => \Illuminate\Support\Str::password(8, letters: true, numbers: true, symbols: true)
        ]);
    })->name('generar.password');

    // CRUD de Categorías (7 rutas en una sola línea)
    Route::resource('categorias', CategoriaController::class);
    Route::resource('espacios', EspacioController::class);
    Route::resource('tipos_espacios', TipoEspacioController::class)->parameters([
        'tipos_espacios' => 'tipoEspacio'
    ]);

    Route::resource('libros', LibroController::class);
    Route::resource('usuarios', UserController::class);
    Route::get('/configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
    Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

    Route::get('/admin/festivos', [FestivoController::class, 'index'])->name('festivos.index');
    Route::post('/admin/festivos', [FestivoController::class, 'store'])->name('festivos.store');
    Route::delete('/admin/festivos/{id}', [FestivoController::class, 'destroy'])->name('festivos.destroy');



    // Aquí irán más adelante:
    // Route::resource('libros', LibroController::class);
    // Route::resource('espacios', EspacioController::class);
});
