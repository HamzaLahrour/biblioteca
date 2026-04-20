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
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\PerfilUsuarioController;

// ==========================================
// 1. ZONA PÚBLICA (Sin loguear)
// ==========================================
Route::get('/', [CatalogoController::class, 'index'])->name('catalogo.index');

Route::prefix('usuarios')->group(function () {
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'authenticate'])->name('usuarios.authenticate')->middleware('throttle:5,1');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
});


// ==========================================
// ZONA PRIVADA (Requiere estar logueado)
// ==========================================
Route::middleware(['auth'])->group(function () {

    // Cerrar sesión (Común para Administradores y Usuarios)
    Route::post('/usuarios/logout', [UserController::class, 'logout'])->name('usuarios.logout');


    // ==========================================
    // 2. ZONA EXCLUSIVA ALUMNOS (Blindaje Usuario)
    // ==========================================
    Route::middleware(['can:es_usuario'])->group(function () {

        // Espacio del Alumno
        Route::get('/mi-espacio', [PerfilUsuarioController::class, 'index'])->name('perfil.index');

        // --- EL FLUJO DE RESERVAS ---
        // 1. Ver catálogo de Tipos de Espacio
        Route::get('/reservar-espacio', [App\Http\Controllers\ReservaUsuarioController::class, 'index'])->name('reservas_usuario.index');

        // 2. Formulario de fecha/hora
        Route::get('/reservar-espacio/tipo/{tipo}', [App\Http\Controllers\ReservaUsuarioController::class, 'create'])->name('reservas_usuario.create');

        // 3. Comprobar disponibilidad y asignar
        Route::post('/reservar-espacio/tipo/{tipo}/comprobar', [App\Http\Controllers\ReservaUsuarioController::class, 'comprobar'])->name('reservas_usuario.comprobar');

        // 4. Guardar definitivo
        Route::post('/reservar-espacio/guardar', [App\Http\Controllers\ReservaUsuarioController::class, 'store'])->name('reservas_usuario.store');

        // (Aquí meteremos luego las rutas para que el alumno reserve salas)
    });


    // ==========================================
    // 3. ZONA EXCLUSIVA ADMIN (Blindaje Administrador)
    // ==========================================
    Route::middleware(['can:es_admin'])->group(function () {

        Route::get('/dashboard', function () {
            return view('layouts.admin');
        })->name('dashboard');

        Route::get('/generar-password', function () {
            return response()->json(['password' => \Illuminate\Support\Str::password(8, letters: true, numbers: true, symbols: true)]);
        })->name('generar.password');

        // CRUDs de Admin
        Route::resource('categorias', CategoriaController::class);
        Route::resource('espacios', EspacioController::class);
        Route::resource('tipos_espacios', TipoEspacioController::class)->parameters(['tipos_espacios' => 'tipoEspacio']);
        Route::resource('libros', LibroController::class);
        Route::resource('usuarios', UserController::class);

        Route::get('/configuracion', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::put('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

        Route::get('/admin/festivos', [FestivoController::class, 'index'])->name('festivos.index');
        Route::post('/admin/festivos', [FestivoController::class, 'store'])->name('festivos.store');
        Route::delete('/admin/festivos/{id}', [FestivoController::class, 'destroy'])->name('festivos.destroy');

        // Gestión de Reservas (Admin)
        Route::resource('reservas', ReservaController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

        // Gestión de Préstamos (Admin)
        Route::resource('prestamos', PrestamoController::class)->except(['edit', 'update', 'destroy']);
        Route::prefix('prestamos/{prestamo}')->name('prestamos.')->group(function () {
            Route::post('/devolver', [PrestamoController::class, 'devolver'])->name('devolver');
            Route::post('/renovar', [PrestamoController::class, 'renovar'])->name('renovar');
            Route::post('/perdido', [PrestamoController::class, 'perdido'])->name('perdido');
        });
    });
});
