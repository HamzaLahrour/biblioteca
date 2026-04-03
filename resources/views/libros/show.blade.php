@extends('layouts.admin')

@section('title', 'Detalles del Libro')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

        <div class="mb-3">
            <a href="{{ route('libros.index') }}" class="text-decoration-none text-muted fw-medium">
                <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-book-half me-2 text-primary"></i>Ficha Literaria
                </h4>
                <span class="badge bg-secondary rounded-pill px-3 py-2 shadow-sm">
                    ID: {{ substr($libro->id, 0, 8) }}...
                </span>
            </div>

            <div class="card-body p-4">

                <div class="row">
                    <div class="col-md-4 text-center mb-4 mb-md-0">
                        @if($libro->portada)
                        <img src="{{ $libro->portada }}" alt="Portada de {{ $libro->titulo }}" class="img-fluid rounded-3 shadow" style="max-height: 350px; object-fit: cover;">
                        @else
                        <div class="bg-light d-flex flex-column justify-content-center align-items-center border rounded-3 shadow-sm mx-auto" style="height: 300px; width: 100%; max-width: 220px;">
                            <i class="bi bi-book text-muted opacity-50" style="font-size: 5rem;"></i>
                            <span class="text-muted mt-3 fw-medium">Sin portada</span>
                        </div>
                        @endif

                        <div class="mt-4">
                            <h6 class="fw-bold text-muted mb-2">Inventario</h6>
                            <span class="badge rounded-pill fs-6 px-3 py-2 {{ $libro->copias_totales > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                                <i class="bi bi-stack me-1"></i> {{ $libro->copias_totales }} Copias Totales
                            </span>
                        </div>
                    </div>

                    <div class="col-md-8">

                        <h2 class="fw-bold text-dark mb-1">{{ $libro->titulo }}</h2>
                        <h5 class="text-muted mb-4"><i class="bi bi-pen-fill me-2 fs-6"></i>{{ $libro->autor }}</h5>

                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-bold">Categoría:</div>
                            <div class="col-sm-8">
                                <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle fs-6">
                                    <i class="bi bi-bookmark-fill me-1"></i> {{ $libro->categoria->nombre }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-bold">ISBN:</div>
                            <div class="col-sm-8">
                                @if($libro->isbn)
                                <span class="font-monospace text-dark bg-light px-2 py-1 rounded border">{{ $libro->isbn }}</span>
                                @else
                                <span class="text-muted fst-italic">No registrado</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-bold">Editorial:</div>
                            <div class="col-sm-8 text-secondary">
                                {{ $libro->editorial ?: 'Desconocida' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted fw-bold">Año publicación:</div>
                            <div class="col-sm-8 text-secondary">
                                {{ $libro->anio_publicacion ?: 'Desconocido' }}
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 my-4">

                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2">Sinopsis / Descripción:</h6>
                            <p class="text-secondary" style="line-height: 1.6; text-align: justify;">
                                @if($libro->descripcion)
                                {{ $libro->descripcion }}
                                @else
                                <em class="text-muted">No hay ninguna descripción disponible para este ejemplar.</em>
                                @endif
                            </p>
                        </div>

                        <div class="text-muted small">
                            <i class="bi bi-clock-history me-1"></i> Añadido al catálogo el {{ $libro->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                <hr class="text-muted opacity-25 mt-4">

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="{{ route('libros.edit', $libro) }}" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Editar Libro
                    </a>

                    <form action="{{ route('libros.destroy', $libro) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro definitivamente del catálogo?');">
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