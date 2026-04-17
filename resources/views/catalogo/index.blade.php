@extends('layouts.app')

@section('title', 'Catálogo de la Biblioteca')

@section('content')
<div class="container py-4 mb-5">

    {{-- CABECERA / HERO BANNERS --}}
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold text-dark display-5 mb-3">Descubre tu próxima lectura</h1>
            <p class="text-muted lead mx-auto" style="max-width: 600px;">Explora nuestro catálogo, encuentra los recursos que necesitas para tus estudios o simplemente sumérgete en una buena historia.</p>
        </div>
    </div>

    {{-- BARRA DE BÚSQUEDA GIGANTE --}}
    <div class="row justify-content-center mb-5">
        <div class="col-md-8 col-lg-6">
            <form action="{{ route('catalogo.index') }}" method="GET" class="position-relative shadow-sm rounded-pill">
                <input type="text" name="buscar" class="form-control form-control-lg rounded-pill ps-4 pe-5 border-0 bg-white" placeholder="Buscar por título, autor..." value="{{ request('buscar') }}" style="height: 60px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <button type="submit" class="btn btn-primary rounded-circle position-absolute top-50 translate-middle-y" style="right: 8px; width: 44px; height: 44px;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- FILTRO POR CATEGORÍAS (Píldoras scrolleables) --}}
    <div class="d-flex flex-nowrap overflow-auto pb-3 mb-4 gap-2 css-scrollbar-hide">
        <a href="{{ route('catalogo.index') }}" class="btn rounded-pill px-4 {{ !request('categoria') ? 'btn-dark' : 'btn-outline-secondary bg-white' }}">
            Todos
        </a>
        @foreach($categorias as $categoria)
        <a href="{{ route('catalogo.index', ['categoria' => $categoria->id, 'buscar' => request('buscar')]) }}"
            class="btn rounded-pill px-4 text-nowrap {{ request('categoria') == $categoria->id ? 'btn-dark' : 'btn-outline-secondary bg-white' }}">
            {{ $categoria->nombre }}
        </a>
        @endforeach
    </div>

    {{-- CUADRÍCULA DE LIBROS (GRID) --}}
    @if($libros->count() > 0)
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($libros as $libro)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-decoration-none book-card transition-all">

                {{-- Contenedor de la Portada con Aspect Ratio fijo --}}
                <div class="position-relative bg-light" style="padding-top: 140%;">
                    @if($libro->portada)
                    <img src="{{ $libro->portada }}" alt="{{ $libro->titulo }}" class="position-absolute top-0 w-100 h-100" style="object-fit: cover;">
                    @else
                    <div class="position-absolute top-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-muted">
                        <i class="bi bi-book fs-1 opacity-50"></i>
                    </div>
                    @endif

                    {{-- Badge de Disponibilidad sobre la foto --}}
                    <div class="position-absolute top-0 end-0 p-2">
                        @if($libro->copias_totales > 0) {{-- Aquí deberías usar tu lógica de copias_disponibles --}}
                        <span class="badge bg-success bg-opacity-90 shadow-sm rounded-pill backdrop-blur"><i class="bi bi-check-circle me-1"></i>Disponible</span>
                        @else
                        <span class="badge bg-danger bg-opacity-90 shadow-sm rounded-pill backdrop-blur"><i class="bi bi-clock me-1"></i>Agotado</span>
                        @endif
                    </div>
                </div>

                <div class="card-body d-flex flex-column p-3">
                    <span class="text-uppercase text-primary fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">
                        {{ $libro->categoria->nombre ?? 'General' }}
                    </span>
                    <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $libro->titulo }}">{{ $libro->titulo }}</h6>
                    <p class="text-muted small mb-3 text-truncate">{{ $libro->autor }}</p>

                    {{-- Botón que empuja hacia abajo --}}
                    <div class="mt-auto">
                        <a href="#" class="btn btn-sm btn-light w-100 fw-medium text-primary border">Ver Detalles</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $libros->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @else
    {{-- ESTADO VACÍO --}}
    <div class="text-center py-5">
        <div class="bg-white p-5 rounded-4 shadow-sm d-inline-block border">
            <i class="bi bi-search fs-1 text-muted opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold text-dark">No hemos encontrado nada</h5>
            <p class="text-muted">Prueba con otras palabras o quita el filtro de categoría.</p>
            <a href="{{ route('catalogo.index') }}" class="btn btn-outline-primary rounded-pill px-4 mt-2">Limpiar búsqueda</a>
        </div>
    </div>
    @endif

</div>

{{-- CSS para efectos hover chulos --}}
<style>
    .book-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .backdrop-blur {
        backdrop-filter: blur(4px);
    }

    /* Ocultar barra de scroll en los filtros */
    .css-scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .css-scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection