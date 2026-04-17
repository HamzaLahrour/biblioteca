<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        // Traemos las categorías para los filtros
        $categorias = Categoria::orderBy('nombre')->get();

        // Consulta base (solo libros que existan, nada de borrados)
        $query = Libro::with('categoria');

        // Filtro 1: Buscador de texto
        $query->when($request->buscar, function ($q, $buscar) {
            $q->where(function ($q2) use ($buscar) {
                $q2->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('autor', 'like', "%{$buscar}%");
            });
        });

        // Filtro 2: Por categoría (Píldoras)
        $query->when($request->categoria, function ($q, $categoriaId) {
            $q->where('categoria_id', $categoriaId);
        });

        // Paginamos de 12 en 12 (múltiplo de 2, 3 y 4 para que la cuadrícula cuadre perfecta)
        $libros = $query->orderBy('titulo', 'asc')->paginate(12);

        return view('catalogo.index', compact('libros', 'categorias'));
    }
}
