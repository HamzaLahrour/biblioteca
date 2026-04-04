<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Reserva extends Model
{


    use HasUuids;


    public function libro()
    {
        return $this->belongsTo(Libro::class);
    }

    public function espacio()
    {
        return $this->belongsTo(Espacio::class);
    }

    protected $fillable = [
        'usuario_id',
        'espacio_id',
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'estado'
    ];
}
