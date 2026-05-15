@extends('layouts.admin')

@section('title', 'Gestión de Reservas')

@section('content')

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('reservas.index') }}" method="GET">
            <div class="row g-3">

                {{-- Filtro: Usuario --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Buscar Alumno</label>
                    <input type="text" name="buscar_usuario" class="form-control" placeholder="Nombre o email..." value="{{ request('buscar_usuario') }}">
                </div>

                {{-- Filtro: Tipo de Espacio --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tipo de Espacio</label>
                    <select name="tipo_espacio_id" class="form-select">
                        <option value="">Todas las zonas</option>
                        @foreach($tipos_espacios as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo_espacio_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro: Estado --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                        <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                {{-- Filtro: Rango de Fechas --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>

                {{-- Botones de Acción --}}
                <div class="col-md-1 d-flex align-items-end">
                    <div class="d-grid w-100 gap-2">
                        <button type="submit" class="btn btn-primary" title="Filtrar resultados">Filtrar</button>

                        @if(request()->anyFilled(['buscar_usuario', 'tipo_espacio_id', 'estado', 'fecha_inicio', 'fecha_fin']))
                        <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-calendar-check me-2"></i>Listado de Reservas
        </h5>
        <a href="{{ route('reservas.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Nueva Reserva
        </a>
    </div>

    <div class="card-body p-4">
        @if($reservas->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3">Fecha</th>
                        <th scope="col" class="border-0 py-3">Espacio</th>
                        <th scope="col" class="border-0 py-3">Horario</th>
                        <th scope="col" class="border-0 py-3 text-center">Estado</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($reservas as $reserva)
                    <tr>
                        <td class="fw-medium text-dark py-3">
                            <i class="bi bi-calendar3 text-muted me-1"></i>
                            {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}
                        </td>
                        <td class="py-3">
                            {{ $reserva->espacio->nombre ?? 'Espacio no encontrado' }}
                        </td>
                        <td class="py-3 text-muted">
                            <i class="bi bi-clock me-1"></i>
                            {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                        </td>
                        <td class="py-3 text-center">
                            @if($reserva->estado === 'activa')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                Activa
                            </span>
                            @elseif($reserva->estado === 'finalizada')
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                Finalizada
                            </span>
                            @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2">
                                Cancelada
                            </span>
                            @endif
                        </td>
                        <td class="text-end py-3">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('reservas.show', $reserva->id) }}"
                                    class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                    <i class="bi bi-eye-fill me-1"></i> Ver
                                </a>

                                @if($reserva->estado === 'activa' && !\Carbon\Carbon::parse($reserva->fecha_reserva . ' ' . $reserva->hora_fin)->isPast())
                                <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta reserva? Liberarás el hueco.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar Reserva">
                                        <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-5 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 custom-pagination">
            <div class="text-muted small bg-light px-3 py-2 rounded-pill border border-neutral-100 shadow-sm-inner">
                Mostrando del <span class="fw-bold text-dark">{{ $reservas->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $reservas->lastItem() ?? 0 }}</span> de <span class="fw-bold text-primary">{{ $reservas->total() ?? 0 }}</span> resultados
            </div>

            <div class="pagination-wrapper">
                {{ $reservas->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>

        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-3 text-secondary"></i>
            <h5 class="fw-bold text-dark">No hay ninguna reserva registrada.</h5>
            <p>Aún no has realizado o recibido ninguna reserva en los espacios de la biblioteca.</p>
            <a href="{{ route('reservas.create') }}" class="btn btn-outline-primary rounded-pill px-4 mt-2">
                Hacer mi primera reserva
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    .shadow-sm-inner {
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

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