@extends('layouts.app')
@section('title', 'Elegir Horario | LibreLah')

@section('content')
<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <a href="{{ route('reservas_usuario.index') }}" class="text-muted text-decoration-none mb-4 d-inline-block transition-link">
                <i class="bi bi-arrow-left me-1"></i>Volver a tipos de espacio
            </a>

            <div class="mb-4">
                <span class="badge bg-dark rounded-pill px-3 py-2 fw-medium mb-2">{{ $tipo->nombre }}</span>
                <h3 class="fw-bold">¿Cuándo lo necesitas?</h3>
            </div>

            @if($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 rounded-4 p-3 mb-4">
                <ul class="mb-0 small text-danger-emphasis">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('reservas_usuario.comprobar', $tipo->id) }}" method="POST" class="bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light">
                @csrf

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Fecha</label>
                    <input type="date" name="fecha" class="form-control form-control-lg bg-light border-0 rounded-3" value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-6">
                        <label class="form-label text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Hora Inicio</label>
                        <input type="time" name="hora_inicio" class="form-control form-control-lg bg-light border-0 rounded-3" value="{{ old('hora_inicio') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Hora Fin</label>
                        <input type="time" name="hora_fin" class="form-control form-control-lg bg-light border-0 rounded-3" value="{{ old('hora_fin') }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark btn-lg w-100 rounded-pill fw-bold" style="padding-top: 14px; padding-bottom: 14px;">
                    <i class="bi bi-search me-2"></i>Buscar hueco libre
                </button>
            </form>

        </div>
    </div>
</div>

<style>
    .transition-link {
        transition: color 0.2s ease;
    }

    .transition-link:hover {
        color: #111 !important;
    }
</style>
@endsection