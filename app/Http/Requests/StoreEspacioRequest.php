<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEspacioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('codigo')) {
            $this->merge([
                'codigo' => strtoupper(trim($this->codigo)),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'min:2',
                'max:90',
                'regex:/^(?=.*[a-zA-ZñÑáéíóúÁÉÍÓÚ])[a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ]+$/',
                'unique:espacios'
            ],
            'codigo' => [
                'required',
                'string',
                'min:2',
                'max:20',
                'regex:/^[A-Z0-9\-_]+$/',
                'unique:espacios'
            ],
            'ubicacion' => [
                'required',
                'string',
                'max:100'
            ],
            'capacidad' => [
                'required',
                'integer',
                'min:1',
                'max:150'
            ],
            'tipo_espacio_id' => [
                'required',
                'exists:tipo_espacios,id'
            ],
            'disponible' => [
                'sometimes',
                'boolean'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Nombre
            'nombre.required'          => 'El nombre del espacio es obligatorio.',
            'nombre.string'            => 'El nombre debe ser un texto válido.',
            'nombre.min'               => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max'               => 'El nombre no puede superar los 255 caracteres.',
            'nombre.regex'             => 'El nombre debe contener al menos una letra. No puede estar formado solo por números.',
            'nombre.unique'            => 'Ese nombre de espacio ya está en uso. Elige otro para evitar confusiones.',

            // Código
            'codigo.required'          => 'El código es obligatorio.',
            'codigo.string'            => 'El código debe ser una cadena de texto.',
            'codigo.min'               => 'El código debe tener al menos 2 caracteres.',
            'codigo.max'               => 'El código no puede superar los 20 caracteres.',
            'codigo.regex'             => 'El código solo puede contener letras, números, guiones y guiones bajos. Sin espacios.',
            'codigo.unique'            => 'Este código ya está en uso. Elige otro.',

            // Ubicación
            'ubicacion.required'       => 'La ubicación es obligatoria.',
            'ubicacion.string'         => 'La ubicación debe ser un texto válido.',
            'ubicacion.max'            => 'La ubicación no puede superar los 255 caracteres.',

            // Capacidad
            'capacidad.required'       => 'Debes indicar la capacidad del espacio.',
            'capacidad.integer'        => 'La capacidad debe ser un número entero (sin decimales ni letras).',
            'capacidad.min'            => 'La capacidad debe ser al menos de 1 persona.',
            'capacidad.max'            => 'La capacidad máxima es de 150 personas.',

            // Tipo Espacio
            'tipo_espacio_id.required' => 'Debes seleccionar una clasificación para el espacio.',
            'tipo_espacio_id.exists'   => 'La clasificación seleccionada no es válida.',

            // Disponible
            'disponible.boolean'       => 'El valor de disponibilidad no es válido.',
        ];
    }
}
