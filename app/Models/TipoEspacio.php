<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;



class TipoEspacio extends Model
{
    // Usamos UUIDs
    use HasUuids;

    // Indicamos la tabla explícitamente
    protected $table = 'tipo_espacios';

    // Campos que el administrador puede rellenar masivamente
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    /**
     * RELACIONES
     * Un Tipo de Espacio puede tener asignados muchos Espacios físicos.
     */
    public function espacios(): HasMany
    {
        return $this->hasMany(Espacio::class);
    }
}
