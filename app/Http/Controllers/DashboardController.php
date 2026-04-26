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

        // --- Métricas base (igual que antes) ---
        $totalLibros      = Libro::count();
        $totalUsuarios    = User::where('rol', '!=', 'admin')->count();
        $prestamosActivos = Prestamo::where('estado', 'activo')->count();
        $reservasHoyCount = Reserva::whereDate('fecha_reserva', $hoy)->count();

        // --- NUEVO: contexto para las tarjetas métricas ---
        $prestamosVencidos = Prestamo::where('estado', '!=', 'devuelto')
            ->whereDate('fecha_devolucion_prevista', '<', $hoy)
            ->count();

        $prestamosProximos = Prestamo::where('estado', '!=', 'devuelto')
            ->whereDate('fecha_devolucion_prevista', '>=', $hoy)
            ->whereDate('fecha_devolucion_prevista', '<=', $hoy->copy()->addDays(3))
            ->count();

        // Lectores que tienen préstamo activo ahora mismo
        $lectoresConPrestamo = User::where('rol', '!=', 'admin')
            ->whereHas('prestamos', fn($q) => $q->where('estado', 'activo'))
            ->count();
        // --- Reservas de hoy (igual que antes, sirve también para el aforo agrupado) ---
        $reservasHoy = Reserva::with(['user', 'espacio'])
            ->whereDate('fecha_reserva', $hoy)
            ->orderBy('hora_inicio', 'asc')
            ->get();

        // Próxima reserva del día (para subtext de la tarjeta)
        $proximaReserva = $reservasHoy
            ->where('hora_inicio', '>=', Carbon::now()->format('H:i:s'))
            ->first();

        // --- Préstamos urgentes (igual que antes) ---
        $prestamosUrgentes = Prestamo::with(['user', 'libro'])
            ->where('estado', '!=', 'devuelto')
            ->whereDate('fecha_devolucion_prevista', '<=', $hoy->copy()->addDays(3))
            ->orderBy('fecha_devolucion_prevista', 'asc')
            ->take(5)
            ->get();

        // --- NUEVO: últimos movimientos (actividad reciente) ---
        $ultimosMovimientos = Prestamo::with(['user', 'libro'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        // --- NUEVO: libros más prestados ---
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
