@extends('layouts.admin')

@section('title', 'Gestión de Tipos de Espacios')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-tags me-2"></i>Listado de Tipos de Espacios
        </h5>
        <a href="{{ route('tipos_espacios.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Tipo de Espacio
        </a>
    </div>

    <div class="card-body p-4">
        @if($tipoEspacios->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3">Nombre</th>
                        <th scope="col" class="border-0 py-3">Descripción</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($tipoEspacios as $tipoEspacio)
                    <tr>
                        <td class="fw-medium text-dark py-3 border-bottom-subtle">{{ $tipoEspacio->nombre }}</td>
                        <td class="text-muted py-3 border-bottom-subtle">
                            {{ Str::limit($tipoEspacio->descripcion, 60) ?: 'Sin descripción' }}
                        </td>

                        {{-- ACCIONES CON ESTILO CÁPSULA --}}
                        <td class="text-end py-3 pe-4 border-bottom-subtle">
                            <div class="d-inline-flex bg-light border border-secondary-subtle rounded-pill p-1 shadow-sm-inner">
                                <a href="{{ route('tipos_espacios.show', $tipoEspacio->id) }}" class="btn btn-sm rounded-circle text-primary btn-action-hover" title="Ver detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('tipos_espacios.edit', $tipoEspacio->id) }}" class="btn btn-sm rounded-circle text-secondary btn-action-hover mx-1" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('tipos_espacios.destroy', $tipoEspacio->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este tipo de espacio?');">
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

        {{-- PAGINACIÓN DETALLADA --}}
        <div class="mt-5 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 custom-pagination">
            <div class="text-muted small bg-light px-3 py-2 rounded-pill border border-neutral-100 shadow-sm-inner">
                Mostrando del <span class="fw-bold text-dark">{{ $tipoEspacios->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $tipoEspacios->lastItem() ?? 0 }}</span> de <span class="fw-bold text-primary">{{ $tipoEspacios->total() ?? 0 }}</span> resultados
            </div>

            <div class="pagination-wrapper">
                {{ $tipoEspacios->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @else
        <div class="text-center py-5 text-muted bg-light rounded-4 border border-dashed my-3">
            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary opacity-50"></i>
            <h5 class="fw-bold text-dark">No hay ningún Tipo de Espacio registrado.</h5>
            <p>Añade los tipos de espacio que existen en tu biblioteca (por ejemplo, estudio, informática o lectura) para estructurar y gestionar correctamente cada área.</p>
        </div>
        @endif
    </div>
</div>

<style>
    /* Estilos para las acciones */
    .border-bottom-subtle {
        border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
    }

    .shadow-sm-inner {
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

    .btn-action-hover {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-action-hover:hover {
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    /* Estilos de la Paginación */
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
        color: #475569;
        background-color: transparent;
        border: 1px solid #e2e8f0;
        padding: 0.45rem 0.9rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .custom-pagination .page-item.active .page-link {
        color: white;
        background-color: var(--bs-primary, #0d6efd);
        border-color: var(--bs-primary, #0d6efd);
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
    }

    .custom-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: transparent;
        border-color: #e2e8f0;
        opacity: 0.6;
    }

    .custom-pagination .page-link:hover:not(.active):not(.disabled) {
        color: var(--bs-primary, #0d6efd);
        background-color: rgba(13, 110, 253, 0.05);
        border-color: #bfdbfe;
    }
</style>
@endsection