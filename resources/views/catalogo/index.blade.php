@extends('layouts.app')

@section('title', 'Descubrir | LibreLah')

@section('content')
<div class="container py-4 mb-5">

    {{-- HERO PERSONALIZADO 2026 --}}
    <div class="row mb-5 mt-2">
        <div class="col-12 text-center">
            @php
            $primerNombre = Auth::check() ? explode(' ', Auth::user()->nombre ?? Auth::user()->name)[0] : 'Lector';
            @endphp
            <h1 class="fw-bold mb-2" style="color: var(--text-main); letter-spacing: -0.8px; font-size: 2.5rem;">
                Hola {{ $primerNombre }}, <span class="text-gradient">¿qué descubrimos hoy?</span>
            </h1>
            <p class="mx-auto mt-3" style="color: var(--text-muted); max-width: 500px; font-size: 1.05rem;">
                Encuentra tu próxima lectura buscando por título o explorando nuestro catálogo.
            </p>
        </div>
    </div>

    {{-- BARRA DE BÚSQUEDA Y BOTÓN DE FILTROS --}}
    <div class="row justify-content-center mb-4">
        <div class="col-md-10 col-lg-8">
            <form action="{{ route('catalogo.index') }}" method="GET" id="searchForm" class="d-flex gap-3 position-relative">
                <div class="search-container shadow-sm flex-grow-1">
                    <i class="bi bi-search ms-3 text-muted"></i>
                    <input type="text" name="buscar" class="form-control search-input ps-2"
                        placeholder="Buscar por título, autor..."
                        value="{{ request('buscar') }}">
                    <button type="submit" id="searchBtn" class="btn search-btn rounded-circle me-1 d-flex align-items-center justify-content-center">
                        <i class="bi bi-arrow-right text-white" id="searchIcon"></i>
                        <div class="spinner-border spinner-border-sm text-white d-none" id="searchSpinner" role="status"></div>
                    </button>
                </div>

                <button class="btn btn-icon-filter shadow-sm position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosAvanzados" title="Filtros avanzados">
                    <i class="bi bi-sliders"></i>
                    @if(request('categoria') || request('anio'))
                    <span class="position-absolute top-0 start-100 translate-middle p-1 border border-white rounded-circle" style="background-color: var(--primary);">
                        <span class="visually-hidden">Filtros activos</span>
                    </span>
                    @endif
                </button>
            </form>
        </div>
    </div>

    {{-- PÍLDORAS TOP TENDENCIAS --}}
    <div class="d-flex flex-nowrap overflow-auto pb-3 mb-4 gap-2 css-scrollbar-hide justify-content-lg-center">
        <a href="{{ route('catalogo.index') }}" class="category-pill {{ !request('categoria') ? 'active' : '' }}">
            Todo
        </a>
        @foreach($categoriasPopulares as $categoria)
        <a href="{{ route('catalogo.index', ['categoria' => $categoria->id, 'buscar' => request('buscar')]) }}"
            class="category-pill {{ request('categoria') == $categoria->id ? 'active' : '' }}">
            {{ $categoria->nombre }}
        </a>
        @endforeach
    </div>

    {{-- MODO EXPLORACIÓN (NETFLIX) - Solo si no hay búsqueda activa --}}
    @if(!request('buscar') && !request('categoria') && !request('anio'))

    {{-- FILA 1: TENDENCIAS (MÁS PRESTADOS) - PODIO ESTÁTICO DE 4 --}}
    @if(isset($librosPopulares) && $librosPopulares->count() > 0)
    <div class="mb-2 mx-auto" style="max-width: 1200px;">
        <div class="d-flex align-items-center mb-4">
            <h4 class="fw-bold m-0" style="color: var(--secondary-dark);">
                <i class="bi bi-fire me-2" style="color: var(--primary);"></i>Tendencias: Más Prestados
            </h4>
        </div>

        {{-- Usamos flex-wrap con un gap para alinear las 5 tarjetas --}}
        <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-4">
            @foreach($librosPopulares->take(5) as $libroPopular)
            <div style="width: 210px; flex-shrink: 0;">
                {{-- INVOCAMOS AL COMPONENTE --}}
                <x-book-card :libro="$libroPopular" />
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FILAS DINÁMICAS POR CATEGORÍA (Con Scroll) --}}
    @if(isset($categoriasEscaparate))
    @foreach($categoriasEscaparate as $catEscaparate)

    {{-- LÍNEA DIVISORA ELEGANTE --}}
    <hr class="mx-auto" style="max-width: 1200px; opacity: 0.08; border-color: var(--secondary-dark); margin-top: 3.5rem; margin-bottom: 2.5rem;">

    <div class="mb-2 mx-auto position-relative" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <h4 class="fw-bold m-0" style="color: var(--secondary-dark);">
                Explora: <span style="color: var(--primary);">{{ $catEscaparate->nombre }}</span>
            </h4>
            <div class="d-flex gap-3 align-items-center">
                <a href="{{ route('catalogo.index', ['categoria' => $catEscaparate->id]) }}" class="text-decoration-none fw-bold" style="color: var(--primary); font-size: 0.95rem; transition: opacity 0.2s;">
                    Ver todo <i class="bi bi-arrow-right ms-1"></i>
                </a>
                <div class="d-none d-md-flex gap-2">
                    <button class="btn btn-sm btn-light rounded-circle shadow-sm scroll-btn" onclick="scrollRow('scroll-cat-{{ $catEscaparate->id }}', -600)"><i class="bi bi-chevron-left"></i></button>
                    <button class="btn btn-sm btn-light rounded-circle shadow-sm scroll-btn" onclick="scrollRow('scroll-cat-{{ $catEscaparate->id }}', 600)"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>

        <div class="d-flex overflow-auto pb-4 gap-4 css-scrollbar-hide snap-scroll-container" id="scroll-cat-{{ $catEscaparate->id }}" style="scroll-behavior: smooth;">
            @foreach($catEscaparate->libros_destacados as $libroCat)
            <div class="snap-item" style="min-width: 210px; max-width: 210px; flex-shrink: 0;">
                {{-- INVOCAMOS AL COMPONENTE --}}
                <x-book-card :libro="$libroCat" />
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endif

    {{-- ESTADO DE RESULTADOS (CUADRÍCULA): Solo sale si has buscado o filtrado algo --}}
    @else
    <div class="d-flex justify-content-between align-items-center mb-4 mx-auto" style="max-width: 1200px;">
        <h4 class="fw-bold m-0" style="color: var(--secondary-dark);">Resultados de tu búsqueda</h4>
        <span class="text-muted small">{{ $libros->total() }} libros encontrados</span>
    </div>

    @if($libros->count() > 0)
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 justify-content-center mx-auto g-4" style="max-width: 1200px;">
        @foreach($libros as $libro)
        <div class="col">
            {{-- INVOCAMOS AL COMPONENTE --}}
            <x-book-card :libro="$libro" />
        </div>
        @endforeach
    </div>
    <div class="mt-5 d-flex justify-content-center custom-pagination">
        {{ $libros->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="text-center py-5 mt-4">
        <div class="empty-state-card p-5 mx-auto">
            <div class="mb-4 d-flex justify-content-center">
                <div class="empty-icon-wrapper"><i class="bi bi-search"></i></div>
            </div>
            <h5 class="fw-bold mb-2" style="color: var(--secondary-dark);">No encontramos coincidencias</h5>
            <p class="mb-4" style="color: var(--text-muted); font-size: 0.95rem;">Intenta buscar con otras palabras o ajusta los filtros.</p>
            <a href="{{ route('catalogo.index') }}" class="btn btn-empty-state">Limpiar filtros</a>
        </div>
    </div>
    @endif
    @endif

    {{-- OFFCANVAS DE FILTROS ESCALABLE --}}
    <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="filtrosAvanzados">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" style="color: var(--secondary-dark);">
                <i class="bi bi-sliders me-2" style="color: var(--primary);"></i> Filtros
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column">
            <form action="{{ route('catalogo.index') }}" method="GET" class="d-flex flex-column h-100">
                <input type="hidden" name="buscar" value="{{ request('buscar') }}">

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase" style="color: var(--text-muted);">Categorías</label>
                    <div class="position-relative mb-2">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.85rem;"></i>
                        <input type="text" id="filtroCategorias" class="form-control form-control-sm ps-5 bg-light border-0 py-2 rounded-3 shadow-none" placeholder="Buscar categoría...">
                    </div>

                    <div class="category-list-container ps-4 pe-2 py-2" style="max-height: 280px; overflow-y: auto;">
                        <div class="form-check custom-radio-item mb-2">
                            <input class="form-check-input" type="radio" name="categoria" id="cat_all" value="" {{ !request('categoria') ? 'checked' : '' }}>
                            <label class="form-check-label w-100" for="cat_all">Todas las categorías</label>
                        </div>
                        @foreach($categorias as $cat)
                        <div class="form-check custom-radio-item mb-2 category-filterable">
                            <input class="form-check-input" type="radio" name="categoria" id="cat_{{ $cat->id }}" value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'checked' : '' }}>
                            <label class="form-check-label w-100" for="cat_{{ $cat->id }}">{{ $cat->nombre }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase" style="color: var(--text-muted);">Año de Publicación</label>
                    <input type="number" name="anio" class="form-control border-0 bg-light shadow-none py-2 rounded-3" placeholder="Ej: 2024" value="{{ request('anio') }}">
                </div>

                <div class="mt-auto pt-3 border-top d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-3 rounded-3 fw-bold shadow-sm">Aplicar Filtros</button>
                    <a href="{{ route('catalogo.index') }}" class="btn btn-light py-3 rounded-3 text-muted fw-semibold">Restablecer</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* VARIABLES LOCALES */
        :root {
            --primary: #1E90FF;
            --secondary-dark: #0D47A1;
            --secondary-light: #64B5F6;
            --text-main: #212121;
            --text-muted: #757575;
            --bg-light: #F5F5F5;
            --primary-soft: rgba(30, 144, 255, 0.1);
        }

        body {
            color: var(--text-main);
        }

        /* TEXTO DEGRADADO */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }

        /* BUSCADOR */
        .search-container {
            border-radius: 20px;
            background: #fff;
            padding: 6px;
            display: flex;
            align-items: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .search-container:focus-within {
            box-shadow: 0 10px 25px var(--primary-soft) !important;
            border-color: var(--secondary-light);
        }

        .search-input {
            border: none !important;
            box-shadow: none !important;
            background: transparent;
            height: 48px;
            color: var(--secondary-dark);
            font-weight: 500;
        }

        .search-input::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .search-btn {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            background-color: var(--primary);
            border: none;
            transition: background-color 0.2s ease, transform 0.2s;
        }

        .search-btn:hover {
            background-color: var(--secondary-dark);
            transform: scale(1.05);
        }

        /* BOTÓN DE FILTROS */
        .btn-icon-filter {
            width: 62px;
            border-radius: 20px;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            color: var(--primary);
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-icon-filter:hover,
        .btn-icon-filter:focus {
            background: var(--primary-soft);
            color: var(--secondary-dark);
            border-color: var(--primary);
        }

        /* PÍLDORAS RÁPIDAS */
        .category-pill {
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .category-pill:hover {
            background-color: var(--bg-light);
            color: var(--secondary-dark);
        }

        .category-pill.active {
            background-color: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 4px 12px var(--primary-soft);
        }

        /* SCROLL HORIZONTAL (NETFLIX) */
        .css-scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .css-scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .snap-scroll-container {
            scroll-snap-type: x mandatory;
            scroll-padding-left: 10px;
        }

        .snap-item {
            scroll-snap-align: start;
        }

        .z-1 {
            z-index: 10 !important;
        }

        /* TARJETAS */
        .book-card {
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px var(--primary-soft);
        }

        .book-cover-container {
            width: 100%;
            aspect-ratio: 2 / 3;
            overflow: hidden;
            background-color: var(--bg-light);
        }

        .book-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .book-card:hover .book-cover {
            transform: scale(1.025);
        }

        /* TEXTOS DE TARJETA */
        .category-tag {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--primary);
        }

        .book-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--secondary-dark);
            line-height: 1.3;
        }

        .book-author {
            color: var(--text-muted);
        }

        .border-top-subtle {
            border-top: 1px solid rgba(0, 0, 0, 0.04);
        }

        /* BOTÓN DETALLES Y ESTADOS */
        .btn-outline-details {
            color: var(--primary);
            border: 1px solid var(--primary-soft);
            background: transparent;
            transition: all 0.2s ease;
        }

        .btn-outline-details:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 4px 10px var(--primary-soft);
        }

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
            box-shadow: 0 0 6px rgba(34, 197, 94, 0.4);
        }

        .status-dot.unavailable {
            background-color: var(--text-muted);
            opacity: 0.5;
        }

        .status-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* RADIO BUTTONS MODAL (Fix de margen) */
        .custom-radio-item {
            padding: 6px 10px;
            border-radius: 12px;
            transition: background 0.2s ease;
            cursor: pointer;
            margin-left: 0;
        }

        .custom-radio-item:hover {
            background: var(--bg-light);
        }

        .custom-radio-item .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .custom-radio-item .form-check-label {
            color: var(--text-main);
            font-weight: 500;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .custom-radio-item .form-check-input:checked~.form-check-label {
            color: var(--secondary-dark);
            font-weight: 700;
        }

        /* UTILIDADES GENERALES */
        .btn-primary {
            background-color: var(--primary);
            border: none;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--secondary-dark);
        }

        .skeleton-bg {
            background: linear-gradient(90deg, var(--bg-light) 25%, #e0e0e0 50%, var(--bg-light) 75%);
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

        .custom-pagination .page-item .page-link {
            border-radius: 12px;
            margin: 0 3px;
            border: none;
            color: var(--text-muted);
            font-weight: 600;
        }

        .custom-pagination .page-item.active .page-link {
            background-color: var(--primary);
            box-shadow: 0 4px 10px var(--primary-soft);
        }

        .empty-state-card {
            border-radius: 24px;
            background: #fff;
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
            border-radius: 14px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-empty-state:hover {
            background: var(--primary);
            color: white;
        }
    </style>

    <script>
        function scrollRow(containerId, scrollAmount) {
            const container = document.getElementById(containerId);
            if (container) {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
        }
        document.getElementById('searchForm').addEventListener('submit', function() {
            document.getElementById('searchIcon').classList.add('d-none');
            document.getElementById('searchSpinner').classList.remove('d-none');
        });

        document.getElementById('filtroCategorias').addEventListener('input', function(e) {
            const termino = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.category-filterable');

            items.forEach(item => {
                const texto = item.querySelector('.form-check-label').innerText.toLowerCase();
                if (texto.includes(termino)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</div>
@endsection