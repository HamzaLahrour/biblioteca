<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Reserva extends Model
{


    use HasUuids;



    public function espacio()
    {
        return $this->belongsTo(Espacio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id',
        'espacio_id',
        'fecha_reserva',
        'hora_inicio',
        'hora_fin',
        'estado'
    ];
}
