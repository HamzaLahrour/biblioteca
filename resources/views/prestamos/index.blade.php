@extends('layouts.admin')

@section('title', 'Gestión de Préstamos')

@section('content')
<div class="container-fluid px-0">

    {{-- ENCABEZADO Y BOTÓN NUEVO --}}
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--secondary-dark);">
                <i class="bi bi-journal-arrow-up me-2 text-primary"></i>Préstamos
            </h3>
            <p class="text-muted small mb-0">Gestiona las salidas, devoluciones y retrasos del catálogo.</p>
        </div>
        <div>
            <a href="{{ route('prestamos.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
                <i class="bi bi-plus-lg me-1"></i> Nuevo Préstamo
            </a>
        </div>
    </div>

    {{-- BARRA DE HERRAMIENTAS AVANZADA --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
        <div class="card-body p-3">

            {{-- PÍLDORAS DE ESTADO --}}
            <div class="d-flex gap-2 mb-3 overflow-auto pb-1 border-bottom border-light">
                <a href="{{ request()->fullUrlWithQuery(['estado' => null, 'page' => null]) }}" class="btn btn-sm rounded-pill px-3 {{ !request('estado') ? 'btn-primary text-white shadow-sm' : 'btn-light border text-muted' }}">
                    Todos
                </a>
                <a href="{{ request()->fullUrlWithQuery(['estado' => 'activo', 'page' => null]) }}" class="btn btn-sm rounded-pill px-3 {{ request('estado') == 'activo' ? 'btn-success text-white shadow-sm' : 'btn-light border text-success' }}">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Activos
                </a>
                <a href="{{ request()->fullUrlWithQuery(['estado' => 'devuelto_tarde', 'page' => null]) }}" class="btn btn-sm rounded-pill px-3 {{ request('estado') == 'devuelto_tarde' ? 'btn-warning text-dark shadow-sm' : 'btn-light border text-warning' }}">
                    <i class="bi bi-clock-history me-1"></i> Devueltos tarde
                </a>
                <a href="{{ request()->fullUrlWithQuery(['estado' => 'devuelto', 'page' => null]) }}" class="btn btn-sm rounded-pill px-3 {{ request('estado') == 'devuelto' ? 'btn-secondary text-white shadow-sm' : 'btn-light border text-secondary' }}">
                    <i class="bi bi-check2-all me-1"></i> Devueltos (Ok)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['estado' => 'perdido', 'page' => null]) }}" class="btn btn-sm rounded-pill px-3 {{ request('estado') == 'perdido' ? 'btn-danger text-white shadow-sm' : 'btn-light border text-danger' }}">
                    <i class="bi bi-x-circle me-1"></i> Perdidos
                </a>
            </div>

            {{-- FORMULARIO DE FECHAS Y ORDEN --}}
            <form action="{{ route('prestamos.index') }}" method="GET" class="row g-2 align-items-end">
                @if(request('estado'))
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                @endif

                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold mb-1">Desde fecha:</label>
                    <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold mb-1">Hasta fecha:</label>
                    <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold mb-1">Orden:</label>
                    <select name="orden" class="form-select form-select-sm">
                        <option value="desc" {{ request('orden', 'desc') == 'desc' ? 'selected' : '' }}>Más recientes primero</option>
                        <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Más antiguos primero</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 fw-medium">
                        <i class="bi bi-filter me-1"></i> Aplicar
                    </button>
                    @if(request('desde') || request('hasta') || request('orden') == 'asc')
                    <a href="{{ route('prestamos.index', ['estado' => request('estado')]) }}" class="btn btn-sm btn-light border" title="Limpiar filtros">
                        <i class="bi bi-eraser"></i>
                    </a>
                    @endif
                </div>
            </form>

        </div>
    </div>

    {{-- TABLA PRINCIPAL --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0 px-4 pb-4 pt-2">
            @if($prestamos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 mt-3">
                    <thead class="small text-uppercase" style="color: var(--text-muted); letter-spacing: 0.5px;">
                        <tr>
                            <th class="border-bottom-0 pb-3">Lector</th>
                            <th class="border-bottom-0 pb-3">Ejemplar</th>
                            <th class="border-bottom-0 pb-3">Fechas</th>
                            <th class="border-bottom-0 pb-3 text-center">Estado</th>
                            <th class="border-bottom-0 pb-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($prestamos as $prestamo)
                        <tr>
                            {{-- LECTOR --}}
                            <td class="py-3 border-bottom-subtle">
                                <div class="d-flex align-items-center">
                                    <x-user-avatar :user="$prestamo->user" class="me-3" />
                                    <div>
                                        <span class="d-block fw-bold text-dark">{{ $prestamo->user->name ?? 'Usuario Eliminado' }}</span>
                                        <span class="d-block small text-muted">{{ $prestamo->user->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- EJEMPLAR --}}
                            <td class="py-3 border-bottom-subtle" style="max-width: 250px;">
                                <span class="d-block fw-bold text-dark text-truncate" title="{{ $prestamo->libro->titulo ?? 'Libro Eliminado' }}">
                                    {{ $prestamo->libro->titulo ?? 'Libro Eliminado' }}
                                </span>
                            </td>

                            {{-- FECHAS --}}
                            <td class="py-3 border-bottom-subtle">
                                <div class="small fw-medium text-dark mb-1">
                                    <i class="bi bi-calendar-event text-muted me-1"></i> Salida: {{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y') }}
                                </div>
                                <div class="small {{ \Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista)->isPast() && $prestamo->estado == 'activo' ? 'text-danger fw-bold' : 'text-muted' }}">
                                    <i class="bi bi-calendar-check me-1"></i> Límite: {{ \Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista)->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- ESTADO --}}
                            <td class="py-3 text-center border-bottom-subtle">
                                @if($prestamo->estado === 'activo')
                                @if(\Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista)->isPast())
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-2 fw-medium">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Retrasado
                                </span>
                                @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-2 fw-medium">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Activo
                                </span>
                                @endif
                                @elseif($prestamo->estado === 'devuelto')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill px-3 py-2 fw-medium">
                                    <i class="bi bi-check2 me-1"></i> Devuelto
                                </span>
                                @elseif($prestamo->estado === 'devuelto_tarde')
                                <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 fw-medium">
                                    <i class="bi bi-clock-history me-1"></i> Tarde ({{ $prestamo->dias_retraso }}d)
                                </span>
                                @elseif($prestamo->estado === 'perdido')
                                <span class="badge bg-danger text-white rounded-pill px-3 py-2 shadow-sm fw-medium">
                                    <i class="bi bi-x-circle me-1"></i> Perdido
                                </span>
                                @endif
                            </td>

                            {{-- ACCIONES (Desplegable 2026) --}}
                            <td class="py-3 text-end border-bottom-subtle">
                                <div class="dropdown dropstart">
                                    <button
                                        class="btn btn-light btn-sm rounded-circle border"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        data-bs-boundary="window"
                                        aria-expanded="false"
                                        style="width: 32px; height: 32px; transition: all 0.2s;">
                                        <i class="bi bi-three-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-2">
                                        <li>
                                            <a class="dropdown-item rounded-3 mb-1" href="{{ route('prestamos.show', $prestamo->id) }}">
                                                <i class="bi bi-eye text-primary me-2"></i> Ver detalles
                                            </a>
                                        </li>

                                        @if($prestamo->estado === 'activo')
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        {{-- BOTÓN: DEVOLVER --}}
                                        <li>
                                            <form action="{{ route('prestamos.devolver', $prestamo->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item rounded-3 fw-medium text-success mb-1" onclick="return confirm('¿Confirmas la recepción del libro físico?');">
                                                    <i class="bi bi-box-arrow-in-right me-2"></i> Procesar Devolución
                                                </button>
                                            </form>
                                        </li>

                                        {{-- BOTÓN: RENOVAR --}}
                                        <li>
                                            <form action="{{ route('prestamos.renovar', $prestamo->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item rounded-3 mb-1" onclick="return confirm('¿Ampliar el plazo de este préstamo?');">
                                                    <i class="bi bi-arrow-clockwise text-info me-2"></i> Renovar
                                                </button>
                                            </form>
                                        </li>

                                        {{-- BOTÓN: MARCAR PERDIDO --}}
                                        <li>
                                            <form action="{{ route('prestamos.perdido', $prestamo->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item rounded-3 text-danger" onclick="return confirm('ATENCIÓN: Esto sancionará al usuario. ¿Seguro que se ha perdido?');">
                                                    <i class="bi bi-exclamation-triangle me-2"></i> Marcar como Perdido
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN TRADUCIDA CON AIRE --}}
            <div class="mt-5 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 custom-pagination">
                <div class="text-muted small bg-light px-3 py-2 rounded-pill border border-neutral-100 shadow-sm-inner">
                    Mostrando del <span class="fw-bold text-dark">{{ $prestamos->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $prestamos->lastItem() ?? 0 }}</span> de <span class="fw-bold text-primary">{{ $prestamos->total() ?? 0 }}</span> resultados
                </div>

                <div class="pagination-wrapper">
                    {{ $prestamos->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>

            @else
            {{-- ESTADO VACÍO --}}
            <div class="text-center py-5 my-4">
                <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px; background-color: var(--bg-light);">
                    <i class="bi bi-inbox fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No hay préstamos</h5>
                <p class="text-muted mb-4">No se encontraron registros con los filtros actuales.</p>
                <a href="{{ route('prestamos.create') }}" class="btn btn-primary rounded-pill px-4">
                    Crear primer préstamo
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .border-bottom-subtle {
        border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.02) !important;
    }

    .shadow-sm-inner {
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

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