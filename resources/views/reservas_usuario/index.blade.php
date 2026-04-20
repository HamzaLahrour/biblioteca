@extends('layouts.app')
@section('title', 'Tipos de Espacio | LibreLah')

@section('content')
<div class="container py-5 mb-5">

    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark mb-2">¿Qué necesitas hoy?</h2>
        <p class="text-muted">Elige el tipo de espacio y nosotros te buscaremos un sitio libre.</p>
    </div>

    @if($errors->has('error_reserva'))
    <div class="alert alert-danger rounded-4 border-0 bg-danger bg-opacity-10 mb-4 text-center">
        {{ $errors->first('error_reserva') }}
    </div>
    @endif

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($tipos as $tipo)
        <div class="col">
            <div class="card h-100 border border-light shadow-sm rounded-4 overflow-hidden card-hover-effect">
                <div class="card-body p-4 text-center">
                    <div class="bg-dark text-white rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-grid fs-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ $tipo->nombre }}</h5>
                    <p class="text-muted small mb-4">{{ $tipo->descripcion ?? 'Espacio adaptado para tus necesidades.' }}</p>

                    <div class="mb-4 small fw-medium {{ $tipo->espacios_count > 0 ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-layers me-1"></i> {{ $tipo->espacios_count }} espacios en total
                    </div>

                    @if($tipo->espacios_count > 0)
                    <a href="{{ route('reservas_usuario.create', $tipo->id) }}" class="btn btn-dark w-100 rounded-pill fw-medium">Reservar</a>
                    @else
                    <button disabled class="btn btn-light border w-100 rounded-pill">No disponible</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .card-hover-effect {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .08) !important;
    }
</style>
@endsection