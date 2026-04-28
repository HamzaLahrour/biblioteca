@extends('layouts.admin')

@section('title', 'Gestión de Categorías')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">

    {{-- CABECERA CON CONTRASTE --}}
    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-slate-900 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-tags-fill fs-5"></i>
            </div>
            Listado de Categorías
        </h5>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium transition-all btn-hover-elevate">
            <i class="bi bi-plus-circle me-1"></i> Nueva Categoría
        </a>
    </div>

    {{-- CUERPO SIN PADDING PARA QUE LA TABLA CUBRA TODO --}}
    <div class="card-body p-0">
        @if($categorias->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 border-top">
                {{-- CABECERA GRIS PARA ROMPER EL BLANCO --}}
                <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.05em;">
                    <tr>
                        <th scope="col" class="border-0 py-3 ps-4" style="width: 30%;">Nombre</th>
                        <th scope="col" class="border-0 py-3">Descripción</th>
                        <th scope="col" class="border-0 py-3 pe-4 text-end" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($categorias as $categoria)
                    <tr>
                        {{-- NOMBRE --}}
                        <td class="py-4 ps-4 border-bottom-subtle">
                            <div class="fw-bold text-slate-900 fs-6">{{ $categoria->nombre }}</div>
                        </td>

                        {{-- DESCRIPCIÓN CON MEJORA VISUAL --}}
                        <td class="py-4 border-bottom-subtle">
                            @if($categoria->descripcion)
                            <span class="text-slate-600" style="font-size: 0.95rem; line-height: 1.5;">
                                {{ Str::limit($categoria->descripcion, 75) }}
                            </span>
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 fw-medium" style="font-size: 0.8rem;">
                                Sin descripción
                            </span>
                            @endif
                        </td>

                        {{-- ACCIONES EN CÁPSULA GRIS --}}
                        <td class="text-end py-4 pe-4 border-bottom-subtle">
                            <div class="d-inline-flex bg-light border border-secondary-subtle rounded-pill p-1 shadow-sm-inner">
                                <a href="{{ route('categorias.show', $categoria->id) }}" class="btn btn-sm rounded-circle text-primary btn-action-hover" title="Ver detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-sm rounded-circle text-secondary btn-action-hover mx-1" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
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

        {{-- PAGINACIÓN CON FONDO BLANCO Y SEPARACIÓN --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 p-4 bg-white border-top custom-pagination">
            <div class="text-muted small bg-light px-3 py-2 rounded-pill border border-neutral-100 shadow-sm-inner">
                Mostrando del <span class="fw-bold text-slate-900">{{ $categorias->firstItem() ?? 0 }}</span> al <span class="fw-bold text-slate-900">{{ $categorias->lastItem() ?? 0 }}</span> de <span class="fw-bold text-primary">{{ $categorias->total() ?? 0 }}</span> resultados
            </div>

            <div class="pagination-wrapper">
                {{ $categorias->links('pagination::bootstrap-5') }}
            </div>
        </div>

        @else
        {{-- ESTADO VACÍO PULIDO --}}
        <div class="text-center py-5 my-4 mx-4 bg-light rounded-4 border border-dashed border-secondary border-opacity-25">
            <div class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-tags fs-1 text-primary opacity-75"></i>
            </div>
            <h5 class="fw-bold text-slate-900 mb-2">No hay categorías registradas</h5>
            <p class="text-muted mb-4 px-3">Empieza añadiendo tu primera categoría para organizar los libros de la biblioteca.</p>
            <a href="{{ route('categorias.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
                <i class="bi bi-plus-circle me-1"></i> Añadir Categoría
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    /* Colores personalizados para asegurar el contraste */
    :root {
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-600: #475569;
    }

    .text-slate-900 {
        color: var(--slate-900) !important;
    }

    .text-slate-800 {
        color: var(--slate-800) !important;
    }

    .text-slate-600 {
        color: var(--slate-600) !important;
    }

    /* Detalles de la tabla */
    .border-bottom-subtle {
        border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.02) !important;
    }

    .shadow-sm-inner {
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

    .border-dashed {
        border-style: dashed !important;
    }

    /* Efecto de elevar botón principal */
    .btn-hover-elevate {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-hover-elevate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25) !important;
    }

    /* Estilo para los botones de acción en pastilla */
    .btn-action-hover {
        width: 32px;
        height: 32px;
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
    }

    .custom-pagination nav>div.d-none.flex-sm-fill.d-sm-flex>div:first-child {
        display: none !important;
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
        color: var(--slate-600);
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
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
    }

    .custom-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: transparent;
        border-color: #e2e8f0;
        opacity: 0.6;
    }

    .custom-pagination .page-link:hover:not(.active):not(.disabled) {
        color: var(--primary);
        background-color: rgba(13, 110, 253, 0.05);
        border-color: #bfdbfe;
    }
</style>
@endsection