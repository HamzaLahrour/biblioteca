@extends('layouts.admin')

@section('title', 'Nueva Reserva')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        {{-- Alertas del Chef (Errores de negocio) --}}
        @error('error_reserva')
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-sign-stop-fill fs-3 me-3"></i>
            <div>
                <strong class="d-block">¡No se pudo completar la reserva!</strong>
                {{ $message }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @enderror

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex align-items-center">
                <a href="{{ route('reservas.index') }}" class="btn btn-sm btn-light rounded-circle shadow-sm me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h5 class="mb-0 fw-bold text-secondary">
                    <i class="bi bi-calendar-plus me-2"></i>Programar Nueva Reserva
                </h5>
            </div>

            <div class="card-body p-4 pt-3">
                <form action="{{ route('reservas.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="espacio_id" class="form-label fw-medium text-dark">Selecciona el Espacio <span class="text-danger">*</span></label>
                        <select name="espacio_id" id="espacio_id" class="form-select border-0 bg-light py-2 px-3 @error('espacio_id') is-invalid @enderror" required>
                            <option value="">-- Elige una sala o puesto --</option>
                            @foreach($espacios as $espacio)
                            <option value="{{ $espacio->id }}" {{ old('espacio_id') == $espacio->id ? 'selected' : '' }}>
                                {{ $espacio->nombre }} (Capacidad: {{ $espacio->capacidad }} pers.)
                            </option>
                            @endforeach
                        </select>
                        @error('espacio_id')
                        <div class="invalid-feedback ps-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="fecha" class="form-label fw-medium text-dark">Fecha de la Reserva <span class="text-danger">*</span></label>
                        <div class="input-group bg-light rounded-3 overflow-hidden border border-0">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-calendar-event text-muted"></i></span>
                            <input type="date" name="fecha" id="fecha" class="form-control bg-transparent border-0 shadow-none @error('fecha') is-invalid @enderror" value="{{ old('fecha', \Carbon\Carbon::today()->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                        </div>
                        @error('fecha')
                        <div class="text-danger small mt-1 ps-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="hora_inicio" class="form-label fw-medium text-dark">Hora de Inicio <span class="text-danger">*</span></label>
                            <div class="input-group bg-light rounded-3 overflow-hidden border border-0">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-clock text-muted"></i></span>
                                <input type="time" name="hora_inicio" id="hora_inicio" class="form-control bg-transparent border-0 shadow-none @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio') }}" required>
                            </div>
                            @error('hora_inicio')
                            <div class="text-danger small mt-1 ps-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="hora_fin" class="form-label fw-medium text-dark">Hora de Fin <span class="text-danger">*</span></label>
                            <div class="input-group bg-light rounded-3 overflow-hidden border border-0">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-clock-fill text-muted"></i></span>
                                <input type="time" name="hora_fin" id="hora_fin" class="form-control bg-transparent border-0 shadow-none @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin') }}" required>
                            </div>
                            @error('hora_fin')
                            <div class="text-danger small mt-1 ps-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if(auth()->user()->rol === 'admin')
                    <div class="mb-4 pt-4 border-top">
                        <label for="user_id" class="form-label fw-bold text-primary">
                            <i class="bi bi-person-badge me-1"></i> Modo Mostrador (Buscador de Usuarios)
                        </label>

                        <select name="user_id" id="buscador-usuarios" class="form-select" placeholder="🔍 Escribe el nombre, apellido o email del alumno..." autocomplete="off">
                            <option value="">-- Es para mí (Reserva interna del personal) --</option>
                            @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('user_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->nombre }} {{ $usuario->apellidos ?? '' }} ({{ $usuario->email }})
                            </option>
                            @endforeach
                        </select>

                        <small class="text-muted ps-2 mt-1 d-block">Empieza a escribir para buscar. Déjalo vacío si es para ti.</small>
                        @error('user_id')
                        <div class="text-danger small mt-1 ps-2">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                        <a href="{{ route('reservas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-medium">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
                            <i class="bi bi-check2-circle me-1"></i> Confirmar Reserva
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Comprobamos si el elemento existe (solo existirá si es admin)
        if (document.getElementById('buscador-usuarios')) {
            new TomSelect("#buscador-usuarios", {
                create: false, // No permite crear usuarios nuevos desde aquí
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                maxOptions: 50, // Muestra máximo 50 resultados a la vez para no petar el navegador
                highlight: true // Resalta en amarillo lo que vas escribiendo
            });
        }
    });
</script>
@endpush
@endsection