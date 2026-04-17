@extends('layouts.admin')

@section('title', 'Detalles del Libro')

@section('content')
<div class="row justify-content-center mb-5">
    <div class="col-md-10 col-lg-9">

        <div class="mb-3">
            <a href="{{ route('libros.index') }}" class="text-decoration-none text-muted fw-medium">
                <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
            </a>
        </div>

        {{-- ========================================== --}}
        {{-- FICHA DEL LIBRO --}}
        {{-- ========================================== --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-book-half me-2 text-primary"></i>Ficha Literaria
                </h4>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2 shadow-sm font-monospace">
                    ID: {{ substr($libro->id, 0, 8) }}
                </span>
            </div>

            <div class="card-body p-4">
                <div class="row">
                    {{-- COLUMNA DE PORTADA Y STOCK --}}
                    <div class="col-md-4 text-center mb-4 mb-md-0 d-flex flex-column align-items-center">
                        @if($libro->portada)
                        <img src="{{ $libro->portada }}" alt="Portada" class="img-fluid rounded-3 shadow border" style="max-height: 300px; object-fit: cover;">
                        @else
                        <div class="bg-light d-flex flex-column justify-content-center align-items-center border rounded-3 shadow-sm" style="height: 300px; width: 100%; max-width: 200px;">
                            <i class="bi bi-book text-muted opacity-50" style="font-size: 5rem;"></i>
                            <span class="text-muted mt-3 fw-medium">Sin portada</span>
                        </div>
                        @endif

                        {{-- EL STOCK REAL (Mejorado) --}}
                        <div class="mt-4 w-100 px-3">
                            <h6 class="fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Disponibilidad</h6>
                            <div class="d-flex justify-content-center align-items-center p-2 rounded-4 border {{ $disponibles > 0 ? 'bg-success-subtle border-success-subtle' : 'bg-danger-subtle border-danger-subtle' }}">
                                <span class="fs-4 fw-bold {{ $disponibles > 0 ? 'text-success' : 'text-danger' }} me-1">{{ $disponibles }}</span>
                                <span class="text-muted small">/ {{ $libro->copias_totales }} en estantería</span>
                            </div>
                            @if($disponibles <= 0)
                                <div class="text-danger small fw-bold mt-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>Agotado temporalmente
                        </div>
                        @endif
                    </div>
                </div>

                {{-- COLUMNA DE DATOS --}}
                <div class="col-md-8">
                    <h2 class="fw-bold text-dark mb-1">{{ $libro->titulo }}</h2>
                    <h5 class="text-muted mb-4"><i class="bi bi-pen-fill me-2 fs-6"></i>{{ $libro->autor }}</h5>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-bold">Categoría:</div>
                        <div class="col-sm-8">
                            <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle fs-6 px-3">
                                <i class="bi bi-bookmark-fill me-1"></i> {{ $libro->categoria->nombre ?? 'Sin Categoría' }}
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
                        <div class="col-sm-8 text-secondary">{{ $libro->editorial ?: 'Desconocida' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted fw-bold">Publicación:</div>
                        <div class="col-sm-8 text-secondary">{{ $libro->anio_publicacion ?: 'Desconocido' }}</div>
                    </div>

                    <hr class="text-muted opacity-10 my-4">

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Sinopsis:</h6>
                        <p class="text-secondary" style="line-height: 1.6; text-align: justify;">
                            {{ $libro->descripcion ?: 'No hay ninguna descripción disponible para este ejemplar.' }}
                        </p>
                    </div>
                </div>
            </div>

            <hr class="text-muted opacity-10 mt-2 mb-3">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('libros.edit', $libro) }}" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <form action="{{ route('libros.destroy', $libro) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="bi bi-trash-fill me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- HISTORIAL DE PRÉSTAMOS (Dashboard) --}}
    {{-- ========================================== --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="bi bi-activity me-2 text-primary"></i>Actividad del Ejemplar
            </h5>
        </div>

        <div class="card-body p-4 pt-2">

            {{-- 📊 MINI-ESTADÍSTICAS RÁPIDAS --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="bg-light border rounded-4 p-3 text-center h-100 shadow-sm">
                        <div class="fs-3 fw-bold text-dark mb-0 line-height-1">{{ $stats['total'] }}</div>
                        <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Registros Totales</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-primary-subtle border border-primary-subtle rounded-4 p-3 text-center h-100 shadow-sm">
                        <div class="fs-3 fw-bold text-primary mb-0 line-height-1">{{ $stats['activos'] }}</div>
                        <div class="text-primary-emphasis small text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">En Posesión</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-success-subtle border border-success-subtle rounded-4 p-3 text-center h-100 shadow-sm">
                        <div class="fs-3 fw-bold text-success mb-0 line-height-1">{{ $stats['devueltos'] }}</div>
                        <div class="text-success-emphasis small text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Devueltos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="bg-danger-subtle border border-danger-subtle rounded-4 p-3 text-center h-100 shadow-sm">
                        <div class="fs-3 fw-bold text-danger mb-0 line-height-1">{{ $stats['perdidos'] }}</div>
                        <div class="text-danger-emphasis small text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Perdidos</div>
                    </div>
                </div>
            </div>

            {{-- 🔍 Filtros con mayor peso visual --}}
            <div class="bg-dark bg-opacity-10 p-3 rounded-4 mb-4 border border-secondary border-opacity-25">
                <form action="{{ route('libros.show', $libro->id) }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="buscar_lector" class="form-control border-start-0 shadow-none" placeholder="Buscar por alumno..." value="{{ request('buscar_lector') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="estado_prestamo" class="form-select form-select-sm shadow-none border-0">
                            <option value="">Cualquier estado</option>
                            <option value="activo" {{ request('estado_prestamo') == 'activo' ? 'selected' : '' }}>En posesión (Activo)</option>
                            <option value="devuelto" {{ request('estado_prestamo') == 'devuelto' ? 'selected' : '' }}>Devuelto (Ok)</option>
                            <option value="devuelto_tarde" {{ request('estado_prestamo') == 'devuelto_tarde' ? 'selected' : '' }}>Devuelto Tarde</option>
                            <option value="perdido" {{ request('estado_prestamo') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark w-100 fw-medium shadow-sm">Filtrar</button>
                        @if(request()->anyFilled(['buscar_lector', 'estado_prestamo']))
                        <a href="{{ route('libros.show', $libro->id) }}" class="btn btn-sm btn-light border text-muted" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla de Préstamos Mejorada --}}
            @if($prestamos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                        <tr>
                            <th class="border-0 rounded-start-3 py-3">Lector</th>
                            <th class="border-0 py-3 text-center">Salida</th>
                            <th class="border-0 py-3 text-center">Devolución</th>
                            <th class="border-0 py-3 text-center">Estado</th>
                            <th class="border-0 rounded-end-3 py-3 text-end">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($prestamos as $prestamo)
                        <tr>
                            <td class="py-3">
                                <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $prestamo->user->name ?? 'Usuario borrado' }}</div>
                            </td>

                            <td class="py-3 text-center text-muted small font-monospace">
                                <i class="bi bi-calendar-minus me-1"></i>{{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y') }}
                            </td>

                            {{-- LA FECHA CLAVE DE DEVOLUCIÓN --}}
                            <td class="py-3 text-center font-monospace small">
                                @if($prestamo->estado === 'activo')
                                <span class="text-warning-emphasis bg-warning-subtle px-2 py-1 rounded">
                                    <i class="bi bi-hourglass-split me-1"></i>Pendiente
                                </span>
                                @elseif($prestamo->fecha_devolucion)
                                <span class="text-muted">
                                    <i class="bi bi-calendar-check me-1 text-success"></i>{{ \Carbon\Carbon::parse($prestamo->fecha_devolucion)->format('d/m/Y') }}
                                </span>
                                @else
                                <span class="text-muted">---</span>
                                @endif
                            </td>

                            <td class="py-3 text-center">
                                @if($prestamo->estado === 'activo')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">En Posesión</span>
                                @elseif($prestamo->estado === 'devuelto')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Devuelto</span>
                                @elseif($prestamo->estado === 'devuelto_tarde')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Devuelto tarde</span>
                                @elseif($prestamo->estado === 'perdido')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Perdido</span>
                                @endif
                            </td>

                            <td class="text-end py-3">
                                <a href="{{ route('prestamos.show', $prestamo->id) }}" class="btn btn-sm btn-light border text-secondary shadow-sm" title="Ver recibo de préstamo">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                {{ $prestamos->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

            @else
            <div class="text-center py-5 text-muted bg-white rounded-3 border border-dashed my-2">
                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-0 fw-medium">No hay registros con estos filtros.</p>
            </div>
            @endif
        </div>
    </div>

</div>
</div>
@endsection