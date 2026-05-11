<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;


class ComentarioController extends Controller
{
    public function store(Request $request, $libro_id)
    {
        $request->validate([
            'estrellas' => 'required|integer|min:1|max:5',
            'contenido' => 'nullable|string|max:1000',
        ]);

        $libro = \App\Models\Libro::findOrFail($libro_id);

        // un usuario solo puede comentar una vez por libro
        if (Comentario::where('user_id', auth()->id())->where('libro_id', $libro->id)->exists()) {
            return response()->json(['error' => 'Ya has comentado este libro.'], 400);
        }

        $comentario = Comentario::create([
            'user_id' => auth()->id(),
            'libro_id' => $libro->id,
            'estrellas' => $request->estrellas,
            'contenido' => $request->contenido,
        ]);

        // necesitamos el user cargado para pintarlo en el frontend sin recargar
        $comentario->load('user');

        return response()->json(['success' => true, 'mensaje' => 'Comentario publicado', 'comentario' => $comentario]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'estrellas' => 'required|integer|min:1|max:5',
            'contenido' => 'nullable|string|max:1000',
        ]);

        $comentario = \App\Models\Comentario::findOrFail($id);

        // que nadie pueda editar el comentario de otro
        if ($comentario->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        $comentario->update([
            'estrellas' => $request->estrellas,
            'contenido' => $request->contenido,
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Comentario actualizado', 'comentario' => $comentario]);
    }

    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);

        // el admin puede borrar cualquier comentario, el usuario solo el suyo
        $esDueño = $comentario->user_id === auth()->id();
        $esAdmin = auth()->user()->rol === 'admin';

        if (!$esDueño && !$esAdmin) {
            abort(403, 'No tienes permiso para borrar este comentario.');
        }

        $comentario->delete();

        return back()->with('success', 'Comentario eliminado correctamente.');
    }
}
