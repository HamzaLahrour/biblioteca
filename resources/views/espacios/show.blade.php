@extends('layouts.admin')

@section('title', 'Detalles del Espacio')

@section('content')
<div class="mb-4">
    <a href="{{ route('espacios.index') }}" class="btn btn-sm btn-light rounded-pill shadow-sm border" style="color: var(--text-muted); font-weight: 500;">
        <i class="bi bi-arrow-left me-1"></i> Volver a Espacios
    </a>
</div>

<div class="row g-4">
    {{-- =========================================================================
         COLUMNA IZQUIERDA: PERFIL DEL ESPACIO (Sticky)
         ========================================================================= --}}
    <div class="col-lg-4 col-xl-3">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 text-center border-bottom">
                {{-- Icono de la Sala --}}
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background-color: rgba(30, 144, 255, 0.1); color: var(--primary);">
                    <i class="bi bi-door-open-fill fs-1"></i>
                </div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-dark);">{{ $espacio->nombre }}</h4>
                <p class="small mb-3" style="color: var(--text-muted);">
                    <i class="bi bi-tag-fill me-1"></i> {{ $espacio->tipoEspacio->nombre ?? 'Sin categoría' }}
                </p>

                @if($espacio->disponible)
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 w-100 fs-6">
                    <i class="bi bi-check-circle-fill me-1"></i> Disponible
                </span>
                @else
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-2 w-100 fs-6">
                    <i class="bi bi-tools me-1"></i> En mantenimiento
                </span>
                @endif
            </div>

            <div class="card-body p-4">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex justify-content-between align-items-center">
                        <span style="color: var(--text-muted);"><i class="bi bi-upc-scan me-2"></i>Código</span>
                        <span class="fw-bold text-dark">{{ $espacio->codigo }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between align-items-center">
                        <span style="color: var(--text-muted);"><i class="bi bi-geo-alt-fill me-2"></i>Ubicación</span>
                        <span class="fw-medium text-end" style="font-size: 0.9rem;">{{ $espacio->ubicacion }}</span>
                    </li>
                    <li class="mb-0 d-flex justify-content-between align-items-center">
                        <span style="color: var(--text-muted);"><i class="bi bi-people-fill me-2"></i>Aforo Max.</span>
                        <span class="badge bg-light text-dark border fs-6">{{ $espacio->capacidad }} pers.</span>
                    </li>
                </ul>
            </div>

            <div class="card-footer bg-transparent border-top-0 p-4 pt-0 d-flex gap-2">
                <a href="{{ route('espacios.edit', $espacio) }}" class="btn btn-primary rounded-pill w-100 shadow-sm fw-medium">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
                <form action="{{ route('espacios.destroy', $espacio) }}" method="POST" class="w-100" onsubmit="return confirm('¿Eliminar este espacio? Solo es posible si no tiene reservas.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger rounded-pill w-100 fw-medium">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         COLUMNA DERECHA: DASHBOARD DE RESERVAS Y FILTROS
         ========================================================================= --}}
    <div class="col-lg-8 col-xl-9">

        {{-- 1. TARJETAS DE MÉTRICAS RÁPIDAS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background-color: rgba(30, 144, 255, 0.05);">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="bg-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 48px; height: 48px; color: var(--primary);">
                            <i class="bi bi-calendar2-check fs-4"></i>
                        </div>
                        <div>
                            <p class="small fw-bold mb-0 text-uppercase" style="color: var(--text-muted);">Ocupación Hoy</p>
                            <h4 class="fw-bold mb-0" style="color: var(--primary);">{{ $metricas['activas_hoy'] }} <span class="fs-6 fw-normal" style="opacity: 0.7;">reservas</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background-color: var(--bg-light);">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="bg-white text-dark rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-bar-chart-fill fs-4"></i>
                        </div>
                        <div>
                            <p class="small fw-bold mb-0 text-uppercase" style="color: var(--text-muted);">Total Histórico</p>
                            <h4 class="fw-bold text-dark mb-0">{{ $metricas['historico'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-danger bg-opacity-10">
                    <div class="card-body p-3 d-flex align-items-center">
                        <div class="bg-white text-danger rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="bi bi-x-octagon fs-4"></i>
                        </div>
                        <div>
                            <p class="text-danger opacity-75 small fw-bold mb-0 text-uppercase">Canceladas</p>
                            <h4 class="fw-bold text-danger mb-0">{{ $metricas['canceladas'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. PANEL DE FILTROS Y TABLA --}}
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color: var(--secondary-dark);">
                    <i class="bi bi-calendar-week me-2"></i>Historial de Reservas
                </h5>
                <span class="badge bg-light text-dark border shadow-sm">{{ $reservas->total() }} resultados</span>
            </div>

            <div class="px-4 mb-3">
                {{-- Botones de Filtros Rápidos --}}
                <div class="d-flex gap-2 mb-3 overflow-auto pb-2">
                    <span class="small fw-bold pt-1 me-2" style="color: var(--text-muted);"><i class="bi bi-lightning-charge-fill text-warning"></i> Rápidos:</span>
                    <a href="{{ route('espacios.show', ['espacio' => $espacio->id, 'fecha_inicio' => today()->format('Y-m-d'), 'fecha_fin' => today()->format('Y-m-d')]) }}" class="btn btn-sm rounded-pill px-3 {{ request('fecha_inicio') == today()->format('Y-m-d') && request('fecha_fin') == today()->format('Y-m-d') ? 'btn-primary text-white' : 'btn-outline-secondary' }}">Hoy</a>
                    <a href="{{ route('espacios.show', ['espacio' => $espacio->id, 'fecha_inicio' => today()->format('Y-m-d'), 'fecha_fin' => today()->addDays(7)->format('Y-m-d')]) }}" class="btn btn-sm rounded-pill px-3 {{ request('fecha_fin') == today()->addDays(7)->format('Y-m-d') ? 'btn-primary text-white' : 'btn-outline-secondary' }}">Próximos 7 días</a>
                    @if(request()->hasAny(['fecha_inicio', 'fecha_fin', 'estado']))
                    <a href="{{ route('espacios.show', $espacio) }}" class="btn btn-sm btn-light text-danger border rounded-pill px-3"><i class="bi bi-x-circle me-1"></i>Limpiar Filtros</a>
                    @endif
                </div>

                {{-- Buscador Avanzado --}}
                <div class="rounded-4 p-3 border" style="background-color: var(--bg-light);">
                    <form action="{{ route('espacios.show', $espacio) }}" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1" style="color: var(--text-muted);">Desde fecha</label>
                            <input type="date" name="fecha_inicio" class="form-control form-control-sm shadow-none border-0" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1" style="color: var(--text-muted);">Hasta fecha</label>
                            <input type="date" name="fecha_fin" class="form-control form-control-sm shadow-none border-0" value="{{ request('fecha_fin') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1" style="color: var(--text-muted);">Estado</label>
                            <select name="estado" class="form-select form-select-sm shadow-none border-0">
                                <option value="">Todos</option>
                                <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>🟢 Activas</option>
                                <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>🔴 Canceladas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-sm btn-secondary w-100 rounded-3 shadow-sm fw-medium">
                                <i class="bi bi-search me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 3. TABLA DE RESULTADOS --}}
            <div class="card-body p-0 px-4 pb-4">
                @if($reservas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="small text-uppercase" style="color: var(--text-muted); letter-spacing: 0.5px;">
                            <tr>
                                <th class="border-bottom-0 pb-3">Fecha y Hora</th>
                                <th class="border-bottom-0 pb-3">Usuario</th>
                                <th class="border-bottom-0 pb-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($reservas as $reserva)
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</div>
                                    <div class="small" style="color: var(--text-muted);">
                                        <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($reserva->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-medium text-dark">{{ $reserva->user->name ?? 'Usuario Desconocido' }}</span>
                                            <span class="d-block" style="color: var(--text-muted); font-size: 0.75rem;">{{ $reserva->user->email ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    @if($reserva->estado === 'activa')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1">Activa</span>
                                    @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1">Cancelada</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    {{-- Conservamos los filtros en la paginación --}}
                    {{ $reservas->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
                @else
                <div class="text-center py-5">
                    <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px; background-color: var(--bg-light);">
                        <i class="bi bi-calendar-x fs-1" style="color: var(--text-muted); opacity: 0.5;"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Sin reservas encontradas</h6>
                    <p class="small mb-0" style="color: var(--text-muted);">Modifica los filtros o el rango de fechas para ver otros resultados.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection