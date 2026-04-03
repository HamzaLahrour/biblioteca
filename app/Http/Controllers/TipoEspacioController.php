<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTipoEspacioRequest;
use App\Http\Requests\UpdateTipoEspacioRequest;
use App\Models\TipoEspacio;


class TipoEspacioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $tipoEspacios=TipoEspacio::orderBy('nombre')->paginate(10);
        return view('tipo_espacios.index', compact('tipoEspacios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipo_espacios.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTipoEspacioRequest $request)
    {
        TipoEspacio::create($request->validated());
        return redirect()->route('tipos_espacios.index')->with('succes','Tipo de Espacio creado con éxito');

    }

    /**
     * Display the specified resource.
     */
    public function show(TipoEspacio $tipoEspacio)
    {
        $tipoEspacio->load('espacios'); 
        return view('tipo_espacios.show', compact('tipoEspacio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoEspacio $tipoEspacio)
    {
        return view('tipo_espacios.edit',compact('tipoEspacio'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoEspacioRequest $request, TipoEspacio $tipoEspacio)
    {
         $tipoEspacio->fill($request->validated());

        if (!$tipoEspacio->isDirty()) {
            return redirect()->route('tipos_espacios.index')
            ->with('info', 'No realizaste ningún cambio.');
        }

        $tipoEspacio->save();

        return redirect()->route('tipos_espacios.index')
            ->with('success', 'Tipo Espacio actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoEspacio $tipoEspacio)
    {
        if ($tipoEspacio->espacios()->count() > 0) {
            return redirect()->route('tipos_espacios.index')
                             ->with('error', 'No puedes borrar un Tipo de Espacio que contenga espacios.');
        }

        $tipoEspacio->delete();

        return redirect()->route('tipos_espacios.index')
                         ->with('success', 'Tipo Espacio eliminado con éxito.');
    }
}
