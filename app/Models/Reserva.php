<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;



class Reserva extends Model
{


    use HasUuids;

    public function getEstadoAttribute($value)
    {
        if ($value === 'activa') {
            $fechaHoraFin = Carbon::parse($this->fecha_reserva . ' ' . $this->hora_fin);
            if ($fechaHoraFin->isPast()) {
                return 'finalizada';
            }
        }
        return $value;
    }


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
