<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreLibroRequest;
use App\Http\Requests\UpdateLibroRequest;
use App\Models\Libro;
use App\Models\Categoria;
use App\Models\Prestamo;

class LibroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Iniciamos la consulta cargando la relación para evitar N+1
        $query = \App\Models\Libro::with('categoria');

        // 1. Buscador de Texto Libre (Título, Autor o ISBN)
        $query->when(request('buscar'), function ($q, $buscar) {
            $q->where(function ($q2) use ($buscar) {
                $q2->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('autor', 'like', "%{$buscar}%")
                    ->orWhere('isbn', 'like', "%{$buscar}%");
            });
        });

        // 2. Filtro de Categoría
        $query->when(request('categoria_id'), function ($q, $categoria) {
            $q->where('categoria_id', $categoria);
        });



        // Ordenamos alfabéticamente por defecto
        $libros = $query->orderBy('titulo', 'asc')->paginate(10);

        // Traemos las categorías para llenar el desplegable del filtro
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();

        return view('libros.index', compact('libros', 'categorias'));
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

        // 1. CALCULADORA DE MINI-ESTADÍSTICAS (Sobre TODOS los préstamos del libro)
        $stats = [
            'total'     => $libro->prestamos()->count(),
            'activos'   => $libro->prestamos()->where('estado', 'activo')->count(),
            'devueltos' => $libro->prestamos()->whereIn('estado', ['devuelto', 'devuelto_tarde'])->count(),
            'perdidos'  => $libro->prestamos()->where('estado', 'perdido')->count(),
        ];

        // 2. DISPONIBILIDAD REAL
        // Si tu modelo no tiene un campo 'disponibles', lo calculamos al vuelo:
        $disponibles = $libro->disponibles ?? ($libro->copias_totales - $stats['activos']);

        // 3. CONSULTA DE PRÉSTAMOS PARA LA TABLA
        $query = $libro->prestamos()->with('user');

        $query->when(request('buscar_lector'), function ($q, $buscar) {
            $q->whereHas('user', function ($q2) use ($buscar) {
                $q2->where('name', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            });
        });

        $query->when(request('estado_prestamo'), function ($q, $estado) {
            $q->where('estado', $estado);
        });

        $prestamos = $query->orderBy('fecha_prestamo', 'desc')->paginate(10);

        return view('libros.show', compact('libro', 'prestamos', 'stats', 'disponibles'));
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
        if ($libro->prestamos()->count() > 0) {
            return redirect()->route('libros.index')
                ->with('error', 'No puedes borrar un libro que contenga prestamos.');
        }

        $libro->delete();

        return redirect()->route('libros.index')
            ->with('success', 'El libro ha sido eliminado con éxito.');
    }
}
