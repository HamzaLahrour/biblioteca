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
use Illuminate\Support\Facades\Auth;

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
    public function index(Request $request)
    {
        // Iniciamos la consulta cargando las relaciones para no saturar la BD
        $query = Reserva::with(['user', 'espacio']);

        // 1. Filtrar por Estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // 2. Filtrar por Espacio
        if ($request->filled('espacio_id')) {
            $query->where('espacio_id', $request->espacio_id);
        }

        // 3. Filtrar por Rango de Fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_reserva', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_reserva', '<=', $request->fecha_fin);
        }

        // 4. Filtrar por Usuario (Buscador por nombre o email)
        if ($request->filled('buscar_usuario')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->buscar_usuario . '%')
                    ->orWhere('email', 'like', '%' . $request->buscar_usuario . '%');
            });
        }

        // Ordenamos por fecha más reciente y paginamos (conservando los filtros en la URL)
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Mandamos los espacios para rellenar el `<select>` del filtro
        $espacios = Espacio::orderBy('nombre')->get();

        return view('reservas.index', compact('reservas', 'espacios'));
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
                ->with('success', '¡Reserva confirmada con éxito para el día ' . \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') . '!');
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

        // Valida que si no eres dueño de la reserva ni admin no puedes cancelar 
        if (Auth::user()->rol !== 'admin' && Auth::user()->id !== $reserva->user_id) {
            return redirect()->back()->with('error', 'No tienes permiso para cancelar esta reserva.');
        }



        // 3. LA EJECUCIÓN
        $reserva->delete();

        return redirect()->back()->with('success', 'Reserva cancelada correctamente.');
    }
}
