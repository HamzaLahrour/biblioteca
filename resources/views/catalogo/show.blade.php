@extends('layouts.app')

@section('title', 'Detalles del Libro | LibreLah')

@section('content')
<div class="container py-4">

    {{-- BOTÓN DE VOLVER --}}
    <div class="mb-4">
        <a href="{{ route('catalogo.index') }}" class="text-decoration-none text-muted d-inline-flex align-items-center fw-bold transition-all" style="font-size: 0.9rem;">
            <i class="bi bi-arrow-left me-2"></i> Volver al Catálogo
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="row g-0">
            {{-- COLUMNA DE LA PORTADA --}}
            <div class="col-md-4 col-lg-3 bg-light d-flex align-items-center justify-content-center p-4">
                @if($libro->portada)
                <img src="{{ $libro->portada }}" alt="{{ $libro->titulo }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: cover;">
                @else
                <div class="text-center text-muted">
                    <i class="bi bi-book fs-1 opacity-50 mb-2 d-block"></i>
                    <span class="small fw-medium">Sin portada</span>
                </div>
                @endif
            </div>

            {{-- COLUMNA DE DATOS --}}
            <div class="col-md-8 col-lg-9 p-4 p-md-5 d-flex flex-column">

                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill shadow-sm mb-2">
                        {{ $libro->categoria->nombre ?? 'Sin Categoría' }}
                    </span>

                    {{-- ESTADO DE DISPONIBILIDAD --}}
                    @if($libro->ejemplares_disponibles > 0)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Disponible ({{ $libro->ejemplares_disponibles }})
                    </span>
                    @else
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-x-circle me-1"></i> Agotado
                    </span>
                    @endif
                </div>

                <h1 class="fw-bold mb-1" style="color: var(--secondary-dark); letter-spacing: -0.5px;">{{ $libro->titulo }}</h1>
                <h5 class="text-muted mb-4"><i class="bi bi-pen me-2 opacity-50"></i>{{ $libro->autor }}</h5>

                <hr class="opacity-10 mb-4">

                <div class="mb-4 flex-grow-1">
                    <h6 class="fw-bold mb-3">Sinopsis / Descripción</h6>
                    <p class="text-muted lh-lg" style="font-size: 0.95rem;">
                        {{ $libro->descripcion ?? 'No hay descripción disponible para este libro.' }}
                    </p>
                </div>

                <div class="row g-3 bg-light rounded-4 p-3 mb-4 text-center text-md-start">
                    <div class="col-12 col-md-4 border-end-md">
                        <span class="d-block text-muted small fw-bold text-uppercase">ISBN</span>
                        <span class="fw-medium">{{ $libro->isbn ?? 'N/A' }}</span>
                    </div>
                    <div class="col-12 col-md-4 border-end-md">
                        <span class="d-block text-muted small fw-bold text-uppercase">Editorial</span>
                        <span class="fw-medium">{{ $libro->editorial ?? 'N/A' }}</span>
                    </div>
                    <div class="col-12 col-md-4">
                        <span class="d-block text-muted small fw-bold text-uppercase">Año de publicación</span>
                        <span class="fw-medium">{{ $libro->anio_publicacion ?? 'N/A' }}</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection