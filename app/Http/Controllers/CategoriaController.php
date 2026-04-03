<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias=Categoria::orderBy('nombre')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create($request->validated());

        return redirect()->route('categorias.index')->with('succes','Categoria creada con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        $categoria->load('libros'); 
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit',compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->fill($request->validated());

        if (!$categoria->isDirty()) {
            return redirect()->route('categorias.index')
            ->with('info', 'No realizaste ningún cambio.');
        }

        $categoria->save();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        // Opcional: Proteger para que no se borre si tiene libros
        if ($categoria->libros()->count() > 0) {
            return redirect()->route('categorias.index')
                             ->with('error', 'No puedes borrar una categoría que contiene libros.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
                         ->with('success', 'Categoría eliminada con éxito.');
    }
}
