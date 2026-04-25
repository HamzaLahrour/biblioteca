<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $libro = $this->route('libro')->id;

        return [
            'titulo'           => 'required|string|max:255',
            'autor'            => 'required|string|max:255',
            // El ISBN es opcional, pero si lo ponen, debe ser único en la tabla libros
            'isbn'             => 'nullable|string|max:255|unique:libros,isbn,' . $libro,
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
}
