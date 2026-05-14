<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLibroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepara los datos antes de validarlos (limpia los ceros a la izquierda en números).
     */
    protected function prepareForValidation()
    {
        $mergeData = [];

        // Convertimos "01" a 1 para evitar que la regla 'integer' de Laravel falle
        if ($this->has('copias_totales') && is_numeric($this->copias_totales)) {
            $mergeData['copias_totales'] = (int) $this->copias_totales;
        }

        if ($this->has('anio_publicacion') && is_numeric($this->anio_publicacion)) {
            $mergeData['anio_publicacion'] = (int) $this->anio_publicacion;
        }

        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Pillamos el modelo del libro que estamos editando
        $libro = $this->route('libro');

        return [
            'titulo'           => 'required|string|max:255',
            'autor'            => 'required|string|max:255',
            // Usamos la clase Rule para ignorar el ID actual
            'isbn'             => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('libros', 'isbn')->ignore($libro)
            ],
            'editorial'        => 'nullable|string|max:255',
            'anio_publicacion' => 'nullable|integer|min:1000|max:' . date('Y'),
            'copias_totales'   => 'required|integer|min:1',


            'portada'          => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048'
            ],



            'descripcion'      => 'nullable|string',
            'categoria_id'     => 'required|exists:categorias,id',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'         => 'El título del libro es obligatorio.',
            'autor.required'          => 'El autor es obligatorio.',

            'isbn.unique'             => 'Este ISBN ya está registrado en otro libro de la biblioteca.',

            'anio_publicacion.integer' => 'El año de publicación debe ser un número entero.',
            'anio_publicacion.min'    => 'El año de publicación no parece válido.',
            'anio_publicacion.max'    => 'El año no puede ser en el futuro.',

            'copias_totales.required' => 'Debes indicar cuántas copias físicas tienes.',
            'copias_totales.integer'  => 'La cantidad de copias debe ser un número entero.',
            'copias_totales.min'      => 'Debes tener al menos 1 copia.',

            'categoria_id.required'   => 'Debes seleccionar una categoría.',
            'categoria_id.exists'     => 'La categoría seleccionada no es válida.',


            'portada.image'            => 'El archivo debe ser una imagen.',
            'portada.mimes'            => 'Formatos permitidos: jpeg, png, jpg, webp.',
            'portada.max'              => 'La imagen no puede pesar más de 2MB.',
        ];
    }

    /**
     * Añadimos la validación matemática del ISBN que también tienes en el Store
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isbn = $this->input('isbn');

            // Si está vacío, no hacemos nada (es nullable)
            if (empty($isbn)) {
                return;
            }

            // 1. Limpiamos espacios y guiones
            $cleanIsbn = str_replace(['-', ' '], '', strtoupper($isbn));
            $length = strlen($cleanIsbn);

            // 2. Evaluamos si es de 10 o 13 caracteres
            if ($length === 10) {
                if (!$this->isValidIsbn10($cleanIsbn)) {
                    $validator->errors()->add('isbn', 'El ISBN-10 ingresado no es matemáticamente válido.');
                }
            } elseif ($length === 13) {
                if (!$this->isValidIsbn13($cleanIsbn)) {
                    $validator->errors()->add('isbn', 'El ISBN-13 ingresado no es matemáticamente válido.');
                }
            } else {
                $validator->errors()->add('isbn', 'El ISBN debe tener exactamente 10 o 13 caracteres válidos (sin contar guiones).');
            }
        });
    }

    private function isValidIsbn10(string $isbn): bool
    {
        if (!preg_match('/^\d{9}[\dX]$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$isbn[$i] * (10 - $i);
        }

        $last = $isbn[9];
        $sum += ($last === 'X') ? 10 : (int)$last;

        return $sum % 11 === 0;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (!preg_match('/^\d{13}$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $multiplier = ($i % 2 === 0) ? 1 : 3;
            $sum += (int)$isbn[$i] * $multiplier;
        }

        return $sum % 10 === 0;
    }
}
