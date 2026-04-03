<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoEspacioRequest extends FormRequest
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
        // Capturamos el ID del tipo de espacio desde la URL
        $tipoEspacioId = $this->route('tipoEspacio')->id;
        return [
            'nombre' => 'required|string|max:255|unique:tipo_espacios,nombre,' . $tipoEspacioId,
            'descripcion' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del tipo de espacio es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'nombre.unique' => 'Ya existe otro tipo de espacio con ese nombre.',
            
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción es demasiado larga (máximo 1000 caracteres).',
        ];
    }
}
