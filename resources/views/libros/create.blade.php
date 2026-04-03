@extends('layouts.admin')

@section('title', 'Añadir Nuevo Libro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        <div class="mb-3">
            <a href="{{ route('espacios.index') }}" class="text-decoration-none" style="color: var(--text-muted); font-weight: 500;">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h4 class="mb-0" style="color: var(--secondary-dark); font-weight: 700;">
                    <i class="bi bi-tag me-2" style="color: var(--primary);"></i>Crear Libro
                </h4>
                <p class="text-muted mt-1 mb-0" style="font-size: 0.9rem;">
                    Añade un libro al catálogo de la biblioteca para facilitar su gestión, préstamo y consulta.
                </p>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('libros.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="titulo" class="form-label" style="font-weight: 600; color: var(--text-main);">Nombre del libro <span class="text-danger">*</span></label>
                        <input type="text"
                            name="titulo"
                            id="titulo"
                            class="form-control form-control-lg @error('titulo') is-invalid @enderror"
                            value="{{ old('titulo') }}"
                            placeholder="Ej: Don Quijote de la Mancha, Cien años de soledad, El Principito..."
                            style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('titulo')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-4">
                        <label for="autor" class="form-label" style="font-weight: 600; color: var(--text-main);">Autor del libro <span class="text-danger">*</span></label>
                        <input type="text"
                            name="autor"
                            id="autor"
                            class="form-control form-control-lg @error('autor') is-invalid @enderror"
                            value="{{ old('autor') }}"
                            placeholder="Ej: Gabriel García Márquez"
                            style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('autor')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="isbn" class="form-label" style="font-weight: 600; color: var(--text-main);">
                            ISBN <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            name="isbn"
                            id="isbn"
                            class="form-control form-control-lg @error('isbn') is-invalid @enderror"
                            value="{{ old('isbn') }}"
                            placeholder="Ej: 978-84-376-0494-7"
                            style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('isbn')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="editorial" class="form-label" style="font-weight: 600; color: var(--text-main);">
                            Editorial <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            name="editorial"
                            id="editorial"
                            class="form-control form-control-lg @error('editorial') is-invalid @enderror"
                            value="{{ old('editorial') }}"
                            placeholder="Ej: Planeta, Penguin Random House..."
                            style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('editorial')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-form.select
                        name="categoria_id"
                        label="Categoría del libro"
                        :options="$categorias" />

                    <div class="mb-4">
                        <label for="anio_publicacion" class="form-label" style="font-weight: 600; color: var(--text-main);">
                            Año de publicación <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            name="anio_publicacion"
                            id="anio_publicacion"
                            class="form-control form-control-lg @error('anio_publicacion') is-invalid @enderror"
                            value="{{ old('anio_publicacion') }}"
                            placeholder="Ej: 1998"
                            style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('anio_publicacion')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="copias_totales" class="form-label" style="font-weight: 600; color: var(--text-main);">
                            Copias totales <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            name="copias_totales"
                            id="copias_totales"
                            class="form-control form-control-lg @error('copias_totales') is-invalid @enderror"
                            value="{{ old('copias_totales') }}"
                            placeholder="Ej: 10"
                            style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('copias_totales')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="portada" class="form-label" style="font-weight: 600; color: var(--text-main);">
                            URL de la portada
                        </label>
                        <input type="url"
                            name="portada"
                            id="portada"
                            class="form-control form-control-lg @error('portada') is-invalid @enderror"
                            value="{{ old('portada') }}"
                            placeholder="Ej: https://ejemplo.com/portada.jpg"
                            style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('portada')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label" style="font-weight: 600; color: var(--text-main);">
                            Descripción
                        </label>
                        <textarea
                            name="descripcion"
                            id="descripcion"
                            rows="4"
                            class="form-control form-control-lg @error('descripcion') is-invalid @enderror"
                            placeholder="Breve resumen del libro..."
                            style="font-size: 1rem; border-color: #e0e0e0;">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                        <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('espacios.index') }}" class="btn btn-light px-4" style="font-weight: 500;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="font-weight: 500;">
                            <i class="bi bi-save me-1"></i> Guardar Espacio
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection