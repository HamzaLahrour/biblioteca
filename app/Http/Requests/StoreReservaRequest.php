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
        return [
            // Obligatorio, tiene que ser UUID, y la sala DEBE existir en la BD
            'espacio_id'  => ['required', 'uuid', 'exists:espacios,id'],

            // Obligatorio, debe ser fecha válida, y no puede ser en el pasado
            'fecha'       => ['required', 'date', 'after_or_equal:today'],

            // Obligatorio y con formato estricto de hora (ej: 09:30)
            'hora_inicio' => ['required', 'date_format:H:i'],

            // Obligatorio, formato de hora, y estrictamente POSTERIOR a la hora de inicio
            'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],

            // Opcional (solo lo mandará el admin), pero si lo manda, debe ser UUID y existir
            'user_id'     => ['nullable', 'uuid', 'exists:users,id'],
        ];
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
