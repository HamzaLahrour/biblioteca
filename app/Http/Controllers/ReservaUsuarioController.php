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
        return view('reservas_usuarios.index', compact('tipos'));
    }

    public function create(TipoEspacio $tipo)
    {
        return view('reservas_usuarios.create', compact('tipo'));
    }

    public function comprobar(Request $request, TipoEspacio $tipo)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $espacios = $tipo->espacios()->where('disponible', 1)->get();
        $espacioAsignado = null;

        foreach ($espacios as $espacio) {
            $solapamiento = Reserva::where('espacio_id', $espacio->id)
                ->where('fecha_reserva', $request->fecha)
                ->where('estado', 'activa')
                ->where('hora_inicio', '<', $request->hora_fin)
                ->where('hora_fin', '>', $request->hora_inicio)
                ->exists();

            if (!$solapamiento) {
                $espacioAsignado = $espacio;
                break;
            }
        }

        if (!$espacioAsignado) {
            return back()->withErrors(['error_reserva' => "No quedan espacios disponibles de tipo '{$tipo->nombre}' en este horario."])->withInput();
        }

        return view('reservas_usuarios.confirmar', [
            'tipo' => $tipo,
            'espacio' => $espacioAsignado,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);
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
            return redirect()->route('reservas_usuarios.index')->withErrors(['error_reserva' => $e->getMessage()]);
        }
    }
}
