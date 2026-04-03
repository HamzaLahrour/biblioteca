@extends('layouts.admin')

@section('title', 'Editar Categoría')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        
        <div class="mb-3">
            <a href="{{ route('categorias.index') }}" class="text-decoration-none" style="color: var(--text-muted); font-weight: 500;">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0" style="color: var(--secondary-dark); font-weight: 700;">
                        <i class="bi bi-pencil-square me-2" style="color: var(--primary);"></i>Editar Categoría
                    </h4>
                    <p class="text-muted mt-1 mb-0" style="font-size: 0.9rem;">
                        Modifica los datos de esta clasificación.
                    </p>
                </div>
                <span class="badge" style="background-color: var(--secondary-light); color: #fff;">ID: {{ substr($categoria->id, 0, 8) }}...</span>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('categorias.update', $categoria->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="nombre" class="form-label" style="font-weight: 600; color: var(--text-main);">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $categoria->nombre) }}" 
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
                                  style="border-color: #e0e0e0;">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('categorias.index') }}" class="btn btn-light px-4" style="font-weight: 500;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="font-weight: 500;">
                            <i class="bi bi-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection