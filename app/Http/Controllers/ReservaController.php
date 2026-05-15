<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReservaStoreRequest;
use App\Http\Requests\ReservaUpdateRequest;
use App\Http\Requests\StoreReservaRequest;
use App\Services\ReservaService;
use App\Models\Reserva;
use App\Models\Espacio;
use App\Models\TipoEspacio;
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

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_espacio_id')) {
            $query->whereHas('espacio', function ($q) use ($request) {
                $q->where('tipo_espacio_id', $request->tipo_espacio_id);
            });
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_reserva', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_reserva', '<=', $request->fecha_fin);
        }

        if ($request->filled('buscar_usuario')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->buscar_usuario . '%')
                    ->orWhere('email', 'like', '%' . $request->buscar_usuario . '%');
            });
        }

        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tipos_espacios = TipoEspacio::orderBy('nombre')->get();

        return view('reservas.index', compact('reservas', 'tipos_espacios'));
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
            $datosValidados = $request->validated();
            $usuarioActual = auth()->id();

            $reserva = $this->reservaService->crearReserva($datosValidados, $usuarioActual);

            return redirect()->route('reservas.index')
                ->with('success', '¡Reserva confirmada con éxito para el día ' . \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') . '!');
        } catch (Exception $e) {

            return back()
                ->withErrors(['error_reserva' => $e->getMessage()])
                ->withInput();
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



        $reserva->delete();

        return redirect()->back()->with('success', 'Reserva cancelada correctamente.');
    }
}
