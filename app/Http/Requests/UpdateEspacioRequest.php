<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEspacioRequest extends FormRequest
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
        // Pillamos el ID del espacio que estamos editando desde la ruta
        $espacioId = $this->route('espacio')->id;

        return [
            'nombre'          => 'required|string|max:255',
            // Regla de oro: Único en la tabla espacios, pero ignora mi propio ID
            'codigo'          => 'required|string|max:255|unique:espacios,codigo,' . $espacioId,
            'ubicacion'       => 'required|string|max:255',
            'capacidad'       => 'required|integer|min:1',
            'tipo_espacio_id' => 'required|exists:tipo_espacios,id',
            'disponible'      => 'boolean',
        ];
    }
    

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'codigo.unique'   => 'Este código ya lo tiene otro espacio.',
            'tipo_espacio_id.required' => 'Debes seleccionar un tipo.',
        ];
    }
}
