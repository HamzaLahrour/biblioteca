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
                'max:255',
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
                'max:255'
            ],
            'capacidad' => [
                'required',
                'integer',
                'min:1'
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
            'nombre.required'          => 'El nombre del espacio es obligatorio.',
            'nombre.min'               => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.regex'             => 'El nombre debe contener al menos una letra. No puede estar formado solo por números.',
            'nombre.unique'            => 'Ese nombre de espacio ya está en uso. Elige otro para evitar confusiones.',

            'codigo.required'          => 'El código es obligatorio.',
            'codigo.regex'             => 'El código solo puede contener letras, números, guiones y guiones bajos. Sin espacios.',
            'codigo.unique'            => 'Este código ya está en uso. Elige otro.',

            'ubicacion.required'       => 'La ubicación es obligatoria.',

            'capacidad.required'       => 'Debes indicar la capacidad del espacio.',
            'capacidad.min'            => 'La capacidad debe ser al menos de 1 persona.',

            'tipo_espacio_id.required' => 'Debes seleccionar una clasificación para el espacio.',
            'tipo_espacio_id.exists'   => 'La clasificación seleccionada no es válida.',

        ];
    }
}
