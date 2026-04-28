<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{

    use HasUuids;
    protected $fillable = ['user_id', 'libro_id', 'estrellas', 'contenido'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function libro()
    {
        return $this->belongsTo(Libro::class);
    }
}
