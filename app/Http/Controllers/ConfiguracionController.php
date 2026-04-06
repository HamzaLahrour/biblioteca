<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;
use App\Http\Requests\UpdateConfiguracionRequest;



class ConfiguracionController extends Controller
{
    public function edit()
    {
        $configuraciones = Configuracion::all()->groupBy('seccion');
        return view('configuracion.edit', compact('configuraciones'));
    }

    public function update(UpdateConfiguracionRequest $request)
    {
        foreach ($request->configuraciones as $clave => $valor) {
            Configuracion::where('clave', $clave)->update(['valor' => $valor]);
        }

        return redirect()->route('configuracion.edit')
            ->with('success', 'Configuración guardada correctamente.');
    }
}
