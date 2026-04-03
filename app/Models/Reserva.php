<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    public function libro(){
        return $this->belongsTo(Libro::class);
    }

    public function espacio(){
        return $this->belongsTo(Espacio::class);
    }

    protected $fillable = [
    'usuario_id', 'espacio_id', 'fecha_reserva', 
    'hora_inicio', 'hora_fin', 'estado'
    ];
}
