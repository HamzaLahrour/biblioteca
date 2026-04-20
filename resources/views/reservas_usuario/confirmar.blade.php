@extends('layouts.app')
@section('title', 'Confirmar Reserva | LibreLah')

@section('content')
<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 text-center">

            <div class="bg-success text-white rounded-circle d-inline-flex justify-content-center align-items-center mb-4 shadow-sm" style="width: 80px; height: 80px;">
                <i class="bi bi-check-lg fs-1"></i>
            </div>

            <h2 class="fw-bold mb-2 text-dark">¡Hemos encontrado un sitio!</h2>
            <p class="text-muted mb-5">Revisa los datos y confirma tu reserva antes de que alguien te quite el puesto.</p>

            <div class="card border border-light shadow-sm rounded-4 mb-5 text-start overflow-hidden">
                <div class="card-body p-4 p-md-5 bg-white">
                    <h6 class="text-uppercase text-muted small fw-bold mb-3" style="letter-spacing: 1px;">Se te asignará:</h6>
                    <h4 class="fw-bold text-dark mb-4 border-bottom pb-3"><i class="bi bi-pin-map-fill text-success me-2"></i>{{ $espacio->nombre }}</h4>

                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-calendar-event text-dark"></i>
                        </div>
                        <span class="fw-medium fs-5 text-dark">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                            <i class="bi bi-clock text-dark"></i>
                        </div>
                        <span class="fw-medium fs-5 text-dark">{{ $hora_inicio }} - {{ $hora_fin }}</span>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO DEFINITIVO (Manda los datos al Store) --}}
            <form action="{{ route('reservas_usuario.store') }}" method="POST">
                @csrf
                <input type="hidden" name="espacio_id" value="{{ $espacio->id }}">
                <input type="hidden" name="fecha" value="{{ $fecha }}">
                <input type="hidden" name="hora_inicio" value="{{ $hora_inicio }}">
                <input type="hidden" name="hora_fin" value="{{ $hora_fin }}">

                <div class="d-flex flex-column flex-sm-row gap-3">
                    <a href="{{ route('reservas_usuario.create', $tipo->id) }}" class="btn btn-outline-dark btn-lg rounded-pill w-100 fw-medium">Cambiar hora</a>
                    <button type="submit" class="btn btn-dark btn-lg rounded-pill w-100 fw-bold shadow-sm">Confirmar Reserva</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection