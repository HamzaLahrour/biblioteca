<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Configuracion;
use App\Models\User;
use App\Models\Reserva;
use App\Models\Espacio;
use Illuminate\Support\Carbon;
use Monolog\Handler\IFTTTHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StoreReservaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Reglas comunes para ambos pasos (Comprobar y Guardar)
        $reglas = [
            'fecha'       => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'user_id'     => ['nullable', 'uuid', 'exists:users,id'],
        ];

        // Si estamos en el paso final de GUARDAR, entonces sí exigimos el espacio_id
        if ($this->routeIs('reservas_usuario.store') || $this->routeIs('reservas.store')) {
            $reglas['espacio_id'] = ['required', 'uuid', 'exists:espacios,id'];
        }

        return $reglas;
    }

    public function messages()
    {
        return [
            'espacio_id.exists'     => 'El espacio seleccionado no es válido.',
            'fecha.after_or_equal'  => 'No puedes realizar reservas en fechas pasadas.',
            'hora_inicio.date_format' => 'El formato de la hora de inicio debe ser HH:MM.',
            'hora_fin.after'        => 'La hora de fin debe ser posterior a la de inicio. ¡Nada de viajes en el tiempo!',
            'user_id.exists'        => 'El usuario indicado no existe en el sistema.',
        ];
    }
}
