@extends('layouts.admin')

@section('title', 'Gestión de Libros')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-book-half me-2"></i>Catálogo de Libros
        </h5>
        <a href="{{ route('libros.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Añadir Libro
        </a>
    </div>

    <div class="card-body p-4 pt-3">

        {{-- 🔍 BARRA DE FILTROS PULIDA (Minimalista) --}}
        <div class="bg-light p-3 rounded-4 mb-4 border-0 shadow-inner">
            <form action="{{ route('libros.index') }}" method="GET" class="row g-2 align-items-center">

                {{-- Buscador de Texto Libre --}}
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0 shadow-none" placeholder="Buscar por título, autor o ISBN..." value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- Filtro de Categoría --}}
                <div class="col-md-4">
                    <select name="categoria_id" class="form-select form-select-sm shadow-none border-secondary-subtle rounded-3">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botones de Acción --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-medium shadow-sm rounded-pill">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['buscar', 'categoria_id']))
                    <a href="{{ route('libros.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle px-2" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLA DE DATOS (Pulida) --}}
        @if($libros->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3 pl-3" style="width: 70px;">Portada</th>
                        <th scope="col" class="border-0 py-3 pl-2">Título y Autor</th>
                        <th scope="col" class="border-0 py-3">Inventario</th>
                        <th scope="col" class="border-0 py-3 text-center">Stock Real</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end pl-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($libros as $libro)
                    <tr>
                        {{-- PORTADA con pulido sutil --}}
                        <td class="py-3 pl-3 border-bottom-subtle">
                            @if($libro->portada)
                            <img src="{{ $libro->portada }}" alt="Portada" class="rounded shadow-sm border-0" style="width: 45px; height: 65px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex justify-content-center align-items-center border rounded shadow-sm" style="width: 45px; height: 65px;">
                                <i class="bi bi-journal-text text-muted fs-5"></i>
                            </div>
                            @endif
                        </td>

                        {{-- TÍTULO Y AUTOR --}}
                        <td class="py-3 pl-2 border-bottom-subtle">
                            <div class="fw-bold text-dark text-wrap" style="max-width: 250px; font-size: 1.05rem;">{{ $libro->titulo }}</div>
                            <div class="text-primary mt-1 fw-medium" style="font-size: 0.85rem;"><i class="bi bi-person me-1"></i>{{ $libro->autor }}</div>
                        </td>

                        {{-- INVENTARIO (ISBN + Categoría) --}}
                        <td class="py-3 border-bottom-subtle">
                            <div class="mb-2">
                                @if($libro->isbn)
                                <span class="font-monospace text-muted small"><i class="bi bi-upc-scan me-1"></i>{{ $libro->isbn }}</span>
                                @else
                                <span class="badge bg-light bg-opacity-50 text-secondary border-0 small px-2 py-1">Sin ISBN</span>
                                @endif
                            </div>
                            <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle rounded-pill px-3 py-2 fw-medium" style="font-size: 0.85rem;">
                                {{ $libro->categoria->nombre ?? 'Sin Categoría' }}
                            </span>
                        </td>

                        {{-- COPIAS / STOCK REAL (CÁPSULAS TAMAÑO NORMAL, VERDE/ROJO sutiles) --}}
                        <td class="text-center py-3 border-bottom-subtle">
                            @php
                            $prestados = $libro->prestamos()->where('estado', 'activo')->count();
                            $disponibles = $libro->copias_totales - $prestados;
                            @endphp

                            <div class="d-flex justify-content-center">
                                @if($disponibles <= 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-medium" style="font-size: 0.85rem;">
                                    0 <span class="fw-normal opacity-75">/ {{ $libro->copias_totales }}</span> <span class="ms-1 fw-normal opacity-75">— Agotado</span>
                                    </span>
                                    @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-2 fw-medium" style="font-size: 0.85rem;">
                                        {{ $disponibles }} <span class="fw-normal opacity-75">/ {{ $libro->copias_totales }}</span>
                                    </span>
                                    @endif
                            </div>
                        </td>

                        {{-- ACCIONES: Metidos en una pastilla (pill) para no flotar --}}
                        <td class="text-end py-3 pl-3 border-bottom-subtle">
                            <div class="d-inline-flex bg-light border border-secondary-subtle rounded-pill p-1">
                                <a href="{{ route('libros.show', $libro->id) }}" class="btn btn-sm rounded-circle text-primary btn-action-hover" title="Ver detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('libros.edit', $libro->id) }}" class="btn btn-sm rounded-circle text-secondary btn-action-hover mx-1" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('libros.destroy', $libro->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm rounded-circle text-danger btn-action-hover" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN CON MUCHO AIRE Y TRADUCIDA AL ESPAÑOL --}}
        <div class="mt-5 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 custom-pagination">
            <div class="text-muted small bg-light px-3 py-2 rounded-pill border border-neutral-100">
                Mostrando del <span class="fw-bold text-dark">{{ $libros->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $libros->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ $libros->total() }}</span> resultados
            </div>

            <div class="pagination-wrapper">
                {{ $libros->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>

        @else
        <div class="text-center py-5 my-3 text-muted bg-light rounded-4 border border-dashed border-secondary border-opacity-25">
            <i class="bi bi-search fs-1 d-block mb-3 text-secondary opacity-50"></i>
            <h5 class="fw-bold text-dark">No se encontraron resultados</h5>
            <p>Prueba a cambiar los filtros de búsqueda o limpia el formulario.</p>
            @if(request()->anyFilled(['buscar', 'categoria_id']))
            <a href="{{ route('libros.index') }}" class="btn btn-outline-primary rounded-pill mt-2 px-4 shadow-sm fw-medium">Limpiar Filtros</a>
            @endif
        </div>
        @endif
    </div>
</div>

<style>
    /* Pulido de bordes y sombras */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.04) !important;
    }

    .shadow-inner {
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    }

    .border-bottom-subtle {
        border-bottom: 1px solid rgba(0, 0, 0, 0.03) !important;
    }

    .border-dashed {
        border-style: dashed !important;
    }

    /* Pulido de filas de tabla */
    .table-hover tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.01) !important;
    }

    /* Estilo para los nuevos botones de acción en pastilla */
    .btn-action-hover {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
    }

    .btn-action-hover:hover {
        background-color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    /* === MAGIA PARA LA PAGINACIÓN === */
    .custom-pagination nav>div.d-flex.justify-content-between.flex-fill.d-sm-none {
        display: none !important;
        /* Oculta texto en móvil */
    }

    .custom-pagination nav>div.d-none.flex-sm-fill.d-sm-flex>div:first-child {
        display: none !important;
        /* Oculta texto en desktop */
    }

    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
    }

    .custom-pagination .pagination {
        margin-bottom: 0;
        gap: 5px;
        border: none;
    }

    .custom-pagination .page-item:first-child .page-link,
    .custom-pagination .page-item:last-child .page-link {
        border-radius: 50px;
    }

    .custom-pagination .page-link {
        border-radius: 50px;
        color: var(--slate-700);
        background-color: transparent;
        border: 1px solid #e2e8f0;
        padding: 0.45rem 0.9rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .custom-pagination .page-item.active .page-link {
        color: white;
        background-color: var(--primary);
        border-color: var(--primary);
        box-shadow: 0 2px 5px rgba(59, 130, 246, 0.2);
    }

    .custom-pagination .page-item.disabled .page-link {
        color: #94a3b8;
        background-color: transparent;
        border-color: #e2e8f0;
        opacity: 0.6;
    }

    .custom-pagination .page-link:hover:not(.active):not(.disabled) {
        color: var(--primary);
        background-color: var(--primary-soft);
        border-color: #bfdbfe;
    }
</style>
@endsection