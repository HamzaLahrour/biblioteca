<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLibroRequest extends FormRequest
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
            'titulo'           => 'required|string|max:255',
            'autor'            => 'required|string|max:255',
            // El ISBN es opcional, pero si lo ponen, debe ser único en la tabla libros
            'isbn'             => 'nullable|string|max:255|unique:libros,isbn',
            'editorial'        => 'nullable|string|max:255',
            // Validamos que el año sea lógico (hasta el año actual)
            'anio_publicacion' => 'nullable|integer|min:1000|max:' . date('Y'),
            'copias_totales'   => 'required|integer|min:1',
            'portada'          => 'nullable|string', // Aquí guardaremos la URL de la imagen de Google
            'descripcion'      => 'nullable|string',
            // Validamos que la categoría exista realmente en la BD
            'categoria_id'     => 'required|exists:categorias,id',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'         => 'El título del libro es obligatorio.',
            'autor.required'          => 'El autor es obligatorio.',
            'isbn.unique'             => 'Este ISBN ya está registrado en la biblioteca.',
            'anio_publicacion.min'    => 'El año de publicación no parece válido.',
            'anio_publicacion.max'    => 'El año no puede ser en el futuro.',
            'copias_totales.required' => 'Debes indicar cuántas copias físicas tienes.',
            'copias_totales.min'      => 'Debes tener al menos 1 copia.',
            'categoria_id.required'   => 'Debes seleccionar una categoría.',
            'categoria_id.exists'     => 'La categoría seleccionada no es válida.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isbn = $this->input('isbn');

            // Si está vacío, no hacemos nada (ya que en tus rules es nullable)
            if (empty($isbn)) {
                return;
            }

            // 1. Limpiamos espacios y guiones para quedarnos solo con los caracteres
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
        // Validamos que sean 9 números seguidos de un número o una 'X'
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
        // Validamos que sean exactamente 13 números
        if (!preg_match('/^\d{13}$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            // Alterna multiplicando por 1 y por 3
            $multiplier = ($i % 2 === 0) ? 1 : 3;
            $sum += (int)$isbn[$i] * $multiplier;
        }

        return $sum % 10 === 0;
    }
}
