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
        $espacios=Espacio::orderBy('nombre')->paginate(10);
        return view('espacios.index', compact('espacios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tipos = TipoEspacio::orderBy('nombre')->get();
        return view('espacios.create',compact('tipos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEspacioRequest $request)
    {
        Espacio::create($request->validated());
        
        return redirect()->route('espacios.index')->with('succes','Espacio creado con éxito');

    }

    /**
     * Display the specified resource.
     */
    public function show(Espacio $espacio)
    {
        $espacio->load('reservas'); 
        return view('espacios.show', compact('espacio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Espacio $espacio)
    {
        $tipos = TipoEspacio::orderBy('nombre')->get();
        return view('espacios.edit',compact('espacio','tipos'));

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
