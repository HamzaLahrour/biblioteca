@extends('layouts.admin')

@section('title', 'Añadir Nueva Categoría')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="mb-3">
            <a href="{{ route('categorias.index') }}" class="text-decoration-none" style="color: var(--text-muted); font-weight: 500;">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h4 class="mb-0" style="color: var(--secondary-dark); font-weight: 700;">
                    <i class="bi bi-tag me-2" style="color: var(--primary);"></i>Crear Categoría
                </h4>
                <p class="text-muted mt-1 mb-0" style="font-size: 0.9rem;">
                    Añade una nueva clasificación para organizar el catálogo de libros.
                </p>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('categorias.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="nombre" class="form-label" style="font-weight: 600; color: var(--text-main);">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre') }}" 
                               placeholder="Ej: Novela Histórica, Ciencia Ficción..."
                               style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('nombre')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="descripcion" class="form-label" style="font-weight: 600; color: var(--text-main);">Descripción (Opcional)</label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="4" 
                                  class="form-control @error('descripcion') is-invalid @enderror" 
                                  placeholder="Explica brevemente qué tipo de libros incluye esta categoría..."
                                  style="border-color: #e0e0e0;">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('categorias.index') }}" class="btn btn-light px-4" style="font-weight: 500;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="font-weight: 500;">
                            <i class="bi bi-save me-1"></i> Guardar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection