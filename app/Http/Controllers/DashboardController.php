<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libro;
use App\Models\User;
use App\Models\Prestamo;
use App\Models\Reserva;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        $totalLibros      = Libro::count();
        $totalUsuarios    = User::where('rol', '!=', 'admin')->count();
        $prestamosActivos = Prestamo::where('estado', 'activo')->count();
        $reservasHoyCount = Reserva::whereDate('fecha_reserva', $hoy)->count();

        // préstamos que ya han pasado su fecha límite y siguen sin devolver
        $prestamosVencidos = Prestamo::where('estado', '!=', 'devuelto')
            ->whereDate('fecha_devolucion_prevista', '<', $hoy)
            ->count();

        // préstamos que vencen en los próximos 3 días
        $prestamosProximos = Prestamo::where('estado', '!=', 'devuelto')
            ->whereDate('fecha_devolucion_prevista', '>=', $hoy)
            ->whereDate('fecha_devolucion_prevista', '<=', $hoy->copy()->addDays(3))
            ->count();

        $lectoresConPrestamo = User::where('rol', '!=', 'admin')
            ->whereHas('prestamos', fn($q) => $q->where('estado', 'activo'))
            ->count();

        $reservasHoy = Reserva::with(['user', 'espacio'])
            ->whereDate('fecha_reserva', $hoy)
            ->orderBy('hora_inicio', 'asc')
            ->get();

        // la siguiente reserva que aún no ha empezado
        $proximaReserva = $reservasHoy
            ->where('hora_inicio', '>=', Carbon::now()->format('H:i:s'))
            ->first();

        $prestamosUrgentes = Prestamo::with(['user', 'libro'])
            ->where('estado', '!=', 'devuelto')
            ->whereDate('fecha_devolucion_prevista', '<=', $hoy->copy()->addDays(3))
            ->orderBy('fecha_devolucion_prevista', 'asc')
            ->take(5)
            ->get();

        // últimos préstamos tocados, para el feed de actividad reciente
        $ultimosMovimientos = Prestamo::with(['user', 'libro'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        $librosTop = Libro::withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalLibros',
            'totalUsuarios',
            'prestamosActivos',
            'reservasHoyCount',
            'prestamosVencidos',
            'prestamosProximos',
            'lectoresConPrestamo',
            'reservasHoy',
            'proximaReserva',
            'prestamosUrgentes',
            'ultimosMovimientos',
            'librosTop',
        ));
    }
}
