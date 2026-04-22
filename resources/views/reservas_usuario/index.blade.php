@extends('layouts.app')
@section('title', 'Tipos de Espacio | LibreLah')

@section('content')
<div class="container py-5 mb-5">

    {{-- CABECERA FASE GOLD --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-2" style="color: var(--text-main); letter-spacing: -0.8px;">
            ¿Qué necesitas <span class="text-gradient">hoy?</span>
        </h2>
        <p class="mx-auto mt-3" style="color: var(--text-muted); max-width: 500px; font-size: 1.05rem;">
            Elige el tipo de espacio y nosotros te buscaremos el rincón perfecto.
        </p>
    </div>

    {{-- ALERTA DE ERROR PREMIUM --}}
    @if($errors->has('error_reserva'))
    <div class="row justify-content-center mb-4">
        <div class="col-md-8 col-lg-6">
            <div class="alert custom-alert-danger d-flex align-items-center gap-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">No pudimos procesar la reserva</h6>
                    <p class="mb-0 small">{{ $errors->first('error_reserva') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- CUADRÍCULA DE ESPACIOS --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 justify-content-center g-4 mx-auto" style="max-width: 1200px;">
        @foreach($tipos as $tipo)
        <div class="col">
            <div class="card h-100 space-card border-0">
                <div class="card-body p-4 d-flex flex-column text-center">

                    {{-- Icono Inyectado con ADN --}}
                    <div class="icon-wrapper mx-auto mb-4">
                        <i class="bi bi-grid-1x2"></i>
                    </div>

                    <h5 class="fw-bold mb-2" style="color: var(--secondary-dark);">{{ $tipo->nombre }}</h5>
                    <p class="mb-4" style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
                        {{ $tipo->descripcion ?? 'Espacio adaptado para tus necesidades, perfecto para concentrarte.' }}
                    </p>

                    <div class="mt-auto">
                        {{-- Indicador de disponibilidad estilo Catálogo --}}
                        <div class="status-indicator justify-content-center mb-4">
                            @if($tipo->espacios_count > 0)
                            <span class="status-dot available"></span>
                            <span class="status-text">{{ $tipo->espacios_count }} espacios disponibles</span>
                            @else
                            <span class="status-dot unavailable"></span>
                            <span class="status-text text-muted">Agotado actualmente</span>
                            @endif
                        </div>

                        {{-- Botones Branded --}}
                        @if($tipo->espacios_count > 0)
                        <a href="{{ route('reservas_usuario.create', $tipo->id) }}" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm btn-action">
                            Reservar ahora
                        </a>
                        @else
                        <button disabled class="btn btn-light border-0 w-100 rounded-pill py-2 fw-semibold" style="color: var(--text-muted); background-color: var(--bg-light);">
                            No disponible
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    /* VARIABLES LOCALES (Nuestro ADN) */
    :root {
        --primary: #1E90FF;
        --secondary-dark: #0D47A1;
        --secondary-light: #64B5F6;
        --text-main: #212121;
        --text-muted: #757575;
        --bg-light: #F5F5F5;
        --primary-soft: rgba(30, 144, 255, 0.1);
        --danger-soft: rgba(239, 68, 68, 0.1);
    }

    body {
        color: var(--text-main);
    }

    /* TEXTO DEGRADADO */
    .text-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
    }

    /* TARJETAS DE ESPACIO */
    .space-card {
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.4s ease;
    }

    .space-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px var(--primary-soft);
    }

    /* CONTENEDOR DEL ICONO */
    .icon-wrapper {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background-color: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        transition: all 0.3s ease;
        transform: rotate(-3deg);
    }

    .space-card:hover .icon-wrapper {
        transform: rotate(0deg) scale(1.05);
        background-color: var(--primary);
        color: #fff;
        box-shadow: 0 8px 20px var(--primary-soft);
    }

    /* BOTONES */
    .btn-action {
        background-color: var(--primary);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        background-color: var(--secondary-dark);
        transform: translateY(-2px);
    }

    /* INDICADORES DE ESTADO */
    .status-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .status-dot.available {
        background-color: #22c55e;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
    }

    .status-dot.unavailable {
        background-color: #ef4444;
    }

    .status-text {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-main);
    }

    /* ALERTA CUSTOMIZADA */
    .custom-alert-danger {
        background-color: var(--danger-soft);
        color: #ef4444;
        border: 1px dashed rgba(239, 68, 68, 0.3);
        border-radius: 16px;
    }
</style>
@endsection