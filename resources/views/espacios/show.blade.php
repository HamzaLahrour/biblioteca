@extends('layouts.admin')

@section('title', 'Detalles del Espacio')

@section('content')
<style>
    .btn-electric {
        background-color: #1E90FF;
        color: white;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-electric:hover {
        background-color: #0d73d9;
        color: white;
        box-shadow: 0 4px 12px rgba(30, 144, 255, 0.3);
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
        background-color: #1E90FF;
        /* Tu azul */
        border-color: #1E90FF;
        box-shadow: 0 2px 5px rgba(30, 144, 255, 0.2);
    }

    .custom-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: transparent;
        border-color: #e2e8f0;
        opacity: 0.6;
    }

    .custom-pagination .page-link:hover:not(.active):not(.disabled) {
        color: #1E90FF;
        background-color: rgba(30, 144, 255, 0.05);
        border-color: #bfdbfe;
    }
</style>

<div class="pb-5 mb-5">

    <div class="mb-3">
        <a href="{{ route('espacios.index') }}" class="btn bg-white rounded-pill shadow-sm px-3 py-2 d-inline-flex align-items-center fw-bold transition-hover border" style="color: var(--secondary-dark); font-size: 0.85rem;">
            <i class="bi bi-arrow-left-short fs-5 me-1" style="color: #1E90FF;"></i> Volver a Espacios
        </a>
    </div>

    <div class="row g-4 align-items-start">

        <div class="col-lg-4 col-xl-3">

            <div class="sticky-top" style="top: 1.5rem; z-index: 10;">

                <div class="card shadow-sm border-0 rounded-4 position-relative">

                    <div class="position-absolute top-0 end-0 p-3 z-1">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-circle border border-white shadow-sm d-flex align-items-center justify-content-center transition-hover" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 35px; height: 35px; background: rgba(255,255,255,0.9);">
                                <i class="bi bi-three-dots-vertical text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2 text-sm mt-1">
                                <li>
                                    <a class="dropdown-item rounded-2 fw-medium mb-1 d-flex align-items-center" href="{{ route('espacios.edit', $espacio) }}">
                                        <i class="bi bi-pencil-square me-2" style="color: #1E90FF;"></i> Editar espacio
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider opacity-10 my-1">
                                </li>
                                <li>
                                    <form action="{{ route('espacios.destroy', $espacio) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este espacio? Solo es posible si no tiene reservas.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item rounded-2 fw-medium text-danger d-flex align-items-center">
                                            <i class="bi bi-trash3 me-2"></i> Eliminar espacio
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-body p-4 text-center border-bottom border-light">
                        {{-- Icono de la Sala --}}
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border border-white" style="width: 80px; height: 80px; background: rgba(30, 144, 255, 0.08); color: #1E90FF;">
                            <i class="bi bi-door-open-fill fs-1"></i>
                        </div>
                        <h4 class="fw-bold mb-1" style="color: var(--secondary-dark);">{{ $espacio->nombre }}</h4>
                        <p class="small mb-4" style="color: var(--text-muted);">
                            <i class="bi bi-tag-fill me-1 opacity-50"></i> {{ $espacio->tipoEspacio->nombre ?? 'Sin categoría' }}
                        </p>

                        @if($espacio->disponible)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-4 py-2 fs-6 shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> Disponible
                        </span>
                        @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-4 py-2 fs-6 shadow-sm">
                            <i class="bi bi-tools me-1"></i> En mantenimiento
                        </span>
                        @endif
                    </div>

                    <div class="card-body p-4">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 d-flex justify-content-between align-items-center">
                                <span class="small fw-medium" style="color: var(--text-muted);"><i class="bi bi-upc-scan me-2 opacity-50"></i>Código</span>
                                <span class="fw-bold text-dark">{{ $espacio->codigo }}</span>
                            </li>
                            <li class="mb-3 d-flex justify-content-between align-items-center">
                                <span class="small fw-medium" style="color: var(--text-muted);"><i class="bi bi-geo-alt-fill me-2 opacity-50"></i>Ubicación</span>
                                <span class="fw-medium text-end text-dark" style="font-size: 0.9rem;">{{ $espacio->ubicacion }}</span>
                            </li>
                            <li class="mb-0 d-flex justify-content-between align-items-center">
                                <span class="small fw-medium" style="color: var(--text-muted);"><i class="bi bi-people-fill me-2 opacity-50"></i>Aforo Max.</span>
                                <span class="badge bg-light text-dark border fs-6 rounded-3">{{ $espacio->capacidad }} pers.</span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-0">

                <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0" style="color: var(--secondary-dark);">
                        Historial de Reservas
                    </h5>
                    <span class="badge bg-light text-dark border shadow-sm rounded-pill px-3">{{ $reservas->total() }} resultados</span>
                </div>

                <div class="px-4 mb-3 border-bottom border-light pb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 overflow-auto pb-2">
                        <span class="small fw-bold me-2" style="color: var(--secondary-dark);"><i class="bi bi-funnel-fill me-1" style="color: #1E90FF;"></i> Filtros:</span>
                        <a href="{{ route('espacios.show', ['espacio' => $espacio->id, 'fecha_inicio' => today()->format('Y-m-d'), 'fecha_fin' => today()->format('Y-m-d')]) }}" class="btn btn-sm rounded-pill px-3 fw-medium shadow-sm {{ request('fecha_inicio') == today()->format('Y-m-d') && request('fecha_fin') == today()->format('Y-m-d') ? 'btn-electric' : 'bg-white border text-muted' }}">Hoy</a>
                        <a href="{{ route('espacios.show', ['espacio' => $espacio->id, 'fecha_inicio' => today()->format('Y-m-d'), 'fecha_fin' => today()->addDays(7)->format('Y-m-d')]) }}" class="btn btn-sm rounded-pill px-3 fw-medium shadow-sm {{ request('fecha_fin') == today()->addDays(7)->format('Y-m-d') ? 'btn-electric' : 'bg-white border text-muted' }}">Próximos 7 días</a>

                        @if(request()->hasAny(['fecha_inicio', 'fecha_fin', 'estado']))
                        <div class="vr mx-1 opacity-25"></div>
                        <a href="{{ route('espacios.show', $espacio) }}" class="btn btn-sm text-danger rounded-pill px-3 fw-medium custom-link-hover"><i class="bi bi-x-circle me-1"></i>Limpiar</a>
                        @endif
                    </div>

                    <div class="rounded-4 p-3 border shadow-sm" style="background-color: var(--bg-light);">
                        <form action="{{ route('espacios.show', $espacio) }}" method="GET" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1" style="color: var(--text-muted);">Desde fecha</label>
                                <input type="date" name="fecha_inicio" class="form-control form-control-sm shadow-none border-0 rounded-3" value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1" style="color: var(--text-muted);">Hasta fecha</label>
                                <input type="date" name="fecha_fin" class="form-control form-control-sm shadow-none border-0 rounded-3" value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold mb-1" style="color: var(--text-muted);">Estado</label>
                                <select name="estado" class="form-select form-select-sm shadow-none border-0 rounded-3 text-muted">
                                    <option value="">Todos los estados</option>
                                    <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                                    <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Canceladas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm w-100 rounded-3 shadow-sm fw-bold btn-electric py-1">
                                    <i class="bi bi-search me-1"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($reservas->count() > 0)
                    <div class="table-responsive px-4">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="small" style="color: var(--text-muted); font-size: 0.75rem;">
                                <tr>
                                    <th class="border-bottom pb-3 fw-bold text-uppercase">Fecha y Hora</th>
                                    <th class="border-bottom pb-3 fw-bold text-uppercase">Usuario</th>
                                    <th class="border-bottom pb-3 fw-bold text-uppercase text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @foreach($reservas as $reserva)
                                <tr>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark"><i class="bi bi-calendar2 me-2 opacity-50"></i>{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</div>
                                        <div class="small fw-medium mt-1" style="color: var(--text-muted);">
                                            <i class="bi bi-clock me-2 opacity-50 text-primary"></i>{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <x-user-avatar :user="$reserva->user" size="36px" fontSize="13px" class="me-3" />
                                            <div>
                                                <span class="d-block fw-bold text-dark">{{ $reserva->user->name ?? 'Usuario Desconocido' }}</span>
                                                <span class="d-block" style="color: var(--text-muted); font-size: 0.75rem;">{{ $reserva->user->email ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($reserva->estado === 'activa')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 shadow-sm">Activa</span>
                                        @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 shadow-sm">Cancelada</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 pb-4 pt-3">
                        <div class="mt-3 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 custom-pagination">
                            <div class="text-muted small bg-light px-3 py-2 rounded-pill border shadow-sm" style="border-color: #f1f5f9 !important;">
                                Mostrando del <span class="fw-bold text-dark">{{ $reservas->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $reservas->lastItem() ?? 0 }}</span> de <span class="fw-bold" style="color: #1E90FF;">{{ $reservas->total() ?? 0 }}</span> resultados
                            </div>

                            <div class="pagination-wrapper">
                                {{ $reservas->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                    @else
                    <div class="text-center py-5">
                        <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3 bg-light shadow-sm" style="width: 70px; height: 70px;">
                            <i class="bi bi-calendar-x fs-2" style="color: #1E90FF; opacity: 0.7;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Sin reservas encontradas</h6>
                        <p class="small mb-0" style="color: var(--text-muted);">Modifica los filtros o el rango de fechas para ver otros resultados.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection