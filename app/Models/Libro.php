<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Libro extends Model
{
    use HasUuids;
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }


    public function getDisponiblesAttribute()
    {
        $prestados = $this->prestamos()->where('estado', 'activo')->count();
        return $this->copias_totales - $prestados;
    }

    protected $fillable = [
        'categoria_id',
        'titulo',
        'autor',
        'isbn',
        'editorial',
        'anio_publicacion',
        'copias_totales',
        'portada',
        'descripcion',
    ];
}
