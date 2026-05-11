<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        $categoriasPopulares = Categoria::withCount('libros')
            ->orderByDesc('libros_count')
            ->take(5)
            ->get();

        // los 8 más prestados
        $librosPopulares = Libro::with('categoria')
            ->withCount('prestamos')
            ->orderByDesc('prestamos_count')
            ->take(8)
            ->get();

        // categorías con al menos 4 libros para el escaparate
        $categoriasEscaparate = Categoria::has('libros', '>=', 4)
            ->withCount('libros')
            ->orderByDesc('libros_count')
            ->take(5)
            ->get();

        foreach ($categoriasEscaparate as $categoria) {
            $categoria->setRelation('libros_destacados', $categoria->libros()->take(8)->get());
        }

        $query = Libro::with('categoria');

        $query->when($request->buscar, function ($q, $buscar) {
            $q->where(function ($q2) use ($buscar) {
                $q2->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('autor', 'like', "%{$buscar}%");
            });
        });

        $query->when($request->categoria, function ($q, $categoriaId) {
            $q->where('categoria_id', $categoriaId);
        });

        $query->when($request->anio, function ($q, $anio) {
            $q->where('anio_publicacion', $anio);
        });

        $libros = $query->orderBy('titulo', 'asc')->paginate(12);

        return view('catalogo.index', compact(
            'libros',
            'categorias',
            'categoriasPopulares',
            'librosPopulares',
            'categoriasEscaparate'
        ));
    }

    public function show(\App\Models\Libro $libro)
    {
        $libro->load('categoria');

        return view('catalogo.show', compact('libro'));
    }
}
