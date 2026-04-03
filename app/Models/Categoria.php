<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Categoria extends Model
{
    use HasUuids; 

    protected $table = 'categorias';
    public $incrementing = false;
    protected $keyType = 'string';
 

   

    protected $fillable = [
    'nombre', 'descripcion'
    ];
    public function libros(){
        return $this->hasMany(Libro::class);
    }
}
