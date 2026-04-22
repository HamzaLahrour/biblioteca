<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Todas las categorías para el Offcanvas
        $categorias = Categoria::orderBy('nombre')->get();

        // 2. Categorías Populares (Píldoras)
        $categoriasPopulares = Categoria::withCount('libros')
            ->orderByDesc('libros_count')
            ->take(5)
            ->get();

        // 3. TENDENCIAS: Los 8 libros más prestados (o destacados)
        // (Ajusta 'prestamos' al nombre real de tu relación si es diferente)
        $librosPopulares = Libro::with('categoria')
            ->withCount('prestamos')
            ->orderByDesc('prestamos_count')
            ->take(8)
            ->get();

        // 4. ESCAPARATE NETFLIX (El consejo del profe)
        // Cogemos 3 categorías con bastantes libros para hacer las filas de scroll
        $categoriasEscaparate = Categoria::has('libros', '>=', 4)
            ->withCount('libros')
            ->orderByDesc('libros_count')
            ->take(5)
            ->get();

        // A cada categoría le cargamos sus 8 libros más recientes/populares
        foreach ($categoriasEscaparate as $categoria) {
            $categoria->setRelation('libros_destacados', $categoria->libros()->take(8)->get());
        }

        // 5. Catálogo general con sus filtros
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
}
