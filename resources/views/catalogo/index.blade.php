@extends('layouts.app')

@section('title', 'Descubrir | LibreLah')

@section('content')
<div class="container py-4 mb-5">

    {{-- HERO PERSONALIZADO 2026 --}}
    <div class="row mb-5 mt-2">
        <div class="col-12 text-center">
            @php
            // Extraemos el primer nombre del usuario, o ponemos un default
            $primerNombre = Auth::check() ? explode(' ', Auth::user()->nombre ?? Auth::user()->name)[0] : 'Lector';
            @endphp
            <h1 class="fw-bold text-dark mb-2" style="letter-spacing: -0.8px; font-size: 2.5rem;">
                Hola {{ $primerNombre }}, <span style="color: var(--primary);">¿qué descubrimos hoy?</span>
            </h1>
            <p class="text-muted mx-auto mt-3" style="max-width: 500px; font-size: 1.05rem;">
                Encuentra tu próxima lectura buscando por título o explorando nuestros filtros avanzados.
            </p>
        </div>
    </div>

    {{-- BARRA DE BÚSQUEDA Y BOTÓN DE FILTROS AVANZADOS --}}
    <div class="row justify-content-center mb-4">
        <div class="col-md-10 col-lg-8">
            <form action="{{ route('catalogo.index') }}" method="GET" id="searchForm" class="d-flex gap-2 position-relative">
                <div class="search-container shadow-sm flex-grow-1">
                    <input type="text" name="buscar" class="form-control form-control-lg search-input ps-4"
                        placeholder="Buscar por título, autor o ISBN..."
                        value="{{ request('buscar') }}">
                    <button type="submit" id="searchBtn" class="btn btn-primary search-btn rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-search" id="searchIcon"></i>
                        <div class="spinner-border spinner-border-sm text-white d-none" id="searchSpinner" role="status"></div>
                    </button>
                </div>

                {{-- Botón para abrir Offcanvas de Filtros (Año, Categorías completas) --}}
                <button class="btn btn-filter shadow-sm d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosAvanzados">
                    <i class="bi bi-sliders text-primary"></i> <span class="d-none d-sm-inline">Filtros</span>
                    @if(request('categoria') || request('anio'))
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                        <span class="visually-hidden">Filtros activos</span>
                    </span>
                    @endif
                </button>
            </form>
        </div>
    </div>

    {{-- PÍLDORAS RÁPIDAS (Solo muestra las 5 primeras o las destacadas para no saturar) --}}
    <div class="d-flex flex-nowrap overflow-auto pb-3 mb-4 gap-2 css-scrollbar-hide justify-content-lg-center">
        <a href="{{ route('catalogo.index') }}"
            class="category-pill {{ !request('categoria') ? 'active' : '' }}">
            Todo
        </a>
        @foreach($categorias->take(6) as $categoria)
        <a href="{{ route('catalogo.index', ['categoria' => $categoria->id, 'buscar' => request('buscar')]) }}"
            class="category-pill {{ request('categoria') == $categoria->id ? 'active' : '' }}">
            {{ $categoria->nombre }}
        </a>
        @endforeach
        @if($categorias->count() > 6)
        <button class="category-pill text-primary border-primary bg-transparent" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosAvanzados">
            Ver todas ({{ $categorias->count() }}) <i class="bi bi-chevron-right fs-6" style="vertical-align: -1px;"></i>
        </button>
        @endif
    </div>

    {{-- CUADRÍCULA DE LIBROS CENTRADA --}}
    @if($libros->count() > 0)
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 justify-content-center mx-auto g-4" style="max-width: 1200px;">
        @foreach($libros as $libro)
        <div class="col">
            <a href="#" class="text-decoration-none d-block h-100">
                <div class="card h-100 book-card">

                    {{-- Portada con Skeleton Loader nativo --}}
                    <div class="book-cover-container skeleton-bg">
                        @if($libro->portada)
                        {{-- onload elimina la clase skeleton cuando la imagen ya cargó --}}
                        <img src="{{ $libro->portada }}" alt="Portada de {{ $libro->titulo }}" class="book-cover" onload="this.parentElement.classList.remove('skeleton-bg'); this.style.opacity=1;" style="opacity: 0; transition: opacity 0.3s ease;">
                        @else
                        <div class="book-cover d-flex flex-column justify-content-center align-items-center bg-light">
                            <i class="bi bi-book fs-1 text-primary opacity-25"></i>
                        </div>
                        @endif
                    </div>

                    {{-- Info del libro --}}
                    <div class="card-body d-flex flex-column p-3">
                        <span class="category-tag mb-1">
                            {{ $libro->categoria->nombre ?? 'General' }}
                        </span>
                        <h6 class="book-title mb-1 text-truncate" title="{{ $libro->titulo }}">
                            {{ $libro->titulo }}
                        </h6>
                        <p class="book-author mb-3 text-truncate">
                            {{ $libro->autor }}
                        </p>

                        {{-- Footer minimalista --}}
                        <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-2" style="border-color: rgba(0,0,0,0.04) !important;">
                            <div class="status-indicator">
                                @if($libro->copias_totales > 0)
                                <span class="status-dot available"></span> <span class="status-text">Disponible</span>
                                @else
                                <span class="status-dot unavailable"></span> <span class="status-text text-muted">Agotado</span>
                                @endif
                            </div>
                            <span class="year-badge">{{ $libro->anio_publicacion ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- PAGINACIÓN CUSTOMIZADA --}}
    <div class="mt-5 d-flex justify-content-center custom-pagination">
        {{ $libros->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

    @else
    {{-- ESTADO VACÍO REDISEÑADO --}}
    <div class="text-center py-5 mt-4">
        <div class="empty-state-card p-5 mx-auto">
            <div class="mb-4 d-flex justify-content-center">
                <div class="empty-icon-wrapper">
                    <i class="bi bi-search"></i>
                </div>
            </div>
            <h5 class="fw-bold text-dark mb-2">No encontramos coincidencias</h5>
            <p class="text-muted mb-4" style="font-size: 0.95rem;">Intenta buscar con otras palabras o ajusta los filtros en el menú avanzado.</p>
            <a href="{{ route('catalogo.index') }}" class="btn btn-empty-state">Limpiar filtros de búsqueda</a>
        </div>
    </div>
    @endif

</div>

{{-- OFFCANVAS DE FILTROS AVANZADOS (Aparece desde la derecha) --}}
<div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="filtrosAvanzados">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold text-dark"><i class="bi bi-sliders text-primary me-2"></i>Filtros Avanzados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('catalogo.index') }}" method="GET">
            <input type="hidden" name="buscar" value="{{ request('buscar') }}">

            <div class="mb-4">
                <label class="form-label fw-bold text-dark small text-uppercase">Categoría</label>
                <select name="categoria" class="form-select form-select-lg border-0 bg-light shadow-none">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-dark small text-uppercase">Año de Publicación</label>
                <input type="number" name="anio" class="form-control form-control-lg border-0 bg-light shadow-none" placeholder="Ej: 2021" value="{{ request('anio') }}">
            </div>

            <div class="d-grid gap-2 mt-5">
                <button type="submit" class="btn btn-primary py-3 rounded-3 fw-bold">Aplicar Filtros</button>
                <a href="{{ route('catalogo.index') }}" class="btn btn-light py-3 rounded-3 text-muted">Restablecer</a>
            </div>
        </form>
    </div>
</div>

{{-- MAGIA CSS 2026 --}}
<style>
    /* Variables Locales de Ajuste */
    :root {
        --primary-soft: rgba(30, 144, 255, 0.1);
        --primary-hover: #0D47A1;
    }

    /* Buscador */
    .search-container {
        border-radius: 16px;
        background: #fff;
        padding: 6px;
        display: flex;
        align-items: center;
        border: 1px solid rgba(0, 0, 0, 0.06);
        transition: box-shadow 0.3s ease, border-color 0.3s ease;
    }

    .search-container:focus-within {
        box-shadow: 0 8px 30px var(--primary-soft) !important;
        border-color: rgba(30, 144, 255, 0.4);
    }

    .search-input {
        border: none !important;
        box-shadow: none !important;
        background: transparent;
        height: 48px;
    }

    .search-btn {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        transition: background-color 0.2s ease;
    }

    /* Botón Filtros Laterales */
    .btn-filter {
        border-radius: 16px;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 0 20px;
        font-weight: 600;
        color: var(--text-main);
        transition: all 0.3s ease;
        position: relative;
    }

    .btn-filter:hover {
        background: var(--primary-soft);
        border-color: var(--primary);
    }

    /* Píldoras de Categoría */
    .category-pill {
        padding: 0.6rem 1.4rem;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-muted);
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.25s ease;
    }

    .category-pill:hover {
        background-color: #f8f9fa;
        color: var(--text-main);
        transform: translateY(-1px);
    }

    .category-pill.active {
        background-color: var(--primary);
        color: #fff;
        border-color: var(--primary);
        box-shadow: 0 4px 12px var(--primary-soft);
    }

    .css-scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .css-scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* TARJETAS - Adiós Fósiles */
    .book-card {
        border-radius: 16px;
        background: linear-gradient(145deg, #ffffff, #fafafa);
        /* Textura levísima */
        border: 1px solid rgba(30, 144, 255, 0.08);
        /* Borde azul muy sutil */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
        overflow: hidden;
    }

    .book-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(30, 144, 255, 0.08);
        border-color: rgba(30, 144, 255, 0.2);
    }

    /* Feedback táctil en móviles */
    .book-card:active {
        transform: scale(0.97);
    }

    /* Portadas y Skeleton */
    .book-cover-container {
        width: 100%;
        aspect-ratio: 2 / 3;
        overflow: hidden;
        background-color: #f0f2f5;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }

    .book-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .book-card:hover .book-cover {
        transform: scale(1.04);
    }

    /* Animación del Skeleton */
    .skeleton-bg {
        background: linear-gradient(90deg, #f0f2f5 25%, #e6e8eb 50%, #f0f2f5 75%);
        background-size: 200% 100%;
        animation: loadingShimmer 1.5s infinite linear;
    }

    @keyframes loadingShimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Tipografía interior de la tarjeta */
    .category-tag {
        font-size: 0.65rem;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--primary);
    }

    .book-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .book-author {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    /* Footer de la tarjeta */
    .status-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-dot.available {
        background-color: #22c55e;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
    }

    .status-dot.unavailable {
        background-color: #ef4444;
    }

    .status-text {
        font-size: 0.75rem;
        font-weight: 600;
        color: #444;
    }

    .year-badge {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-muted);
        background: rgba(0, 0, 0, 0.04);
        padding: 3px 8px;
        border-radius: 6px;
    }

    /* Paginación Customizada (Sobrescribe Bootstrap) */
    .custom-pagination .pagination {
        gap: 4px;
    }

    .custom-pagination .page-item .page-link {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: var(--text-muted);
        font-weight: 500;
        transition: all 0.2s;
    }

    .custom-pagination .page-item.active .page-link {
        background-color: var(--primary);
        color: white;
        box-shadow: 0 4px 10px var(--primary-soft);
    }

    .custom-pagination .page-item .page-link:hover:not(.active) {
        background-color: var(--primary-soft);
        color: var(--primary);
    }

    /* Estado Vacío */
    .empty-state-card {
        max-width: 450px;
        background: #fff;
        border-radius: 24px;
        border: 1px dashed rgba(0, 0, 0, 0.1);
    }

    .empty-icon-wrapper {
        width: 72px;
        height: 72px;
        background: var(--primary-soft);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--primary);
        transform: rotate(-5deg);
    }

    .btn-empty-state {
        background: transparent;
        border: 2px solid var(--primary);
        color: var(--primary);
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-empty-state:hover {
        background: var(--primary);
        color: white;
    }
</style>

{{-- JS para el estado de carga del buscador --}}
<script>
    document.getElementById('searchForm').addEventListener('submit', function() {
        document.getElementById('searchIcon').classList.add('d-none');
        document.getElementById('searchSpinner').classList.remove('d-none');
    });
</script>
@endsection