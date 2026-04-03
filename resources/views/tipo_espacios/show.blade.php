@extends('layouts.admin')

@section('title', 'Detalles del Tipo de Espacio')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        
        <div class="mb-3">
            <a href="{{ route('tipos_espacios.index') }}" class="text-decoration-none text-muted fw-medium">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-info-circle-fill me-2 text-primary"></i>Detalles de Clasificación
                </h4>
                <span class="badge bg-secondary rounded-pill px-3 py-2 shadow-sm">
                    ID: {{ substr($tipoEspacio->id, 0, 8) }}...
                </span>
            </div>
            
            <div class="card-body p-4">
                
                <div class="row mb-4 align-items-center">
                    <div class="col-sm-4 text-muted fw-bold">Nombre:</div>
                    <div class="col-sm-8 fw-bold fs-5 text-dark">{{ $tipoEspacio->nombre }}</div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Descripción:</div>
                    <div class="col-sm-8 text-secondary">
                        {{ $tipoEspacio->descripcion ?: 'Sin descripción asignada.' }}
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Fecha de registro:</div>
                    <div class="col-sm-8 text-secondary">
                        <i class="bi bi-calendar3 me-1"></i> {{ $tipoEspacio->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                @php
                    $espaciosAsociados = $tipoEspacio->espacios;
                    $cantidad = $espaciosAsociados->count();
                @endphp

                <div class="mt-5 mb-4">
                    <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">
                        <i class="bi bi-geo-alt-fill me-2"></i>Espacios vinculados ({{ $cantidad }})
                    </h6>

                    @if($cantidad > 0)
                        <div class="list-group list-group-flush border rounded-3">
                            @foreach($espaciosAsociados as $espacio)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $espacio->nombre }}</div>
                                        <div class="small text-muted">
                                            Código: {{ $espacio->codigo }} | {{ $espacio->ubicacion }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($espacio->disponible)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Disponible</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Mantenimiento</span>
                                        @endif
                                        
                                        <a href="{{ route('espacios.show', $espacio->id) }}" class="btn btn-sm btn-outline-info rounded-circle" title="Ver espacio">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border text-center text-muted" role="alert">
                            No hay ningún espacio físico asignado a esta categoría todavía.
                        </div>
                    @endif
                </div>

                <hr class="text-muted opacity-25">

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="{{ route('tipos_espacios.edit', $tipoEspacio) }}" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Editar
                    </a>
                    
                    <form action="{{ route('tipos_espacios.destroy', $tipoEspacio) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este tipo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger px-4" {{ $cantidad > 0 ? 'disabled title="Desvincula los espacios primero"' : '' }}>
                            <i class="bi bi-trash-fill me-1"></i> Eliminar
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection