@extends('layouts.admin')

@section('title', 'Editar Espacio')

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
                    <i class="bi bi-tag me-2" style="color: var(--primary);"></i>Editar Espacio
                </h4>
                <p class="text-muted mt-1 mb-0" style="font-size: 0.9rem;">
                    Modifica un espacio de trabajo, sala o puesto individual.
                </p>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('espacios.update',  $espacio) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label for="nombre" class="form-label" style="font-weight: 600; color: var(--text-main);">Nombre del espacio <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $espacio->nombre) }}" 
                               placeholder="Ej: Mesa 4, Sala de Reuniones B, Puesto Informático 12..."
                               style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('nombre')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="codigo" class="form-label" style="font-weight: 600; color: var(--text-main);">Codigo del espacio <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="codigo" 
                               id="codigo" 
                               class="form-control form-control-lg @error('codigo') is-invalid @enderror" 
                               value="{{ old('codigo',$espacio->codigo) }}" 
                               placeholder="Ej: TP-01"
                               style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('codigo')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="ubicacion" class="form-label" style="font-weight: 600; color: var(--text-main);">Ubicación<span class="text-danger">*</span></label>
                        <input type="text" 
                               name="ubicacion" 
                               id="ubicacion" 
                               class="form-control form-control-lg @error('ubicacion') is-invalid @enderror" 
                               value="{{ old('ubicacion',$espacio->ubicacion) }}" 
                               placeholder="Introduce la ubicación del espacio (Ej: Sala de estudio 2)"
                               style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('ubicacion')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="capacidad" class="form-label" style="font-weight: 600; color: var(--text-main);">Capacidad<span class="text-danger">*</span></label>
                        <input type="number" 
                               name="capacidad" 
                               id="capacidad" 
                               class="form-control form-control-lg @error('capacidad') is-invalid @enderror" 
                               value="{{ old('capacidad',$espacio->capacidad) }}" 
                               placeholder="Introduce la capacidad (Ej: 15 personas)"
                               style="font-size: 1rem; border-color: #e0e0e0;">
                        @error('capacidad')
                            <div class="invalid-feedback fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-form.select 
                        name="tipo_espacio_id" 
                        label="Clasificación del Espacio" 
                        :options="$tipos" 
                        :selected="$espacio->tipo_espacio_id"
                    />
                    
                    

                    <div class="form-check form-switch mb-4">
                        <input type="hidden" name="disponible" value="0">
                        <input class="form-check-input @error('disponible') is-invalid @enderror" 
                            type="checkbox" 
                            role="switch" 
                            name="disponible" 
                            id="disponible" 
                            value="1" 
                            {{ old('disponible', true) ? 'checked' : '' }} 
                            style="transform: scale(1.2); margin-left: -2em;">
                            <label class="form-check-label ms-2" for="disponible" style="font-weight: 600; color: var(--text-main);">
                            Disponible para reservas
                            </label>
                            <div class="form-text text-muted" style="font-size: 0.85rem;">Si lo desmarcas, este espacio estará en mantenimiento.</div>
                    </div>



                





                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('espacios.index') }}" class="btn btn-light px-4" style="font-weight: 500;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="font-weight: 500;">
                            <i class="bi bi-save me-1"></i> Editar Espacio
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection