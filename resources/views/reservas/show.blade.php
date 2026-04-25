@extends('layouts.admin')

@section('title', 'Detalle de Reserva')

@section('content')

<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
    <h5 class="mb-0 fw-bold text-secondary ms-2">
        <i class="bi bi-ticket-detailed me-2"></i>Detalle de Reserva
    </h5>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="max-width: 600px;">

    {{-- Cabecera: datos del usuario --}}
    <div class="card-header bg-light border-0 p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-primary"
                style="width:48px; height:48px; font-size:16px; flex-shrink:0;">
                {{ strtoupper(substr($reserva->user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $reserva->user->name ?? 'U')[1] ?? '', 0, 1)) }}
            </div>
            <div>
                <p class="mb-0 fw-bold text-dark">{{ $reserva->user->name ?? 'Usuario eliminado' }}</p>
                <p class="mb-0 text-muted" style="font-size:13px;">
                    {{ $reserva->user->email ?? '—' }} ·
                    <span class="text-capitalize">{{ $reserva->user->rol ?? '—' }}</span>
                </p>
            </div>
            <div class="ms-auto">
                @if($reserva->estado === 'activa')
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">Activa</span>
                @elseif($reserva->estado === 'finalizada')
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">Finalizada</span>
                @else
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2">Cancelada</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Cuerpo: datos de la reserva --}}
    <div class="card-body p-0">
        <table class="table table-borderless mb-0" style="font-size:14px;">
            <tbody>
                <tr class="border-bottom">
                    <td class="px-4 py-3 text-muted" style="width:40%">Espacio</td>
                    <td class="px-4 py-3 fw-medium">{{ $reserva->espacio->nombre ?? 'Espacio no encontrado' }}</td>
                </tr>
                <tr class="border-bottom">
                    <td class="px-4 py-3 text-muted">Fecha</td>
                    <td class="px-4 py-3 fw-medium">{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</td>
                </tr>
                <tr class="border-bottom">
                    <td class="px-4 py-3 text-muted">Horario</td>
                    <td class="px-4 py-3 fw-medium">
                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} —
                        {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                    </td>
                </tr>
                <tr class="border-bottom">
                    <td class="px-4 py-3 text-muted">Duración</td>
                    <td class="px-4 py-3 fw-medium">
                        {{ \Carbon\Carbon::parse($reserva->hora_inicio)->diffInMinutes(\Carbon\Carbon::parse($reserva->hora_fin)) }} minutos
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-3 text-muted">Reservada el</td>
                    <td class="px-4 py-3 fw-medium">{{ $reserva->created_at->format('d/m/Y \a \l\a\s H:i') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Footer: ID --}}
    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center px-4 py-3">
        <span class="text-muted" style="font-size:11px; font-family: monospace;">ID: {{ $reserva->id }}</span>
        @if($reserva->estado === 'activa' && !\Carbon\Carbon::parse($reserva->fecha_reserva . ' ' . $reserva->hora_inicio)->isPast())
        <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST"
            onsubmit="return confirm('¿Cancelar esta reserva?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                <i class="bi bi-x-circle me-1"></i>Cancelar reserva
            </button>
        </form>
        @endif
    </div>
</div>

@endsection