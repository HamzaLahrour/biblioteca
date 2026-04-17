<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerfilAlumnoController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        // 1. Préstamos Activos (Libros que tiene en casa ahora mismo)
        $prestamos = $usuario->prestamos()
            ->with('libro.categoria')
            ->whereIn('estado', ['activo', 'devuelto_tarde'])
            ->orderBy('fecha_devolucion_prevista', 'asc')
            ->get();

        // 2. Reservas Próximas (Salas que ha pedido de hoy en adelante)
        $reservas = $usuario->reservas()
            ->with('espacio')
            ->where('estado', 'activa')
            ->whereDate('fecha_reserva', '>=', Carbon::today())
            ->orderBy('fecha_reserva', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();

        // 3. Comprobar si tiene alguna sanción activa hoy
        $sancionActiva = $usuario->sanciones()
            ->whereDate('fecha_fin', '>=', Carbon::today())
            ->first();

        return view('perfil.index', compact('usuario', 'prestamos', 'reservas', 'sancionActiva'));
    }
}
