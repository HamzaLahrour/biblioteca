<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreLibroRequest;
use App\Http\Requests\UpdateLibroRequest;
use App\Models\Libro;
use App\Models\Categoria;

class LibroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $libros = Libro::orderBy('titulo')->paginate(10);
        return view('libros.index', compact('libros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('libros.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLibroRequest $request)
    {
        Libro::create($request->validated());

        return redirect()->route('libros.index')->with('success', 'Libro creado con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Libro $libro)
    {
        $libro->load('prestamos');
        return view('libros.show', compact('libro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Libro $libro)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('libros.edit', compact('libro', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLibroRequest $request, Libro $libro)
    {
        $libro->fill($request->validated());

        if (!$libro->isDirty()) {
            return redirect()->route('libros.index')
                ->with('info', 'No realizaste ningún cambio.');
        }

        $libro->save();

        return redirect()->route('libros.index')
            ->with('success', 'El libro ha sido actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Libro $libro)
    {
        if ($libro->reservas()->count() > 0) {
            return redirect()->route('libros.index')
                ->with('error', 'No puedes borrar un libro que contenga reservas.');
        }

        $libro->delete();

        return redirect()->route('libros.index')
            ->with('success', 'El libro ha sido eliminado con éxito.');
    }
}
