<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTipoEspacioRequest extends FormRequest
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
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/',
                'unique:tipo_espacios',
            ],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'     => 'El nombre del Tipo de Espacio es obligatorio.',
            'nombre.string'       => 'El nombre debe ser texto.',
            'nombre.min'          => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.max'          => 'El nombre no puede superar los 100 caracteres.',
            'nombre.regex'        => 'El nombre debe contener al menos una letra, no puede ser solo números.',
            'nombre.unique'       => 'Ya existe un Tipo de Espacio con este nombre.',
        ];
    }
}
