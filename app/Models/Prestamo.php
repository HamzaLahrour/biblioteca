<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Prestamo extends Model
{


    use HasUuids;


    public function libro()
    {
        return $this->belongsTo(Libro::class);
    }
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function sancion(): HasOne
    {
        return $this->hasOne(Sancion::class);
    }

    protected $fillable = [
        'usuario_id',
        'libro_id',
        'fecha_prestamo',
        'fecha_vencimiento',
        'fecha_devolucion',
        'renovado',
        'estado',
        'dias_retraso'
    ];
}
