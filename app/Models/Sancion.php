<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sancion extends Model
{

    use HasUuids;


    protected $table = 'sanciones';


    protected $fillable = [
        'user_id',
        'razon',
        'fecha_inicio',
        'fecha_fin',
        'fecha_levantamiento_manual',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'fecha_levantamiento_manual' => 'date',
        ];
    }

    // Una sanción pertenece a un usuario
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class);
    }
}
