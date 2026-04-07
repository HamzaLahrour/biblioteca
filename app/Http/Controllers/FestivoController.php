<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Festivo;


class FestivoController extends Controller
{
    public function index()
    {
        // Los traemos ordenados para que el calendario tenga sentido visualmente
        $festivos = Festivo::orderBy('fecha', 'asc')->get();

        // Cambia 'admin.festivos' por la ruta donde pongas tu vista HTML
        return view('admin.festivos', compact('festivos'));
    }

    // 2. Guardar un nuevo festivo en la base de datos
    public function store(Request $request)
    {
        // Validación directa (¡incluye la regla unique para que no repita días!)
        $request->validate([
            'fecha'  => ['required', 'date', 'unique:festivos,fecha'],
            'motivo' => ['required', 'string', 'max:255'],
        ], [
            'fecha.unique' => 'Este día ya está marcado como festivo en el calendario.'
        ]);

        // Guardado masivo gracias a que pusiste el $fillable en el Modelo
        Festivo::create([
            'fecha'  => $request->fecha,
            'motivo' => $request->motivo,
        ]);

        return back()->with('success', 'Día festivo añadido correctamente al sistema.');
    }

    // 3. Borrar un festivo (recibe el UUID mágicamente)
    public function destroy($id)
    {
        $festivo = Festivo::findOrFail($id);
        $festivo->delete();

        return back()->with('success', 'Día festivo eliminado. Ahora se podrán hacer reservas en esa fecha.');
    }
}
