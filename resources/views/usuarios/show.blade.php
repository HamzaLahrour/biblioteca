@extends('layouts.admin')

@section('title', 'Perfil de Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-10"> <!-- Amplié un poco el contenedor para las tablas -->

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

            </div>

            <div class="card-body p-4">

                <!-- DATOS DEL USUARIO -->
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

                @php
                $totalPrestamos = $usuario->prestamos->count();
                $totalReservas = $usuario->reservas->count();
                $totalSanciones = $usuario->sanciones->count();
                $tieneActividad = $totalPrestamos > 0 || $totalReservas > 0 || $totalSanciones > 0;
                @endphp

                <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-activity me-2"></i>Detalle de Actividad en la Biblioteca</h5>

                <!-- PESTAÑAS DE ACTIVIDAD -->
                <ul class="nav nav-tabs mb-4" id="actividadTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-info-emphasis" id="prestamos-tab" data-bs-toggle="tab" data-bs-target="#prestamos-pane" type="button" role="tab">
                            <i class="bi bi-book"></i> Préstamos
                            <span class="badge bg-info text-dark ms-1">{{ $totalPrestamos }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-success-emphasis" id="reservas-tab" data-bs-toggle="tab" data-bs-target="#reservas-pane" type="button" role="tab">
                            <i class="bi bi-calendar-check"></i> Reservas
                            <span class="badge bg-success ms-1">{{ $totalReservas }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-danger-emphasis" id="sanciones-tab" data-bs-toggle="tab" data-bs-target="#sanciones-pane" type="button" role="tab">
                            <i class="bi bi-exclamation-triangle"></i> Sanciones
                            <span class="badge bg-danger ms-1">{{ $totalSanciones }}</span>
                        </button>
                    </li>
                </ul>

                <!-- CONTENIDO DE LAS PESTAÑAS -->
                <div class="tab-content" id="actividadTabsContent">

                    <!-- TABLA DE PRÉSTAMOS -->
                    <div class="tab-pane fade show active" id="prestamos-pane" role="tabpanel" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th>Libro/Elemento</th>
                                        <th>Fecha Préstamo</th>
                                        <th>Fecha Devolución</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usuario->prestamos as $prestamo)
                                    <tr>
                                        <!-- Adapta $prestamo->libro->titulo según tu base de datos -->
                                        <td class="fw-medium">{{ $prestamo->libro->titulo ?? 'Recurso desconocido' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($prestamo->fecha_devolucion)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($prestamo->estado === 'activo')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                Activo
                                            </span>
                                            @elseif($prestamo->estado === 'devuelto')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                Devuelto
                                            </span>

                                            @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                Perdido
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Este usuario no tiene préstamos registrados.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLA DE RESERVAS -->
                    <div class="tab-pane fade" id="reservas-pane" role="tabpanel" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light">
                                    <tr>
                                        <th>Espacio / Recurso</th>
                                        <th>Fecha</th>
                                        <th>Hora Inicio</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usuario->reservas as $reserva)
                                    <tr>
                                        <!-- Adapta los campos de reserva según tu DB -->
                                        <td class="fw-medium">{{ $reserva->espacio->nombre ?? 'Espacio' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $reserva->estado ?? 'Pendiente' }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Este usuario no ha realizado reservas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TABLA DE SANCIONES -->
                    <div class="tab-pane fade" id="sanciones-pane" role="tabpanel" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border">
                                <thead class="table-light">
                                    <tr>

                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usuario->sanciones as $sancion)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($sancion->fecha_inicio)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sancion->fecha_fin)->format('d/m/Y') }}</td>
                                        <td>
                                            @if(now()->lessThanOrEqualTo($sancion->fecha_fin))
                                            <span class="badge bg-danger">Activa</span>
                                            @else
                                            <span class="badge bg-secondary">Cumplida</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">El usuario no tiene un historial de sanciones.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <hr class="text-muted opacity-25 mt-4">

                <!-- BOTONES DE ACCIÓN -->
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