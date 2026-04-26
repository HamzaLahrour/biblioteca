@extends('layouts.app')

@section('title', 'Historial de Reservas | LibreLah')

@section('content')
<div class="container py-5 mb-5">

    {{-- CABECERA CON BOTÓN DE VOLVER --}}
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <a href="{{ route('perfil.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block small fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Volver a Mi Espacio
            </a>
            <h2 class="fw-bold mb-0" style="color: var(--text-main); letter-spacing: -0.5px;">
                Historial de <span class="text-gradient" style="background: linear-gradient(135deg, #1E90FF 0%, #64B5F6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Reservas</span>
            </h2>
        </div>
    </div>

    {{-- TARJETA PRINCIPAL BLANCA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($reservas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #F8F9FA;">
                        <tr>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0">Fecha</th>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0">Espacio</th>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0">Horario</th>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold" style="color: #0D47A1;">
                                    {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 fw-medium">
                                {{ $reserva->espacio->nombre ?? 'Sala no disponible' }}
                            </td>
                            <td class="px-4 py-3 text-muted small fw-bold">
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{-- ETIQUETAS DE ESTADO DINÁMICAS --}}
                                @if($reserva->estado === 'cancelada')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Cancelada</span>
                                @elseif($reserva->estado === 'finalizada')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Finalizada</span>
                                @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Activa</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación (Si hay más de 10) --}}
            <div class="px-4 py-3 border-top">
                {{ $reservas->links('pagination::bootstrap-5') }}
            </div>
            @else
            <div class="p-5 text-center text-muted">
                <i class="bi bi-clock-history fs-1 mb-3 d-block opacity-50"></i>
                <h6 class="fw-bold">No hay historial</h6>
                <p class="small mb-0">Aún no has realizado ninguna reserva en la biblioteca.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection