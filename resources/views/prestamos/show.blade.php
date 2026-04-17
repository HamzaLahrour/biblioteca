@extends('layouts.admin')

@section('title', 'Detalle del Préstamo')

@section('content')
<div class="row justify-content-center mb-5">
    <div class="col-md-10 col-lg-8">

        {{-- BOTÓN VOLVER --}}
        <div class="mb-3">
            <a href="{{ route('prestamos.index') }}" class="text-decoration-none text-muted fw-medium">
                <i class="bi bi-arrow-left me-1"></i> Volver al mostrador
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">

            {{-- CABECERA: ESTADO DEL PRÉSTAMO --}}
            <div class="p-4 text-center {{ 
                $prestamo->estado === 'activo' ? 'bg-primary text-white' : 
                ($prestamo->estado === 'devuelto' ? 'bg-success text-white' : 
                ($prestamo->estado === 'devuelto_tarde' ? 'bg-warning text-dark' : 'bg-danger text-white')) 
            }}">
                <h6 class="text-uppercase fw-bold mb-2" style="letter-spacing: 2px; opacity: 0.8;">
                    Expediente #{{ str_pad($prestamo->id, 6, '0', STR_PAD_LEFT) }}
                </h6>
                <h3 class="fw-bold mb-0">
                    @if($prestamo->estado === 'activo')
                    <i class="bi bi-hourglass-split me-2"></i>En Posesión del Lector
                    @elseif($prestamo->estado === 'devuelto')
                    <i class="bi bi-check-circle-fill me-2"></i>Devuelto Correctamente
                    @elseif($prestamo->estado === 'devuelto_tarde')
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Devuelto con Retraso
                    @elseif($prestamo->estado === 'perdido')
                    <i class="bi bi-x-octagon-fill me-2"></i>Dado por Perdido
                    @endif
                </h3>
            </div>

            <div class="card-body p-4 pt-5">

                {{-- DOS COLUMNAS: QUIÉN Y QUÉ --}}
                <div class="row g-4 mb-5">

                    {{-- DATOS DEL LECTOR --}}
                    <div class="col-md-6 border-end-md">
                        <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 1px;">
                            <i class="bi bi-person-badge me-2 text-secondary"></i>Datos del Lector
                        </h6>
                        <div class="d-flex align-items-center mb-3">
                            @php
                            $nombre = $prestamo->user->name ?? 'Usuario borrado';
                            $iniciales = substr($nombre, 0, 2);
                            @endphp
                            <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3 shadow-sm" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                {{ strtoupper($iniciales) }}
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">{{ $nombre }}</h5>
                                <div class="text-muted">{{ $prestamo->user->email ?? '---' }}</div>
                            </div>
                        </div>
                        <ul class="list-unstyled text-muted small mt-3">
                            <li class="mb-2"><strong>DNI:</strong> <span class="font-monospace ms-1">{{ $prestamo->user->dni ?? 'No registrado' }}</span></li>
                            <li><strong>Teléfono:</strong> <span class="ms-1">{{ $prestamo->user->telefono ?? 'No registrado' }}</span></li>
                        </ul>
                    </div>

                    {{-- DATOS DEL LIBRO --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 1px;">
                            <i class="bi bi-book me-2 text-secondary"></i>Datos del Ejemplar
                        </h6>
                        <div class="d-flex mb-3">
                            @if($prestamo->libro->portada)
                            <img src="{{ $prestamo->libro->portada }}" alt="Portada" class="rounded border shadow-sm me-3" style="width: 50px; height: 75px; object-fit: cover;">
                            @else
                            <div class="bg-light border rounded shadow-sm d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 75px;">
                                <i class="bi bi-book text-muted"></i>
                            </div>
                            @endif
                            <div>
                                <h5 class="fw-bold text-dark mb-1" style="line-height: 1.2;">{{ $prestamo->libro->titulo }}</h5>
                                <div class="text-muted mb-2">{{ $prestamo->libro->autor }}</div>
                                <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle">
                                    {{ $prestamo->libro->categoria->nombre ?? 'Sin Categoría' }}
                                </span>
                            </div>
                        </div>
                        <ul class="list-unstyled text-muted small mt-3">
                            <li><strong>ISBN:</strong> <span class="font-monospace ms-1">{{ $prestamo->libro->isbn ?? '---' }}</span></li>
                        </ul>
                    </div>
                </div>

                {{-- BLOQUE DE FECHAS CLAVE --}}
                <div class="bg-light p-4 rounded-4 border border-secondary border-opacity-10">
                    <h6 class="fw-bold text-muted text-uppercase mb-3 text-center" style="letter-spacing: 1px;">Registro Temporal</h6>

                    <div class="row text-center g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Fecha de Salida</div>
                            <div class="fw-bold fs-5 text-dark font-monospace">
                                {{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="col-sm-4 border-start border-end border-secondary border-opacity-25">
                            <div class="text-muted small mb-1">Vencimiento Previsto</div>
                            <div class="fw-bold fs-5 text-dark font-monospace">
                                {{ \Carbon\Carbon::parse($prestamo->fecha_vencimiento)->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Devolución Real</div>
                            <div class="fw-bold fs-5 font-monospace {{ $prestamo->fecha_devolucion ? 'text-success' : 'text-muted' }}">
                                @if($prestamo->fecha_devolucion)
                                {{ \Carbon\Carbon::parse($prestamo->fecha_devolucion)->format('d/m/Y') }}
                                @else
                                <span class="fst-italic">Pendiente</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection