@extends('layouts.app')
@section('title', 'Confirmar Reserva | LibreLah')

@section('content')
<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5 text-center">

            {{-- Animación de éxito premium --}}
            <div class="success-icon-wrapper mx-auto mb-4 shadow-sm">
                <i class="bi bi-check2-circle"></i>
            </div>

            {{-- Cabecera con ADN --}}
            <h2 class="fw-bold mb-2" style="color: var(--text-main); letter-spacing: -0.5px;">
                ¡Hemos encontrado un <span class="text-gradient">sitio!</span>
            </h2>
            <p class="text-muted mb-5" style="font-size: 1.05rem;">
                Revisa los datos y confirma tu reserva antes de que alguien te quite el puesto.
            </p>

            {{-- Tarjeta de Confirmación Flotante --}}
            <div class="card booking-card border-0 text-start mb-4">
                <div class="card-body p-4 p-md-5">

                    <h6 class="text-uppercase small fw-bold mb-3" style="color: var(--primary); letter-spacing: 1px;">
                        Se te asignará:
                    </h6>

                    <h4 class="fw-bold mb-4 border-bottom-subtle pb-3" style="color: var(--secondary-dark);">
                        <i class="bi bi-pin-map-fill me-2" style="color: var(--primary);"></i>{{ $espacio->nombre }}
                    </h4>

                    {{-- Fecha con Icon Box Premium --}}
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-box me-3">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <span class="fw-bold fs-5" style="color: var(--text-main);">
                            {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                        </span>
                    </div>

                    {{-- Hora con Icon Box Premium --}}
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-3">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <span class="fw-bold fs-5" style="color: var(--text-main);">
                            {{ $hora_inicio }} - {{ $hora_fin }}
                        </span>
                    </div>

                </div>
            </div>

            {{-- FORMULARIO DEFINITIVO (Botones Sutiles) --}}
            <form action="{{ route('reservas_usuario.store') }}" method="POST" class="mt-2">
                @csrf
                <input type="hidden" name="espacio_id" value="{{ $espacio->id }}">
                <input type="hidden" name="fecha" value="{{ $fecha }}">
                <input type="hidden" name="hora_inicio" value="{{ $hora_inicio }}">
                <input type="hidden" name="hora_fin" value="{{ $hora_fin }}">

                {{-- flex-column-reverse pone "Confirmar" arriba en móvil. flex-sm-row los pone lado a lado en PC --}}
                <div class="d-flex flex-column-reverse flex-sm-row justify-content-center align-items-center gap-2 gap-sm-3 mt-4">
                    <a href="{{ route('reservas_usuario.create', $tipo->id ?? 1) }}" class="btn btn-ghost-cancel rounded-pill fw-medium px-4 py-2">
                        Cambiar hora
                    </a>
                    <button type="submit" class="btn btn-primary btn-action rounded-pill fw-bold px-5 py-2 d-flex justify-content-center align-items-center gap-2 w-100 w-sm-auto">
                        Confirmar Reserva
                    </button>
                </div>
            </form>

        </div>
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
        --success-main: #22c55e;
        --success-soft: rgba(34, 197, 94, 0.15);
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

    /* ICONO DE ÉXITO */
    .success-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: var(--success-soft);
        color: var(--success-main);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        animation: popIn 0.5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }

    @keyframes popIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        80% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* TARJETA DE CONFIRMACIÓN */
    .booking-card {
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.03) !important;
    }

    .border-bottom-subtle {
        border-bottom: 2px dashed rgba(0, 0, 0, 0.06);
    }

    /* ICON BOXES */
    .icon-box {
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background-color: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* BOTONES SUTILES (Definidos) */
    .btn-action {
        background-color: var(--primary);
        border: none;
        transition: all 0.25s ease;
        /* Sombra mucho más suave y pequeña */
        box-shadow: 0 4px 10px rgba(30, 144, 255, 0.2);
    }

    .btn-action:hover {
        background-color: var(--secondary-dark);
        transform: translateY(-1px);
        /* Elevación más corta */
        box-shadow: 0 6px 15px rgba(13, 71, 161, 0.25);
    }

    /* Botón fantasma (Ghost Button) para la acción secundaria */
    .btn-ghost-cancel {
        color: var(--text-muted);
        background-color: transparent;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .btn-ghost-cancel:hover {
        background-color: var(--bg-light);
        color: var(--text-main);
    }

    /* Utilidad para anchos en responsive */
    @media (min-width: 576px) {
        .w-sm-auto {
            width: auto !important;
        }
    }
</style>
@endsection