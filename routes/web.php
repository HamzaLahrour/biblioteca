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

// 1. ZONA PÚBLICA (Sin loguear)

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de información para el acceso
Route::view('/recuperar-acceso', 'auth.info-password')->name('password.info');
Route::view('/solicitar-cuenta', 'auth.info-register')->name('register.info');

Route::prefix('usuarios')->group(function () {
    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'authenticate'])->name('usuarios.authenticate')->middleware('throttle:5,1');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
});



Route::middleware(['auth'])->group(function () {

    Route::post('/usuarios/logout', [UserController::class, 'logout'])->name('usuarios.logout');
    Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])->name('reservas.destroy');

    Route::post('/libros/{libro}/comentarios', [App\Http\Controllers\ComentarioController::class, 'store'])
        ->name('comentarios.store')
        ->middleware('auth');

    Route::post('/libros/{libro}/comentarios', [App\Http\Controllers\ComentarioController::class, 'store'])->name('comentarios.store')->middleware('auth');
    Route::put('/comentarios/{id}', [App\Http\Controllers\ComentarioController::class, 'update'])->name('comentarios.update')->middleware('auth');

    Route::delete('/comentarios/{id}', [App\Http\Controllers\ComentarioController::class, 'destroy'])->name('comentarios.destroy')->middleware('auth');

    //ZONA EXCLUSIVA ALUMNOS

    Route::middleware(['can:es_usuario'])->group(function () {

        Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');

        Route::get('/mi-espacio', [PerfilUsuarioController::class, 'index'])->name('perfil.index');

        Route::get('/mi-espacio/prestamos/historial', [PerfilUsuarioController::class, 'historialPrestamos'])
            ->name('perfil.prestamos.historial');


        Route::get('/reservar-espacio', [App\Http\Controllers\ReservaUsuarioController::class, 'index'])->name('reservas_usuario.index');

        Route::get('/reservar-espacio/tipo/{tipo}', [App\Http\Controllers\ReservaUsuarioController::class, 'create'])->name('reservas_usuario.create');

        Route::post('/reservar-espacio/tipo/{tipo}/comprobar', [App\Http\Controllers\ReservaUsuarioController::class, 'comprobar'])->name('reservas_usuario.comprobar');

        Route::post('/reservar-espacio/guardar', [App\Http\Controllers\ReservaUsuarioController::class, 'store'])->name('reservas_usuario.store');

        Route::post('/perfil/prestamos/{prestamo}/renovar', [App\Http\Controllers\PerfilUsuarioController::class, 'renovar'])->name('perfil.prestamos.renovar');
        Route::get('/mi-espacio/historial-reservas', [App\Http\Controllers\PerfilUsuarioController::class, 'historialReservas'])->name('perfil.reservas.historial');
        Route::get('/catalogo/{libro}', [CatalogoController::class, 'show'])->name('catalogo.show');
    });


    //ZONA EXCLUSIVA ADMIN 

    Route::middleware(['can:es_admin'])->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

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
        Route::resource('reservas', ReservaController::class)->only(['index', 'create', 'store', 'show',]);

        // Gestión de Préstamos (Admin)
        Route::resource('prestamos', PrestamoController::class)->except(['edit', 'update', 'destroy']);
        Route::prefix('prestamos/{prestamo}')->name('prestamos.')->group(function () {
            Route::post('/devolver', [PrestamoController::class, 'devolver'])->name('devolver');
            Route::post('/renovar', [PrestamoController::class, 'renovar'])->name('renovar');
            Route::post('/perdido', [PrestamoController::class, 'perdido'])->name('perdido');
        });

        Route::resource('festivos', App\Http\Controllers\FestivoController::class)->only(['index', 'store', 'destroy']);
    });
});
