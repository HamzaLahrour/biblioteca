@extends('layouts.admin')

@section('title', 'Panel de Control')

@section('content')
<div class="container-fluid py-3">

    {{-- 1. TARJETAS MÉTRICAS --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('libros.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Catálogo Total</h6>
                            <h2 class="fw-bolder mb-1 text-dark">{{ $totalLibros }}</h2>
                            <p class="mb-0 text-muted" style="font-size: 0.78rem;">títulos en catálogo</p>
                        </div>
                        <i class="bi bi-book fs-2 text-muted opacity-50"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('usuarios.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Lectores</h6>
                            <h2 class="fw-bolder mb-1 text-dark">{{ $totalUsuarios }}</h2>
                            <p class="mb-0 text-muted" style="font-size: 0.78rem;">{{ $lectoresConPrestamo }} con préstamo activo</p>
                        </div>
                        <i class="bi bi-people fs-2 text-muted opacity-50"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('prestamos.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Préstamos Activos</h6>
                            <h2 class="fw-bolder mb-1" style="color: var(--primary);">{{ $prestamosActivos }}</h2>
                            @if($prestamosVencidos > 0)
                            <p class="mb-0 text-danger" style="font-size: 0.78rem;"><i class="bi bi-exclamation-circle me-1"></i>{{ $prestamosVencidos }} vencido(s)</p>
                            @elseif($prestamosProximos > 0)
                            <p class="mb-0 text-warning" style="font-size: 0.78rem;"><i class="bi bi-clock me-1"></i>{{ $prestamosProximos }} vencen pronto</p>
                            @else
                            <p class="mb-0 text-success" style="font-size: 0.78rem;"><i class="bi bi-check-circle me-1"></i>Todos en plazo</p>
                            @endif
                        </div>
                        <i class="bi bi-arrow-left-right fs-2 text-primary opacity-50"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('reservas.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white card-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Reservas (Hoy)</h6>
                            <h2 class="fw-bolder mb-1 text-dark">{{ $reservasHoyCount }}</h2>
                            @if($proximaReserva)
                            <p class="mb-0 text-muted" style="font-size: 0.78rem;">Próxima: {{ \Carbon\Carbon::parse($proximaReserva->hora_inicio)->format('H:i') }}</p>
                            @else
                            <p class="mb-0 text-muted" style="font-size: 0.78rem;">Sin reservas pendientes</p>
                            @endif
                        </div>
                        <i class="bi bi-calendar-check fs-2 text-muted opacity-50"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- 2. FILA CENTRAL --}}
    <div class="row g-4 mb-4">

        {{-- Aforo de Hoy --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-geo-alt me-2 text-primary"></i>Aforo de Hoy</h5>
                    <p class="text-muted small mt-1 mb-0">Salas reservadas para hoy ({{ \Carbon\Carbon::now()->format('d/m/Y') }})</p>
                </div>
                <div class="card-body p-4 pt-2">
                    @if($reservasHoy->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-cup-hot fs-1 opacity-25 mb-2 d-block"></i>
                        No hay reservas para el día de hoy.
                    </div>
                    @else
                    @php $reservasPorEspacio = $reservasHoy->groupBy(fn($r) => $r->espacio->nombre ?? 'Espacio eliminado'); @endphp
                    @foreach($reservasPorEspacio as $nombreEspacio => $slots)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark" style="font-size: 0.875rem;">{{ $nombreEspacio }}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill" style="font-size: 0.7rem;">
                                {{ $slots->count() }} {{ $slots->count() === 1 ? 'reserva' : 'reservas' }}
                            </span>
                        </div>
                        @foreach($slots as $reserva)
                        <a href="{{ route('reservas.show', $reserva) }}"
                            class="d-flex justify-content-between align-items-center py-2 px-2 border-start border-2 border-primary mb-1 text-decoration-none row-hover rounded-end">
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>{{ $reserva->user->name ?? 'Usuario desconocido' }}
                            </small>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">
                                {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} – {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Atención Requerida --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-exclamation-circle me-2 text-danger"></i>Atención Requerida</h5>
                    <p class="text-muted small mt-1 mb-0">Préstamos vencidos o a punto de caducar</p>
                </div>
                <div class="card-body p-4 pt-2">
                    @if($prestamosUrgentes->isEmpty())
                    <div class="d-flex align-items-center gap-3 text-muted py-3">
                        <i class="bi bi-check-circle fs-3 text-success opacity-75"></i>
                        <span class="small">Todo al día. No hay devoluciones urgentes.</span>
                    </div>
                    @else
                    @foreach($prestamosUrgentes as $prestamo)
                    @php $vencido = \Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista)->isPast(); @endphp
                    <a href="{{ route('prestamos.show', $prestamo) }}"
                        class="d-flex justify-content-between align-items-center py-2 px-2 border-bottom text-decoration-none row-hover rounded">
                        <div>
                            <p class="mb-0 fw-bold text-dark text-truncate" style="max-width: 200px; font-size: 0.875rem;">
                                {{ $prestamo->libro->titulo ?? 'Libro eliminado' }}
                            </p>
                            <small class="text-muted">{{ $prestamo->user->name ?? 'Usuario' }}</small>
                        </div>
                        <div class="text-end">
                            <span class="d-block small fw-bold {{ $vencido ? 'text-danger' : 'text-warning' }}">
                                {{ \Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista)->format('d/m/Y') }}
                            </span>
                            <span class="badge {{ $vencido ? 'bg-danger' : 'bg-warning' }} bg-opacity-10 {{ $vencido ? 'text-danger' : 'text-warning' }} rounded-pill" style="font-size: 0.7rem;">
                                {{ $vencido ? 'Vencido' : 'Próximo' }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FILA INFERIOR --}}
    <div class="row g-4">

        {{-- Últimos movimientos --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-activity me-2 text-primary"></i>Últimos movimientos</h5>
                    <p class="text-muted small mt-1 mb-0">Actividad reciente en préstamos</p>
                </div>
                <div class="card-body p-4 pt-2">
                    @if($ultimosMovimientos->isEmpty())
                    <p class="text-muted small">No hay actividad reciente.</p>
                    @else
                    @foreach($ultimosMovimientos as $mov)
                    @php
                    $esDevuelto = $mov->estado === 'devuelto';
                    $esPerdido = $mov->estado === 'perdido';
                    $esVencido = !$esDevuelto && !$esPerdido && \Carbon\Carbon::parse($mov->fecha_devolucion_prevista)->isPast();
                    @endphp
                    <a href="{{ route('prestamos.show', $mov) }}"
                        class="d-flex justify-content-between align-items-center py-2 px-2 border-bottom text-decoration-none row-hover rounded">
                        <div class="d-flex align-items-center gap-3">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                style="width:32px; height:32px; font-size:0.75rem;
                                                 background: {{ $esDevuelto ? '#d1e7dd' : ($esPerdido ? '#f8d7da' : ($esVencido ? '#f8d7da' : '#cfe2ff')) }};
                                                 color: {{ $esDevuelto ? '#0f5132' : ($esPerdido ? '#842029' : ($esVencido ? '#842029' : '#084298')) }};">
                                <i class="bi {{ $esDevuelto ? 'bi-box-arrow-in-left' : ($esPerdido ? 'bi-x-circle' : ($esVencido ? 'bi-exclamation' : 'bi-arrow-right')) }}"></i>
                            </span>
                            <div>
                                <p class="mb-0 fw-bold text-dark" style="font-size: 0.875rem;">

                                    {{ $mov->libro->titulo ?? 'Libro eliminado' }}
                                </p>
                                <small class="text-muted">{{ $mov->user->name ?? 'Usuario' }}</small>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge rounded-pill
                                        {{ $esDevuelto ? 'bg-success bg-opacity-10 text-success' : ($esPerdido ? 'bg-danger bg-opacity-10 text-danger' : ($esVencido ? 'bg-danger bg-opacity-10 text-danger' : 'bg-primary bg-opacity-10 text-primary')) }}"
                                style="font-size: 0.7rem;">
                                {{ $esDevuelto ? 'Devuelto' : ($esPerdido ? 'Perdido' : ($esVencido ? 'Vencido' : 'Activo')) }}
                            </span>
                            <p class="mb-0 text-muted mt-1" style="font-size: 0.72rem;">
                                {{ \Carbon\Carbon::parse($mov->updated_at)->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        {{-- Libros más prestados --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart me-2 text-primary"></i>Más prestados</h5>
                    <p class="text-muted small mt-1 mb-0">Títulos con más préstamos registrados</p>
                </div>
                <div class="card-body p-4 pt-2">
                    @if($librosTop->isEmpty())
                    <p class="text-muted small">No hay datos de préstamos aún.</p>
                    @else
                    @foreach($librosTop as $index => $libro)
                    <a href="{{ route('libros.show', $libro) }}"
                        class="d-flex align-items-center gap-3 py-2 text-decoration-none row-hover rounded px-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span class="text-muted fw-bold flex-shrink-0" style="width: 18px; font-size: 0.8rem;">{{ $index + 1 }}</span>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <p class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.875rem;">{{ $libro->titulo }}</p>
                            <small class="text-muted">{{ $libro->categoria->nombre ?? '—' }}</small>
                        </div>
                        <span class="badge bg-light text-dark border flex-shrink-0" style="font-size: 0.75rem;">
                            {{ $libro->prestamos_count }}×
                        </span>
                    </a>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .card-hover {
        border: 1px solid transparent !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        border-color: var(--primary) !important;
    }

    .row-hover:hover {
        background-color: var(--bs-gray-100);
        cursor: pointer;
    }
</style>
@endsection