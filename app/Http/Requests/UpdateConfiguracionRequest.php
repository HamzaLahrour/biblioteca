<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionRequest extends FormRequest
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
            'configuraciones'         => 'required|array',
            'configuraciones.*'       => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'configuraciones.required' => 'No hay configuraciones que guardar.',
            'configuraciones.array'    => 'El formato de las configuraciones no es válido.',
        ];
    }
}
