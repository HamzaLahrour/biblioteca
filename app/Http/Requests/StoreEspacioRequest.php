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



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
         'nombre'          => 'required|string|max:255',
            'codigo'          => 'required|string|max:255|unique:espacios,codigo',
            'ubicacion'       => 'required|string|max:255',
            'capacidad'       => 'required|integer|min:1',
            'tipo_espacio_id' => 'required|exists:tipo_espacios,id',
            'disponible'      => 'boolean',
        ];
    }


    public function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre del espacio es obligatorio.',
            'codigo.required'          => 'El código es obligatorio.',
            'codigo.unique'            => 'Este código ya está en uso. Elige otro.',
            'ubicacion.required'       => 'La ubicación es obligatoria.',
            'capacidad.required'       => 'Debes indicar la capacidad del espacio.',
            'capacidad.min'            => 'La capacidad debe ser al menos de 1 persona.',
            'tipo_espacio_id.required' => 'Debes seleccionar una clasificación para el espacio.',
            'tipo_espacio_id.exists'   => 'La clasificación seleccionada no es válida.',
        ];
    }
}
