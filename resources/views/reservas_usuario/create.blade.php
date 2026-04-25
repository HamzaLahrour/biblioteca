@extends('layouts.app')
@section('title', 'Elegir Horario | LibreLah')

@section('content')
<div class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">

            {{-- Botón de Volver Premium --}}
            <a href="{{ route('reservas_usuario.index') }}" class="back-link mb-4 d-inline-block">
                <i class="bi bi-arrow-left me-2"></i>Volver a tipos de espacio
            </a>

            {{-- Cabecera con nuestro ADN --}}
            <div class="mb-5 text-center">
                <span class="badge badge-custom rounded-pill px-3 py-2 fw-bold mb-3 d-inline-block">
                    <i class="bi bi-grid-1x2 me-1"></i> {{ $tipo->nombre }}
                </span>
                <h2 class="fw-bold" style="color: var(--text-main); letter-spacing: -0.5px;">
                    ¿Cuándo lo <span class="text-gradient">necesitas?</span>
                </h2>
                <p class="text-muted mt-2">Dinos fecha y hora para buscarte el mejor hueco.</p>
            </div>



            {{-- Tarjeta de Formulario Premium --}}
            <form action="{{ route('reservas_usuario.comprobar', $tipo->id) }}" method="POST" class="booking-form p-4 p-md-5">
                @csrf

                <div class="mb-4 position-relative">
                    <label class="form-label text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="bi bi-calendar3 me-1" style="color: var(--primary);"></i> Fecha
                    </label>
                    <input type="date" name="fecha" class="form-control form-control-lg custom-input rounded-3" value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-6">
                        <label class="form-label text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="bi bi-clock me-1" style="color: var(--primary);"></i> Hora Inicio
                        </label>
                        <input type="time" name="hora_inicio" class="form-control form-control-lg custom-input rounded-3" value="{{ old('hora_inicio') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="bi bi-clock-fill me-1" style="color: var(--primary);"></i> Hora Fin
                        </label>
                        <input type="time" name="hora_fin" class="form-control form-control-lg custom-input rounded-3" value="{{ old('hora_fin') }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-action w-100 rounded-pill fw-bold d-flex justify-content-center align-items-center gap-2" style="padding-top: 14px; padding-bottom: 14px; font-size: 1.05rem;">
                    Buscar hueco libre <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="accordion mt-4 shadow-sm rounded-4 border-0" id="accordionHorarios">
                <div class="accordion-item border-0 rounded-4 overflow-hidden">
                    <h2 class="accordion-header" id="headingHorarios">
                        <button class="accordion-button collapsed py-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHorarios" aria-expanded="false" aria-controls="collapseHorarios" style="background-color: var(--bg-light); color: var(--secondary-dark); font-size: 0.9rem;">
                            <i class="bi bi-clock-history me-2" style="color: var(--primary);"></i> Consultar horarios ocupados de hoy
                        </button>
                    </h2>
                    <div id="collapseHorarios" class="accordion-collapse collapse" aria-labelledby="headingHorarios" data-bs-parent="#accordionHorarios">
                        <div class="accordion-body border-top" style="border-color: rgba(0,0,0,0.05) !important;">

                            @if(isset($reservasHoy) && $reservasHoy->count() > 0)
                            <p class="text-muted small mb-3">Evita solapar tu reserva con estos tramos:</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($reservasHoy as $reserva)
                                <span class="badge bg-white text-danger border border-danger-subtle px-3 py-2 rounded-pill shadow-sm" style="font-weight: 600;">
                                    {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <div class="text-success small fw-bold d-flex align-items-center">
                                <i class="bi bi-stars fs-5 me-2"></i> ¡Todo libre por ahora! Tienes vía libre para elegir.
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

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
        --danger-soft: rgba(239, 68, 68, 0.1);
    }

    body {
        color: var(--text-main);
    }

    /* DEGRADADO */
    .text-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
    }

    /* ENLACE DE VOLVER */
    .back-link {
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        color: var(--primary);
        transform: translateX(-3px);
    }

    /* INSIGNIA DEL TIPO DE ESPACIO */
    .badge-custom {
        background-color: var(--primary-soft);
        color: var(--secondary-dark);
        border: 1px solid rgba(30, 144, 255, 0.2);
    }

    /* CONTENEDOR DEL FORMULARIO */
    .booking-form {
        background-color: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    /* INPUTS CUSTOMIZADOS */
    .custom-input {
        background-color: var(--bg-light);
        border: 2px solid transparent;
        color: var(--secondary-dark);
        font-weight: 500;
        transition: all 0.3s ease;
        padding-left: 1rem;
    }

    .custom-input:focus {
        background-color: #fff;
        border-color: var(--secondary-light);
        box-shadow: 0 0 0 4px var(--primary-soft);
        outline: none;
    }

    /* Eliminar el icono de calendario por defecto en webkit si prefieres el de bootstrap (opcional) */
    /* .custom-input::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.6; transition: 0.2s; } */
    /* .custom-input::-webkit-calendar-picker-indicator:hover { opacity: 1; } */

    /* BOTÓN PRIMARIO */
    .btn-action {
        background-color: var(--primary);
        border: none;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 4px 15px rgba(30, 144, 255, 0.3);
    }

    .btn-action:hover {
        background-color: var(--secondary-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13, 71, 161, 0.4);
    }
</style>
@endsection