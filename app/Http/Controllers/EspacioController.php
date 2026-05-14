<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Espacio;
use App\Http\Requests\StoreEspacioRequest;
use App\Http\Requests\UpdateEspacioRequest;
use App\Models\TipoEspacio;

class EspacioController extends Controller
{
    public function index()
    {
        // with() aquí para no hacer una query por cada espacio en la vista
        $query = \App\Models\Espacio::with('tipoEspacio');

        // busca por nombre, código o ubicación a la vez
        $query->when(request('buscar'), function ($q, $buscar) {
            $q->where(function ($q2) use ($buscar) {
                $q2->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigo', 'like', "%{$buscar}%")
                    ->orWhere('ubicacion', 'like', "%{$buscar}%");
            });
        });

        // filtra por tipo si viene el parámetro en la URL
        $query->when(request('tipo_espacio_id'), function ($q, $tipo) {
            $q->where('tipo_espacio_id', $tipo);
        });

        // disponible es un booleano en BD, convertimos el string del filtro
        $query->when(request()->filled('estado'), function ($q) {
            $estado = request('estado') === 'disponible' ? 1 : 0;
            $q->where('disponible', $estado);
        });

        $espacios = $query->orderBy('nombre', 'asc')->paginate(15);

        // los tipos van al desplegable del filtro superior
        $tipos = \App\Models\TipoEspacio::orderBy('nombre')->get();

        return view('espacios.index', compact('espacios', 'tipos'));
    }

    public function create()
    {
        // necesitamos los tipos para el select del formulario
        $tipos = TipoEspacio::orderBy('nombre')->get();
        return view('espacios.create', compact('tipos'));
    }

    public function store(StoreEspacioRequest $request)
    {
        // la validación ya la hace el FormRequest, aquí solo creamos
        Espacio::create($request->validated());

        return redirect()->route('espacios.index')->with('succes', 'Espacio creado con éxito');
    }

    public function show(Espacio $espacio)
    {
        // construimos la query sin ejecutarla todavía para poder aplicar filtros encima
        $query = $espacio->reservas()->with('user');

        // los tres filtros son opcionales, solo se aplican si vienen en la URL
        if (request()->filled('fecha_inicio')) {
            $query->whereDate('fecha_reserva', '>=', request('fecha_inicio'));
        }

        if (request()->filled('fecha_fin')) {
            $query->whereDate('fecha_reserva', '<=', request('fecha_fin'));
        }

        if (request()->filled('estado')) {
            $query->where('estado', request('estado'));
        }

        // estas métricas son independientes de los filtros, siempre muestran el total real
        $metricas = [
            'historico'   => $espacio->reservas()->count(),
            'activas_hoy' => $espacio->reservas()
                ->whereDate('fecha_reserva', today())
                ->where('estado', 'activa')
                ->count(),
            'canceladas'  => $espacio->reservas()->where('estado', 'cancelada')->count(),
        ];

        // las más recientes primero, y si coincide fecha desempatamos por hora
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->paginate(10);

        return view('espacios.show', compact('espacio', 'reservas', 'metricas'));
    }

    public function edit(Espacio $espacio)
    {
        $tipos = TipoEspacio::orderBy('nombre')->get();
        return view('espacios.edit', compact('espacio', 'tipos'));
    }

    public function update(UpdateEspacioRequest $request, Espacio $espacio)
    {
        $espacio->fill($request->validated());

        // si no tocó nada no tiene sentido lanzar el UPDATE
        if (!$espacio->isDirty()) {
            return redirect()->route('espacios.index')
                ->with('info', 'No realizaste ningún cambio.');
        }

        $espacio->save();

        return redirect()->route('espacios.index')
            ->with('success', 'Espacio actualizado correctamente.');
    }

    public function destroy(Espacio $espacio)
    {
        // no dejamos borrar si tiene reservas asociadas, para no romper el histórico
        if ($espacio->reservas()->count() > 0) {
            return redirect()->route('espacios.index')
                ->with('error', 'No puedes borrar un espacio que contenga reservas.');
        }

        $espacio->delete();

        return redirect()->route('espacios.index')
            ->with('success', 'Espacio eliminado con éxito.');
    }
}
