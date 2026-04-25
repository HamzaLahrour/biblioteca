@extends('layouts.app')

@section('title', 'Mi Espacio | LibreLah')

@section('content')
<div class="container py-5 mb-5">

    {{-- CABECERA DE SECCIÓN --}}
    <div class="mb-5">
        <h2 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">
            Mi <span class="text-gradient">Espacio</span>
        </h2>
        <p class="text-muted">Gestiona tus lecturas, reservas y estado de tu cuenta.</p>
    </div>

    <div class="row g-4 g-xl-5">
        {{-- COLUMNA IZQUIERDA: EL CARNET Y SANCIONES --}}
        <div class="col-lg-4">

            {{-- 🚨 ALERTA DE SANCIÓN --}}
            @if($sancionActiva)
            <div class="alert custom-alert-danger d-flex align-items-start gap-3 shadow-sm mb-4" role="alert">
                <i class="bi bi-shield-fill-exclamation fs-3 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Cuenta Suspendida</h6>
                    <p class="mb-0 small" style="line-height: 1.4;">
                        Tienes una sanción activa por "{{ $sancionActiva->motivo ?? 'retraso en devoluciones' }}".
                        No podrás realizar préstamos ni reservas hasta el <strong>{{ \Carbon\Carbon::parse($sancionActiva->fecha_fin)->format('d/m/Y') }}</strong>.
                    </p>
                </div>
            </div>
            @endif

            {{-- 🪪 CARNET DIGITAL PREMIUM --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4 digital-card position-relative">
                {{-- Efectos de fondo abstracto --}}
                <div class="position-absolute rounded-circle bg-white opacity-10" style="width: 150px; height: 150px; top: -50px; right: -50px;"></div>
                <div class="position-absolute rounded-circle bg-white opacity-10" style="width: 100px; height: 100px; bottom: -20px; left: -20px;"></div>
                <i class="bi bi-fingerprint position-absolute opacity-10" style="font-size: 8rem; right: -10px; bottom: -10px; color: white;"></i>

                <div class="card-body p-4 position-relative z-1 text-white">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="fw-bold text-uppercase" style="letter-spacing: 2px; font-size: 0.75rem; color: rgba(255,255,255,0.7);">Lector Autorizado</div>
                        <div class="status-badge {{ $sancionActiva ? 'bg-danger' : 'bg-success' }}">
                            <i class="bi {{ $sancionActiva ? 'bi-x-circle-fill' : 'bi-check-circle-fill' }} me-1"></i>
                            {{ $sancionActiva ? 'Inactivo' : 'Activo' }}
                        </div>
                    </div>

                    <h3 class="fw-bold mb-1 text-truncate" title="{{ $usuario->name }}">{{ $usuario->name }}</h3>
                    <div class="small mb-4 text-truncate" style="color: rgba(255,255,255,0.8);">{{ $usuario->email }}</div>

                    {{-- DNI con efecto Glassmorphism --}}
                    <div class="p-3 rounded-3 mb-2 text-center glass-panel">
                        <div class="font-monospace fw-bold fs-5" style="letter-spacing: 3px;">{{ $usuario->dni ?? 'SIN-DNI' }}</div>
                    </div>
                    <div class="text-center font-monospace" style="font-size: 0.65rem; color: rgba(255,255,255,0.6);">ID: {{ str_pad($usuario->id, 8, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

        </div>

        {{-- COLUMNA DERECHA: MIS COSAS (Préstamos y Reservas) --}}
        <div class="col-lg-8">

            {{-- 📚 SECCIÓN: MIS PRÉSTAMOS ACTIVOS --}}
            <div class="d-flex justify-content-between align-items-end mb-3">
                <h4 class="fw-bold m-0" style="color: var(--secondary-dark);">Mis Lecturas</h4>
                <a href="{{ route('catalogo.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Explorar</a>
            </div>

            @if($prestamos->count() > 0)
            <div class="row row-cols-1 g-3 mb-5">
                @foreach($prestamos as $prestamo)
                @php
                $vence = \Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista);
                $hoy = \Carbon\Carbon::today();
                $diasRestantes = $hoy->diffInDays($vence, false);
                @endphp

                <div class="col">
                    <div class="card border-0 shadow-sm rounded-4 h-100 float-card">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="bg-light rounded-3 overflow-hidden me-3 flex-shrink-0 book-thumb">
                                @if($prestamo->libro->portada)
                                <img src="{{ $prestamo->libro->portada }}" alt="Portada" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                <div class="w-100 h-100 d-flex justify-content-center align-items-center text-muted" style="background-color: var(--bg-light);">
                                    <i class="bi bi-book text-primary opacity-50"></i>
                                </div>
                                @endif
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <h6 class="fw-bold mb-1 text-truncate" style="color: var(--secondary-dark);">{{ $prestamo->libro->titulo }}</h6>
                                <p class="text-muted small mb-1 text-truncate">{{ $prestamo->libro->autor }}</p>

                                {{-- Fecha: ahora en gris, sin competir con el título --}}
                                <div class="small text-muted mb-2" style="font-size: 0.78rem;">
                                    <i class="bi bi-calendar2 me-1"></i> {{ $vence->format('d/m/Y') }}
                                </div>

                                @if($diasRestantes < 0)
                                    <span class="custom-tag tag-danger"><i class="bi bi-exclamation-circle-fill me-1"></i>¡Vencido hace {{ abs($diasRestantes) }} días!</span>
                                    @elseif($diasRestantes == 0)
                                    <span class="custom-tag tag-warning"><i class="bi bi-clock-fill me-1"></i>Devolver hoy</span>
                                    @elseif($diasRestantes <= 2)
                                        <span class="custom-tag tag-warning"><i class="bi bi-hourglass-split me-1"></i>Quedan {{ $diasRestantes }} días</span>
                                        @else
                                        <span class="custom-tag tag-success"><i class="bi bi-calendar2-check-fill me-1"></i>Devolver en {{ $diasRestantes }} días</span>
                                        @endif

                                        <div class="mt-2">
                                            {{-- PRIORIDAD 1: ¿Ha alcanzado el límite dinámico de la base de datos? --}}
                                            @if($prestamo->renovaciones >= App\Models\Configuracion::get('max_renovaciones', 2))
                                            <div class="d-flex align-items-center text-danger fw-bold" style="font-size: 0.75rem;">
                                                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                                                Límite de renovaciones alcanzado
                                            </div>

                                            {{-- PRIORIDAD 2: ¿El préstamo ya ha vencido? --}}
                                            @elseif($diasRestantes < 0)
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-info-circle me-2"></i> Préstamo vencido, contacta con la biblioteca
                                        </div>

                                        {{-- PRIORIDAD 3: ¿Estamos en la ventana de los últimos 3 días? --}}
                                        @elseif($diasRestantes <= 3)
                                            <form action="{{ route('perfil.prestamos.renovar', $prestamo->id) }}" method="POST" onsubmit="return confirm('¿Quieres solicitar una ampliación para este libro?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" style="font-size: 0.75rem;">
                                                <i class="bi bi-arrow-clockwise me-1"></i> Renovar ahora
                                            </button>
                                            </form>

                                            {{-- PRIORIDAD 4: Si no es nada de lo anterior, es demasiado pronto --}}
                                            @else
                                            <span class="text-muted" style="font-size: 0.7rem;">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                Podrás renovar a partir del {{ $vence->copy()->subDays(3)->format('d/m/Y') }}
                                            </span>
                                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- Empty State Premium --}}
        <div class="empty-state-card p-5 text-center mb-5">
            <div class="empty-icon-wrapper mx-auto mb-3">
                <i class="bi bi-book"></i>
            </div>
            <h6 class="fw-bold" style="color: var(--secondary-dark);">No tienes libros en casa</h6>
            <p class="text-muted small mb-0">Cuando te lleves un libro de la biblioteca, aparecerá aquí con su fecha de devolución.</p>
        </div>
        @endif

        {{-- 🛋️ SECCIÓN: MIS RESERVAS DE SALAS --}}
        <div class="d-flex justify-content-between align-items-end mb-3 mt-4">
            <h4 class="fw-bold m-0" style="color: var(--secondary-dark);">Próximas Reservas</h4>
            <div>
                {{-- Este botón lo programarás mañana cuando estés fresco --}}
                <a href="#" class="btn btn-sm btn-link text-muted text-decoration-none me-2 fw-bold" style="font-size: 0.85rem;">Ver historial</a>
                <a href="{{ route('reservas_usuario.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Reservar Sala</a>
            </div>
        </div>

        @if($reservas->count() > 0)
        <div class="row row-cols-1 g-3">
            @foreach($reservas as $reserva)
            @php
            $fechaSolo = \Carbon\Carbon::parse($reserva->fecha_reserva)->format('Y-m-d');
            $inicio = \Carbon\Carbon::parse($fechaSolo . ' ' . $reserva->hora_inicio);
            $fin = \Carbon\Carbon::parse($fechaSolo . ' ' . $reserva->hora_fin);
            $ahora = \Carbon\Carbon::now();

            $yaPaso = $ahora->gt($fin);
            $enCurso = $ahora->between($inicio, $fin);
            $minutosParaInicio = $ahora->diffInMinutes($inicio, false);

            // Regla: Cancelable si no ha pasado, no está en curso y faltan >= 30 min
            $puedeCancelar = !$yaPaso && !$enCurso && ($minutosParaInicio >= 30);
            @endphp

            {{-- Oculta la tarjeta si ya pasó o está cancelada --}}
            @if(!$yaPaso && $reserva->estado !== 'cancelada')
            <div class="col">
                <div class="card border border-light shadow-sm rounded-4 overflow-hidden float-card">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">
                            {{-- Fecha Icon Box --}}
                            <div class="date-box me-3">
                                <span class="fs-5 fw-bold" style="line-height: 1;">{{ $inicio->format('d') }}</span>
                                <span class="small text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $inicio->isoFormat('MMM') }}</span>
                            </div>

                            {{-- Información Limpia --}}
                            <div>
                                <h6 class="fw-bold mb-1" style="color: var(--secondary-dark);">{{ $reserva->espacio->nombre ?? 'Sala' }}</h6>
                                <div class="text-muted small fw-medium">
                                    <i class="bi bi-clock-fill me-1" style="color: var(--primary);"></i>
                                    {{ $inicio->format('H:i') }} - {{ $fin->format('H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- BOTONERA INVISIBLE (Solo acciones) --}}
                        @if($puedeCancelar)
                        <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas cancelar esta reserva de sala?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-ghost-danger rounded-circle p-2" title="Cancelar reserva">
                                <i class="bi bi-trash3-fill fs-5"></i>
                            </button>
                        </form>
                        @elseif(!$enCurso)
                        {{-- Candado sutil (margen de 30 min superado) --}}
                        <button type="button" class="btn btn-sm text-muted rounded-circle p-2 border-0" title="Demasiado tarde para cancelar" style="background: transparent; cursor: not-allowed;">
                            <i class="bi bi-lock-fill fs-5 opacity-50"></i>
                        </button>
                        @endif

                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @else
        {{-- Empty State Premium --}}
        <div class="empty-state-card p-4 text-center">
            <p class="text-muted small mb-0">No tienes ninguna sala reservada para los próximos días.</p>
        </div>
        @endif

    </div>
</div>
</div>

<style>
    /* VARIABLES LOCALES (ADN LibreLah) */
    :root {
        --primary: #1E90FF;
        --secondary-dark: #0D47A1;
        --secondary-light: #64B5F6;
        --text-main: #212121;
        --text-muted: #757575;
        --bg-light: #F8F9FA;
        --primary-soft: rgba(30, 144, 255, 0.1);
        --danger-soft: rgba(239, 68, 68, 0.1);
        --success-soft: rgba(34, 197, 94, 0.15);
    }

    /* TEXTO DEGRADADO */
    .text-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 800;
    }

    /* ALERTA CUSTOM SANCIONES */
    .custom-alert-danger {
        background-color: var(--danger-soft);
        color: #ef4444;
        border: 1px dashed rgba(239, 68, 68, 0.3);
        border-radius: 16px;
    }

    /* CARNET DIGITAL */
    .digital-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary-dark) 100%);
        box-shadow: 0 10px 30px var(--primary-soft) !important;
    }

    .glass-panel {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* TARJETAS FLOTANTES (Préstamos y Reservas) */
    .float-card {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
    }

    .float-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px var(--primary-soft) !important;
    }

    /* MINIATURAS DE LIBROS */
    .book-thumb {
        width: 60px;
        height: 85px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* ETIQUETAS DE ESTADO (Custom Tags) */
    .custom-tag {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .tag-danger {
        background-color: var(--danger-soft);
        color: #ef4444;
    }

    .tag-warning {
        background-color: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .tag-success {
        background-color: var(--success-soft);
        color: #16a34a;
    }

    /* FECHA RESERVAS (Icon Box) */
    .date-box {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        background-color: var(--primary-soft);
        color: var(--primary);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    /* BOTONES */
    .btn-outline-primary {
        color: var(--primary);
        border: 2px solid var(--primary-soft);
        background: transparent;
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
        box-shadow: 0 4px 10px var(--primary-soft);
    }

    .btn-ghost-danger {
        color: var(--text-muted);
        background: transparent;
        transition: all 0.2s ease;
    }

    .btn-ghost-danger:hover {
        background: var(--danger-soft);
        color: #ef4444;
    }

    /* EMPTY STATES */
    .empty-state-card {
        border-radius: 24px;
        background: #fff;
        border: 1px dashed rgba(0, 0, 0, 0.08);
    }

    .empty-icon-wrapper {
        width: 64px;
        height: 64px;
        background: var(--primary-soft);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: var(--primary);
        transform: rotate(-5deg);
    }

    /* Utilidad para truncar textos largos en tarjetas pequeñas */
    .min-w-0 {
        min-width: 0;
    }
</style>
@endsection