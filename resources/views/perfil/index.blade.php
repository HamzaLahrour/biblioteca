@extends('layouts.app')

@section('title', 'Mi Espacio | BiblioTech')

@section('content')
<div class="container py-5 mb-5">

    <div class="row g-4">
        {{-- COLUMNA IZQUIERDA: EL CARNET Y SANCIONES --}}
        <div class="col-lg-4">

            {{-- 🚨 ALERTA DE SANCIÓN (Solo aparece si está castigado) --}}
            @if($sancionActiva)
            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 rounded-4 p-4 mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-exclamation-octagon-fill fs-4 text-danger me-2"></i>
                    <h6 class="fw-bold text-danger mb-0">Cuenta Suspendida</h6>
                </div>
                <p class="small text-danger-emphasis mb-0">
                    Tienes una sanción activa por "{{ $sancionActiva->motivo ?? 'retraso en devoluciones' }}".
                    No podrás realizar nuevos préstamos ni reservas hasta el <strong>{{ \Carbon\Carbon::parse($sancionActiva->fecha_fin)->format('d/m/Y') }}</strong>.
                </p>
            </div>
            @endif

            {{-- 🪪 CARNET DIGITAL --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #111 0%, #333 100%); color: white;">
                <div class="card-body p-4 position-relative">
                    {{-- Marca de agua sutil --}}
                    <i class="bi bi-book-half position-absolute opacity-10" style="font-size: 8rem; right: -20px; bottom: -20px;"></i>

                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="fw-bold text-uppercase" style="letter-spacing: 2px; font-size: 0.75rem; color: #aaa;">Lector Autorizado</div>
                        <i class="bi bi-check-circle-fill {{ $sancionActiva ? 'text-danger' : 'text-success' }} fs-5"></i>
                    </div>

                    <h3 class="fw-bold mb-1">{{ $usuario->name }}</h3>
                    <div class="text-white-50 small mb-4">{{ $usuario->email }}</div>

                    <div class="p-3 rounded-3 mb-2 text-center" style="background-color: rgba(255,255,255,0.1);">
                        <div class="font-monospace fw-bold fs-5" style="letter-spacing: 4px;">{{ $usuario->dni ?? 'SIN-DNI' }}</div>
                    </div>
                    <div class="text-center font-monospace" style="font-size: 0.6rem; color: #888;">ID: {{ str_pad($usuario->id, 8, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: MIS COSAS (Préstamos y Reservas) --}}
        <div class="col-lg-8">

            {{-- 📚 SECCIÓN: MIS PRÉSTAMOS ACTIVOS --}}
            <div class="d-flex justify-content-between align-items-end mb-3">
                <h4 class="fw-bold text-dark mb-0">Mis Lecturas</h4>
                <a href="{{ route('catalogo.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Explorar catálogo</a>
            </div>

            @if($prestamos->count() > 0)
            <div class="row row-cols-1 g-3 mb-5">
                @foreach($prestamos as $prestamo)
                @php
                // Usamos el nombre correcto de la BD para calcular los días
                $vence = \Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista);
                $hoy = \Carbon\Carbon::today();
                $diasRestantes = $hoy->diffInDays($vence, false);
                @endphp

                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 h-100 transition-all">
                        <div class="card-body p-3 d-flex align-items-center">
                            {{-- Portada Miniatura --}}
                            <div class="bg-light rounded-3 overflow-hidden me-3 flex-shrink-0 border" style="width: 60px; height: 85px;">
                                @if($prestamo->libro->portada)
                                <img src="{{ $prestamo->libro->portada }}" alt="Portada" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                <div class="w-100 h-100 d-flex justify-content-center align-items-center text-muted"><i class="bi bi-book"></i></div>
                                @endif
                            </div>

                            {{-- Datos del Libro --}}
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-dark text-truncate" style="max-width: 250px;">{{ $prestamo->libro->titulo }}</h6>
                                <p class="text-muted small mb-2">{{ $prestamo->libro->autor }}</p>

                                {{-- Lógica visual de días restantes --}}
                                @if($diasRestantes < 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle"><i class="bi bi-exclamation-triangle-fill me-1"></i>¡Vencido hace {{ abs($diasRestantes) }} días!</span>
                                    @elseif($diasRestantes == 0)
                                    <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle"><i class="bi bi-clock-history me-1"></i>Devolver hoy</span>
                                    @elseif($diasRestantes <= 2)
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle"><i class="bi bi-clock-history me-1"></i>Devolver en {{ $diasRestantes }} días</span>
                                        @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle"><i class="bi bi-calendar-check me-1"></i>Quedan {{ $diasRestantes }} días</span>
                                        @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white border rounded-4 p-5 text-center mb-5 shadow-sm">
                <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-book text-muted fs-4"></i>
                </div>
                <h6 class="fw-bold text-dark">No tienes libros prestados</h6>
                <p class="text-muted small mb-0">Cuando te lleves un libro de la biblioteca, aparecerá aquí con su fecha de devolución.</p>
            </div>
            @endif

            {{-- 🛋️ SECCIÓN: MIS RESERVAS DE SALAS --}}
            <h4 class="fw-bold text-dark mb-3 mt-4">Próximas Reservas</h4>

            @if($reservas->count() > 0)
            <div class="row row-cols-1 g-3">
                @foreach($reservas as $reserva)
                <div class="col">
                    <div class="card border border-light shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-dark text-white rounded-3 d-flex flex-column justify-content-center align-items-center me-3" style="width: 55px; height: 55px;">
                                    <span class="fs-5 fw-bold line-height-1">{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d') }}</span>
                                    <span class="small text-uppercase" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->isoFormat('MMM') }}</span>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $reserva->espacio->nombre ?? 'Sala' }}</h6>
                                    <div class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Botón para cancelar reserva --}}
                            <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas cancelar esta reserva?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Cancelar</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-white border rounded-4 p-4 text-center shadow-sm">
                <p class="text-muted small mb-0">No tienes ninguna sala reservada para los próximos días.</p>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
    .transition-all {
        transition: all 0.2s ease;
    }

    .card.transition-all:hover {
        transform: translateX(5px);
    }
</style>
@endsection