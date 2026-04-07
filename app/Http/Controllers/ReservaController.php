<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReservaStoreRequest;
use App\Http\Requests\ReservaUpdateRequest;
use App\Http\Requests\StoreReservaRequest;
use App\Services\ReservaService;
use App\Models\Reserva;
use App\Models\Espacio;
use App\Models\User;

use Exception;

class ReservaController extends Controller
{


    protected $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuario = auth()->user();

        // Si es admin, ve TODAS las reservas. Si es alumno, solo las suyas.
        if ($usuario->rol === 'admin') {
            // Usamos 'with' para evitar el problema de N+1 consultas (Optimización)
            $reservas = Reserva::with(['user', 'espacio'])->orderBy('fecha_reserva', 'desc')->paginate(10);
        } else {
            $reservas = $usuario->reservas()->with('espacio')->orderBy('fecha_reserva', 'desc')->paginate(10);
        }

        return view('reservas.index', compact('reservas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $espacios = Espacio::where('disponible', true)->get();

        $usuarios = [];
        if (auth()->user()->rol === 'admin') {
            $usuarios = User::orderBy('name')->get();
        }

        return view('reservas.create', compact('espacios', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservaRequest $request)
    {
        try {
            // 1. El Request ya validó que no haya "ruedas de tractor"
            $datosValidados = $request->validated();
            $usuarioActual = auth()->id();

            // 2. Le pasamos los datos limpios al Chef (El Servicio)
            $reserva = $this->reservaService->crearReserva($datosValidados, $usuarioActual);

            // 3. Si el Chef no grita, todo ha ido bien. El mesero sirve la respuesta feliz.
            return redirect()->route('reservas.index')
                ->with('success', '¡Reserva confirmada con éxito para el día ' . \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') . '!');
        } catch (Exception $e) {
            // 4. ¡EL CHEF HA GRITADO! (Ej: "¡Es festivo!" o "¡Aforo completo!")
            // El mesero atrapa el error y devuelve al usuario al formulario amablemente
            return back()
                ->withErrors(['error_reserva' => $e->getMessage()])
                ->withInput(); // Mantiene los datos que había rellenado para que no los pierda
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        if (auth()->id() !== $reserva->user_id && auth()->user()->rol !== 'admin') {
            abort(403, 'No tienes permiso para ver los detalles de esta reserva.');
        }

        // Carga las relaciones para que en la vista podamos poner:
        // $reserva->espacio->nombre o $reserva->user->name
        // Usamos load() porque ya tenemos la instancia de $reserva (Route Model Binding)
        $reserva->load(['user', 'espacio']);

        return view('reservas.show', compact('reserva'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        // 1. Barrera de seguridad: ¡Nada de cancelar la reserva del vecino!
        if (auth()->id() !== $reserva->user_id && auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso denegado: No puedes cancelar reservas de otra persona.');
        }

        // 2. Control de listillos: No puedes cancelar una reserva que ya pasó
        if (\Carbon\Carbon::parse($reserva->fecha . ' ' . $reserva->hora_inicio)->isPast()) {
            return back()->withErrors(['error_general' => 'No puedes cancelar una reserva que ya ha comenzado o pasado.']);
        }

        // 3. Cancelamos (liberamos el espacio mágicamente para todos los demás)
        $reserva->update(['estado' => 'cancelada']);

        return redirect()->route('reservas.index')
            ->with('success', 'Tu reserva ha sido cancelada. El espacio vuelve a estar libre.');
    }
}
