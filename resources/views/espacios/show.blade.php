@extends('layouts.admin')

@section('title', 'Detalles del Espacio')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        
        <div class="mb-3">
            <a href="{{ route('espacios.index') }}" class="text-decoration-none text-muted fw-medium">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-door-open-fill me-2 text-primary"></i>Ficha del Espacio
                </h4>
                <span class="badge bg-secondary rounded-pill px-3 py-2 shadow-sm">
                    ID: {{ substr($espacio->id, 0, 8) }}...
                </span>
            </div>
            
            <div class="card-body p-4">
                
                <div class="row mb-4 align-items-center">
                    <div class="col-sm-4 text-muted fw-bold">Nombre del espacio:</div>
                    <div class="col-sm-8 fw-bold fs-5 text-dark">
                        {{ $espacio->nombre }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Código identificador:</div>
                    <div class="col-sm-8">
                        <span class="badge bg-light text-dark border fs-6">
                            {{ $espacio->codigo }}
                        </span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Ubicación física:</div>
                    <div class="col-sm-8 text-secondary">
                        <i class="bi bi-geo-alt-fill me-1 text-danger"></i> {{ $espacio->ubicacion }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Clasificación:</div>
                    <div class="col-sm-8">
                        <a href="{{ route('tipos_espacios.show', $espacio->tipo_espacio_id) }}" class="text-decoration-none">
                            <span class="badge bg-info text-dark fs-6 hover-shadow" title="Ver detalles de esta categoría">
                                <i class="bi bi-tag-fill me-1"></i> {{ $espacio->tipoEspacio->nombre }}
                            </span>
                        </a>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Capacidad máxima:</div>
                    <div class="col-sm-8 text-secondary">
                        <i class="bi bi-people-fill me-1 text-primary"></i> {{ $espacio->capacidad }} personas
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Estado actual:</div>
                    <div class="col-sm-8">
                        @if($espacio->disponible)
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-6 rounded-pill px-3">
                                <i class="bi bi-check-circle-fill me-1"></i> Disponible para reservas
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6 rounded-pill px-3">
                                <i class="bi bi-tools me-1"></i> En mantenimiento
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Fecha de registro:</div>
                    <div class="col-sm-8 text-secondary">
                        <i class="bi bi-calendar3 me-1"></i> {{ $espacio->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <hr class="text-muted opacity-25 mt-5">

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="{{ route('espacios.edit', $espacio) }}" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Editar Espacio
                    </a>
                    
                    <form action="{{ route('espacios.destroy', $espacio) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este espacio del sistema?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger px-4">
                            <i class="bi bi-trash-fill me-1"></i> Eliminar
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection