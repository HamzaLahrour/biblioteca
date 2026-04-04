<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email',
            'password'         => 'required|string|min:8',
            'dni'              => 'required|string|max:20|unique:users,dni',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono'         => 'nullable|string|max:15',

        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'El nombre es obligatorio.',
            'email.required'            => 'El correo electrónico es obligatorio.',
            'email.unique'              => 'Este correo ya está registrado.',
            'password.required'         => 'La contraseña es obligatoria.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'dni.required'              => 'El DNI es obligatorio.',
            'dni.unique'                => 'Este DNI ya pertenece a otro usuario.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'   => 'La fecha de nacimiento no puede ser futura.',

        ];
    }

    public function withValidator($validator)
    {

        $validator->after(function ($validator) {

            // 1. VALIDAR LETRA DEL DNI
            $this->validarDni($validator);
        });
    }



    private function validarDni($validator)
    {

        $dni = $this->input('dni'); // ← Obtenemos el valor del campo

        // Solo validamos si el DNI tiene el formato correcto (ya lo validó rules())
        if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
            return; // Si no cumple el patrón, ya hay error de rules()
        }

        $numeroDni = substr($dni, 0, 8);
        $letraDni = strtoupper(substr($dni, -1));
        $letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $letraCorrecta = $letras[$numeroDni % 23];

        if ($letraDni !== $letraCorrecta) {
            $validator->errors()->add('dni', 'La letra tiene que ser válida en relación al número introducido.');
        }
    }
}
