<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{

    protected $table = 'configuraciones';
    protected $fillable = ['seccion', 'clave', 'valor', 'tipo', 'etiqueta', 'descripcion'];

    // Helper estático para leer configuraciones fácil desde cualquier sitio
    public static function get(string $clave, $default = null): mixed
    {

        $config = self::where('clave', $clave)->first();

        if (!$config) return $default;

        // Casteamos el valor según el tipo
        return match ($config->tipo) {
            'integer' => (int) $config->valor,
            'boolean' => filter_var($config->valor, FILTER_VALIDATE_BOOLEAN),
            'float'   => (float) $config->valor,
            default   => $config->valor, // string, time, etc.
        };
    }
}
