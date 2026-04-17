<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Espacio;
use App\Http\Requests\StoreEspacioRequest;
use App\Http\Requests\UpdateEspacioRequest;
use App\Models\TipoEspacio;

class EspacioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cargamos la relación para evitar el problema N+1
        $query = \App\Models\Espacio::with('tipoEspacio');

        // 1. Buscador de Texto Libre (Nombre, Código o Ubicación)
        $query->when(request('buscar'), function ($q, $buscar) {
            $q->where(function ($q2) use ($buscar) {
                $q2->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigo', 'like', "%{$buscar}%")
                    ->orWhere('ubicacion', 'like', "%{$buscar}%");
            });
        });

        // 2. Filtro por Tipo de Espacio
        $query->when(request('tipo_espacio_id'), function ($q, $tipo) {
            $q->where('tipo_espacio_id', $tipo);
        });

        // 3. Filtro por Disponibilidad (Estado operativo)
        $query->when(request()->has('estado') && request('estado') !== '', function ($q) {
            $estado = request('estado') === 'disponible' ? 1 : 0;
            $q->where('disponible', $estado);
        });

        // Ordenamos alfabéticamente por nombre
        $espacios = $query->orderBy('nombre', 'asc')->paginate(15);

        // Traemos los tipos para el desplegable del filtro
        $tipos = \App\Models\TipoEspacio::orderBy('nombre')->get();

        return view('espacios.index', compact('espacios', 'tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipos = TipoEspacio::orderBy('nombre')->get();
        return view('espacios.create', compact('tipos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEspacioRequest $request)
    {
        Espacio::create($request->validated());

        return redirect()->route('espacios.index')->with('succes', 'Espacio creado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Espacio $espacio)
    {
        // 1. Preparamos la consulta (sin ejecutarla aún)
        $query = $espacio->reservas()->with('user');

        // 2. Aplicamos filtros dinámicos leyendo la URL con el helper global
        if (request()->filled('fecha_inicio')) {
            $query->whereDate('fecha_reserva', '>=', request('fecha_inicio'));
        }

        if (request()->filled('fecha_fin')) {
            $query->whereDate('fecha_reserva', '<=', request('fecha_fin'));
        }

        if (request()->filled('estado')) {
            $query->where('estado', request('estado'));
        }

        // 3. Calculamos las métricas para las tarjetas superiores
        $metricas = [
            'historico'   => $espacio->reservas()->count(),
            'activas_hoy' => $espacio->reservas()
                ->whereDate('fecha_reserva', today())
                ->where('estado', 'activa')
                ->count(),
            'canceladas'  => $espacio->reservas()->where('estado', 'cancelada')->count(),
        ];

        // 4. Ejecutamos la consulta final con ordenación y paginación
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->paginate(10);

        return view('espacios.show', compact('espacio', 'reservas', 'metricas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Espacio $espacio)
    {
        $tipos = TipoEspacio::orderBy('nombre')->get();
        return view('espacios.edit', compact('espacio', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEspacioRequest $request, Espacio $espacio)
    {
        $espacio->fill($request->validated());

        if (!$espacio->isDirty()) {
            return redirect()->route('espacios.index')
                ->with('info', 'No realizaste ningún cambio.');
        }

        $espacio->save();

        return redirect()->route('espacios.index')
            ->with('success', 'Espacio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Espacio $espacio)
    {
        if ($espacio->reservas()->count() > 0) {
            return redirect()->route('espacios.index')
                ->with('error', 'No puedes borrar un espacio que contenga reservas.');
        }

        $espacio->delete();

        return redirect()->route('espacios.index')
            ->with('success', 'Espacio eliminado con éxito.');
    }
}
