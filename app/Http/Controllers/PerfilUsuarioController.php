<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\PrestamoService;
use App\Models\Prestamo;

class PerfilUsuarioController extends Controller
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

    public function renovar(Prestamo $prestamo, PrestamoService $prestamoService)
    {
        try {
            $prestamoService->renovarPrestamo($prestamo, Auth::id());

            return back()->with('success', 'Préstamo renovado correctamente. ¡Disfruta la lectura!');
        } catch (\Exception $e) {
            return back()->withErrors(['error_general' => $e->getMessage()]);
        }
    }
}
