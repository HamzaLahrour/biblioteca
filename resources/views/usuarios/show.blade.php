@extends('layouts.admin')

@section('title', 'Perfil de Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

        <div class="mb-3">
            <a href="{{ route('usuarios.index') }}" class="text-decoration-none text-muted fw-medium">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold text-dark mb-0">
                    <i class="bi bi-person-vcard me-2 text-primary"></i>Ficha del Usuario
                </h4>
                <span class="badge bg-secondary rounded-pill px-3 py-2 shadow-sm">
                    ID: {{ substr($usuario->id, 0, 8) }}...
                </span>
            </div>

            <div class="card-body p-4">

                <div class="row align-items-center mb-5">
                    <div class="col-md-4 text-center border-end">
                        <div class="mb-3">
                            @if($usuario->rol === 'admin')
                            <i class="bi bi-person-badge-fill text-primary" style="font-size: 5rem;"></i>
                            @else
                            <i class="bi bi-person-circle text-secondary" style="font-size: 5rem;"></i>
                            @endif
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ $usuario->name }}</h4>

                        <div class="mt-2">
                            @if($usuario->rol === 'admin')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-shield-lock-fill me-1"></i> Administrador
                            </span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-person-fill me-1"></i> Usuario Normal
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-8 ps-md-5 mt-4 mt-md-0">
                        <h5 class="fw-bold text-secondary border-bottom pb-2 mb-3">Datos Personales</h5>

                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted fw-bold"><i class="bi bi-envelope-at me-2"></i>Email:</div>
                            <div class="col-sm-8 text-dark">{{ $usuario->email }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted fw-bold"><i class="bi bi-card-text me-2"></i>DNI/NIE:</div>
                            <div class="col-sm-8">
                                <span class="font-monospace text-dark bg-light px-2 py-1 rounded border">{{ $usuario->dni }}</span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted fw-bold"><i class="bi bi-telephone me-2"></i>Teléfono:</div>
                            <div class="col-sm-8 text-dark">{{ $usuario->telefono ?: 'No registrado' }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted fw-bold"><i class="bi bi-calendar-event me-2"></i>Edad:</div>
                            <div class="col-sm-8 text-dark">
                                {{ $usuario->edad ? $usuario->edad . ' años' : 'Desconocida' }}
                                <span class="text-muted small">({{ $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('d/m/Y') : '---' }})</span>
                            </div>
                        </div>

                        <div class="row mt-3 pt-2 border-top">
                            <div class="col-sm-4 text-muted fw-bold"><i class="bi bi-clock-history me-2"></i>Registro:</div>
                            <div class="col-sm-8 text-muted small">{{ $usuario->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-activity me-2"></i>Actividad en la Biblioteca</h5>
                <div class="row g-3 mb-4">

                    @php
                    $totalPrestamos = $usuario->prestamos->count();
                    $totalReservas = $usuario->reservas->count();
                    $totalSanciones = $usuario->sanciones->count();
                    $tieneActividad = $totalPrestamos > 0 || $totalReservas > 0 || $totalSanciones > 0;
                    @endphp

                    <div class="col-md-4">
                        <div class="card border-info bg-info bg-opacity-10 shadow-sm h-100">
                            <div class="card-body text-center">
                                <h6 class="text-info-emphasis fw-bold">Préstamos</h6>
                                <h3 class="mb-0 text-info">{{ $totalPrestamos }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-success bg-success bg-opacity-10 shadow-sm h-100">
                            <div class="card-body text-center">
                                <h6 class="text-success-emphasis fw-bold">Reservas</h6>
                                <h3 class="mb-0 text-success">{{ $totalReservas }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-danger bg-danger bg-opacity-10 shadow-sm h-100">
                            <div class="card-body text-center">
                                <h6 class="text-danger-emphasis fw-bold">Sanciones</h6>
                                <h3 class="mb-0 text-danger">{{ $totalSanciones }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="text-muted opacity-25 mt-4">

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Editar Perfil
                    </a>

                    <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger px-4"
                            {{ auth()->id() === $usuario->id ? 'disabled title="No puedes borrarte a ti mismo"' : '' }}
                            {{ $tieneActividad ? 'disabled title="Tiene préstamos, reservas o sanciones activas"' : '' }}>
                            <i class="bi bi-trash-fill me-1"></i> Eliminar
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection