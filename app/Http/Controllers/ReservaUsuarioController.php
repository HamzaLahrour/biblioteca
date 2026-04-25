<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Models\Espacio;
use App\Models\TipoEspacio;
use App\Models\Reserva;
use App\Services\ReservaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ReservaUsuarioController extends Controller
{
    protected $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    public function index()
    {
        $tipos = TipoEspacio::withCount(['espacios' => function ($query) {
            $query->where('disponible', 1);
        }])->orderBy('nombre')->get();



        // Carga la vista desde la NUEVA carpeta
        return view('reservas_usuario.index', compact('tipos'));
    }

    public function create(TipoEspacio $tipo)
    {

        $reservasHoy = \App\Models\Reserva::whereHas('espacio', function ($q) use ($tipo) {
            $q->where('tipo_espacio_id', $tipo->id);
        })
            ->whereDate('fecha_reserva', \Carbon\Carbon::today())
            ->where('estado', 'activa')
            ->orderBy('hora_inicio', 'asc')
            ->get(['hora_inicio', 'hora_fin']);

        return view('reservas_usuario.create', compact('tipo', 'reservasHoy'));
    }

    public function comprobar(StoreReservaRequest $request, TipoEspacio $tipo)
    {
        try {
            // Le pasamos solo los datos validados al servicio
            $espacioLibre = $this->reservaService->buscarEspacioDisponible(
                $tipo->id,
                $request->validated(),
                Auth::id()
            );

            return view('reservas_usuario.confirmar', [
                'tipo' => $tipo,
                'espacio' => $espacioLibre,
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['error_reserva' => $e->getMessage()])->withInput();
        }
    }

    public function store(StoreReservaRequest $request)
    {
        try {
            $this->reservaService->crearReserva(
                $request->validated(),
                Auth::id()
            );

            return redirect()->route('perfil.index')->with('success', '¡Reserva confirmada con éxito!');
        } catch (Exception $e) {
            // Redirige usando el nuevo nombre de ruta
            return redirect()->route('reservas_usuario.index')->withErrors(['error_reserva' => $e->getMessage()]);
        }
    }
}
