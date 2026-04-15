<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrestamoRequest extends FormRequest
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
            'user_id' => [
                'nullable',
                'exists:users,id'
            ],

            // El libro_id es obligatorio sí o sí y debe existir
            'libro_id' => [
                'required',
                'exists:libros,id'
            ],

            // La fecha de devolución es obligatoria, debe ser una fecha válida,
            // y como mínimo tiene que ser mañana (no puedes devolverlo el mismo día).
            'fecha_devolucion_prevista' => [
                'required',
                'date',
                'after:today'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'El usuario seleccionado no existe en nuestra base de datos.',

            'libro_id.required' => 'Debes seleccionar el libro que se va a prestar.',
            'libro_id.exists' => 'El libro seleccionado no es válido o ha sido eliminado.',

            'fecha_devolucion_prevista.required' => 'Debes indicar una fecha de devolución.',
            'fecha_devolucion_prevista.date' => 'El formato de la fecha no es válido.',
            'fecha_devolucion_prevista.after' => 'La fecha de devolución debe ser al menos el día de mañana.',
        ];
    }
}
