<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;



class Espacio extends Model
{

    // Permite utilizar UUIDs de manera automatica
    use HasUuids; 

   //Tabla de base de datos que vamos a usar
   protected $table = 'espacios';

   //Marcamos como falso por que lleva uuid
   public $incrementing = false; 
   //String por que es una cadena el uuiid
   protected $keyType = 'string';


    public function reservas(){
        return $this->hasMany(Reserva::class); 
    }

    public function tipoEspacio(): BelongsTo
    {
        // Por convención de Laravel, buscará automáticamente 'tipo_espacio_id'
        return $this->belongsTo(TipoEspacio::class);
    }

    protected $fillable = [
        'nombre',
        'codigo',
        'ubicacion',
        'capacidad',
        'disponible',
        'tipo_espacio_id', 
    ];

    protected $casts = [
        'disponible' => 'boolean',
    ];
    
}
