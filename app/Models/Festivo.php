<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Importas el trait nativo

use Illuminate\Database\Eloquent\Model;

class Festivo extends Model
{
    use HasUuids;
    protected $fillable = [
        'fecha',
        'motivo'
    ];
    //
}
