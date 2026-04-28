<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;





class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'fecha_nacimiento',
        'telefono',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
        ];
    }

    protected function edad(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null,
        );
    }

    // Un usuario puede tener muchas sanciones
    public function sanciones(): HasMany
    {
        return $this->hasMany(Sancion::class);
    }

    // Un usuario puede tener muchas reservas de espacios
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    // Un usuario puede tener muchos préstamos de libros
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }
}
