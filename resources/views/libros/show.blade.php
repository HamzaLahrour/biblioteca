@extends('layouts.admin')

@section('title', 'Detalles del Libro')

@section('content')
<div class="row justify-content-center mb-5 libro-show-admin">
    <div class="col-md-10 col-lg-9">

        {{-- Volver --}}
        <div class="mb-3">
            <a href="{{ route('libros.index') }}" class="text-decoration-none fw-medium link-volver">
                <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
            </a>
        </div>

        {{-- ========================================== --}}
        {{-- FICHA DEL LIBRO --}}
        {{-- ========================================== --}}
        <div class="card shadow-sm border-0 rounded-4 mb-4 card-blue">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="fw-bold mb-0 titulo-seccion">
                    <i class="bi bi-book-half me-2"></i>Ficha Literaria
                </h4>

                {{-- Reemplazamos el UUID por la categoría, que es info útil para el admin --}}
                <span class="badge categoria-pill rounded-pill px-3 py-2 fw-medium">
                    <i class="bi bi-bookmark-fill me-1"></i>
                    {{ $libro->categoria->nombre ?? 'Sin Categoría' }}
                </span>
            </div>

            <div class="card-body p-4">
                <div class="row">
                    {{-- COLUMNA DE PORTADA Y STOCK --}}
                    <div class="col-md-4 text-center mb-4 mb-md-0 d-flex flex-column align-items-center">
                        @if($libro->portada)
                        <img src="{{ Str::startsWith($libro->portada, 'http') ? $libro->portada : asset('storage/' . $libro->portada) }}" alt="Portada de {{ $libro->titulo }}" class="img-fluid rounded-3 shadow-sm portada-libro" style="max-height: 300px; object-fit: cover;">
                        @else
                        <div class="placeholder-portada d-flex flex-column justify-content-center align-items-center rounded-3" style="height: 300px; width: 100%; max-width: 200px;">
                            <i class="bi bi-book opacity-50" style="font-size: 5rem;"></i>
                            <span class="mt-3 fw-medium small">Sin portada</span>
                        </div>
                        @endif

                        {{-- DISPONIBILIDAD --}}
                        <div class="mt-4 w-100 px-3">
                            <h6 class="fw-bold mb-2 text-uppercase etiqueta-mini">Disponibilidad</h6>
                            <div class="d-flex justify-content-center align-items-baseline p-2 rounded-3 disponibilidad-box {{ $disponibles > 0 ? 'is-disponible' : 'is-agotado' }}">
                                <span class="fs-4 fw-bold me-1">{{ $disponibles }}</span>
                                <span class="small opacity-75">/ {{ $libro->copias_totales }} en estantería</span>
                            </div>
                            @if($disponibles <= 0)
                                <div class="small fw-bold mt-2 text-agotado">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Agotado temporalmente
                        </div>
                        @endif
                    </div>
                </div>

                {{-- COLUMNA DE DATOS --}}
                <div class="col-md-8">
                    <h2 class="fw-bold mb-1 titulo-libro">{{ $libro->titulo }}</h2>
                    <h5 class="autor-libro mb-4">
                        <i class="bi bi-pen-fill me-2 fs-6"></i>{{ $libro->autor }}
                    </h5>

                    <dl class="row mb-0 datos-libro">
                        <dt class="col-sm-4">ISBN</dt>
                        <dd class="col-sm-8">
                            @if($libro->isbn)
                            <span class="font-monospace isbn-chip">{{ $libro->isbn }}</span>
                            @else
                            <span class="text-muted fst-italic">No registrado</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Editorial</dt>
                        <dd class="col-sm-8">{{ $libro->editorial ?: 'Desconocida' }}</dd>

                        <dt class="col-sm-4">Publicación</dt>
                        <dd class="col-sm-8">{{ $libro->anio_publicacion ?: 'Desconocido' }}</dd>

                        <dt class="col-sm-4">Copias totales</dt>
                        <dd class="col-sm-8">{{ $libro->copias_totales }}</dd>
                    </dl>

                    <hr class="separador-suave my-4">

                    <div>
                        <h6 class="fw-bold mb-2 subtitulo-seccion">Sinopsis</h6>
                        <p class="sinopsis mb-0">
                            {{ $libro->descripcion ?: 'No hay ninguna descripción disponible para este ejemplar.' }}
                        </p>
                    </div>
                </div>
            </div>

            <hr class="separador-suave mt-4 mb-3">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('libros.edit', $libro) }}" class="btn btn-primary-blue px-4 shadow-sm">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <form action="{{ route('libros.destroy', $libro) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="bi bi-trash-fill me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- ACTIVIDAD DEL EJEMPLAR --}}
    {{-- ========================================== --}}
    <div class="card shadow-sm border-0 rounded-4 card-blue">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
            <h5 class="mb-0 fw-bold titulo-seccion">
                <i class="bi bi-activity me-2"></i>Actividad del Ejemplar
            </h5>
        </div>

        <div class="card-body p-4 pt-2">

            {{-- MINI-ESTADÍSTICAS --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-neutral">
                        <div class="stat-numero">{{ $stats['total'] }}</div>
                        <div class="stat-label">Registros Totales</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-activo">
                        <div class="stat-numero">{{ $stats['activos'] }}</div>
                        <div class="stat-label">En Posesión</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-devuelto">
                        <div class="stat-numero">{{ $stats['devueltos'] }}</div>
                        <div class="stat-label">Devueltos</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card stat-perdido">
                        <div class="stat-numero">{{ $stats['perdidos'] }}</div>
                        <div class="stat-label">Perdidos</div>
                    </div>
                </div>
            </div>

            {{-- FILTROS --}}
            <div class="filtros-box rounded-4 mb-4 p-3">
                <form action="{{ route('libros.show', $libro->id) }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold mb-1 etiqueta-filtro">Buscar lector</label>
                        <div class="input-group input-group-sm input-group-blue">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="buscar_lector" class="form-control" placeholder="Nombre del alumno..." value="{{ request('buscar_lector') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold mb-1 etiqueta-filtro">Estado</label>
                        <select name="estado_prestamo" class="form-select form-select-sm select-blue">
                            <option value="">Cualquier estado</option>
                            <option value="activo" {{ request('estado_prestamo') == 'activo' ? 'selected' : '' }}>En posesión</option>
                            <option value="devuelto" {{ request('estado_prestamo') == 'devuelto' ? 'selected' : '' }}>Devuelto</option>
                            <option value="devuelto_tarde" {{ request('estado_prestamo') == 'devuelto_tarde' ? 'selected' : '' }}>Devuelto tarde</option>
                            <option value="perdido" {{ request('estado_prestamo') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary-blue w-100 fw-medium">
                            <i class="bi bi-funnel-fill me-1"></i>Filtrar
                        </button>
                        @if(request()->anyFilled(['buscar_lector', 'estado_prestamo']))
                        <a href="{{ route('libros.show', $libro->id) }}" class="btn btn-sm btn-limpiar" title="Limpiar filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- TABLA DE PRÉSTAMOS --}}
            @if($prestamos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 tabla-prestamos">
                    <thead>
                        <tr>
                            <th class="py-3">Lector</th>
                            <th class="py-3 text-center">Salida</th>
                            <th class="py-3 text-center">Devolución</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="py-3 text-end">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prestamos as $prestamo)
                        <tr>
                            <td class="py-3">
                                <div class="fw-bold celda-lector">{{ $prestamo->user->name ?? 'Usuario borrado' }}</div>
                            </td>

                            <td class="py-3 text-center small font-monospace celda-fecha">
                                <i class="bi bi-calendar-minus me-1"></i>{{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y') }}
                            </td>

                            <td class="py-3 text-center font-monospace small">
                                @if($prestamo->estado === 'activo')
                                <span class="chip-pendiente">
                                    <i class="bi bi-hourglass-split me-1"></i>Pendiente
                                </span>
                                @elseif($prestamo->fecha_devolucion_real)
                                <span class="celda-fecha">
                                    <i class="bi bi-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($prestamo->fecha_devolucion_real)->format('d/m/Y') }}
                                </span>
                                @else
                                <span class="text-muted">---</span>
                                @endif
                            </td>

                            <td class="py-3 text-center">
                                @if($prestamo->estado === 'activo')
                                <span class="badge-estado badge-activo">En Posesión</span>
                                @elseif($prestamo->estado === 'devuelto')
                                <span class="badge-estado badge-devuelto">Devuelto</span>
                                @elseif($prestamo->estado === 'devuelto_tarde')
                                <span class="badge-estado badge-tarde">Devuelto tarde</span>
                                @elseif($prestamo->estado === 'perdido')
                                <span class="badge-estado badge-perdido">Perdido</span>
                                @endif
                            </td>

                            <td class="text-end py-3">
                                <a href="{{ route('prestamos.show', $prestamo->id) }}" class="btn btn-sm btn-detalle" title="Ver recibo de préstamo">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                {{ $prestamos->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @else
            <div class="estado-vacio text-center py-5 my-2 rounded-3">
                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                <p class="mb-0 fw-medium">No hay registros con estos filtros.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- OPINIONES (MODERACIÓN — SOLO ADMIN) --}}
    {{-- ========================================== --}}
    <div class="card shadow-sm border-0 rounded-4 mt-4 mb-5 card-blue">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold titulo-seccion">
                <i class="bi bi-chat-square-quote-fill me-2"></i>Opiniones de los lectores
            </h5>
            <span class="badge contador-opiniones rounded-pill px-3 py-2">
                {{ $libro->comentarios->count() }} {{ $libro->comentarios->count() === 1 ? 'opinión' : 'opiniones' }}
            </span>
        </div>

        <div class="card-body p-4 pt-2">

            {{-- Aviso al admin: solo modera, no comenta --}}
            <div class="aviso-moderacion mb-4">
                <i class="bi bi-shield-lock-fill me-2"></i>
                <span>
                    <strong>Modo moderación.</strong>
                    Como administrador puedes revisar y eliminar opiniones, pero no participar en la conversación.
                </span>
            </div>

            {{-- LISTADO DE COMENTARIOS --}}
            <div class="comentarios-lista">
                @forelse($libro->comentarios as $comentario)
                <div class="comentario-item d-flex mb-3">
                    <div class="me-3">
                        <div class="avatar-usuario d-flex justify-content-center align-items-center rounded-circle fw-bold fs-5" style="width: 45px; height: 45px;">
                            {{ strtoupper(substr($comentario->user->name ?? '?', 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-grow-1 comentario-burbuja p-3 rounded-4">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                            <h6 class="fw-bold mb-0 nombre-usuario">{{ $comentario->user->name ?? 'Usuario eliminado' }}</h6>
                            <small class="fecha-comentario">{{ $comentario->created_at->diffForHumans() }}</small>
                        </div>

                        <div class="mb-2 estrellas-valoracion" style="font-size: 0.9rem;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <=$comentario->estrellas)
                                <i class="bi bi-star-fill"></i>
                                @else
                                <i class="bi bi-star opacity-25"></i>
                                @endif
                                @endfor
                                <span class="ms-2 small valoracion-numero">{{ $comentario->estrellas }}/5</span>
                        </div>

                        @if($comentario->contenido)
                        <p class="mb-2 contenido-comentario">{{ $comentario->contenido }}</p>
                        @else
                        <p class="mb-2 contenido-comentario fst-italic opacity-75">— Sin texto, solo valoración —</p>
                        @endif

                        {{-- Solo botón de borrar (moderación) --}}
                        <div class="d-flex justify-content-end mt-2">
                            <form action="{{ route('comentarios.destroy', $comentario->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta opinión? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-eliminar-comentario" title="Eliminar opinión">
                                    <i class="bi bi-trash3 me-1"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="estado-vacio text-center py-5 rounded-3">
                    <i class="bi bi-chat-left-dots fs-1 opacity-25 d-block mb-2"></i>
                    <p class="mb-0 fw-medium">Aún no hay opiniones de los lectores sobre este libro.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
</div>

{{-- ============================================================ --}}
{{-- ESTILOS — Paleta azul consistente para vista admin --}}
{{-- ============================================================ --}}
<style>
    .libro-show-admin {
        --primary-blue: #38BDF8;
        --primary-blue-dark: #0284C7;
        --primary-blue-deep: #082F49;
        --primary-blue-soft: #E0F2FE;
        --primary: #1E90FF;

        --ink: #0F172A;
        --ink-soft: #334155;
        --muted: #64748B;
        --line: #E2E8F0;
        --bg-soft: #F8FAFC;

        --danger: #DC2626;
        --danger-soft: #FEE2E2;
        --warning: #B45309;
        --warning-soft: #FEF3C7;
        --success: #047857;
        --success-soft: #D1FAE5;
        --gold: #F59E0B;

        color: var(--ink);
    }

    /* Volver */
    .libro-show-admin .link-volver {
        color: var(--ink-soft);
    }

    .libro-show-admin .link-volver:hover {
        color: var(--primary-blue-dark);
    }

    /* Card base con acento azul */
    .libro-show-admin .card-blue {
        border-top: 3px solid var(--primary) !important;
    }

    /* Títulos */
    .libro-show-admin .titulo-seccion {
        color: var(--primary-blue-deep);
    }

    .libro-show-admin .titulo-seccion .bi {
        color: var(--primary);
    }

    .libro-show-admin .subtitulo-seccion {
        color: var(--ink);
    }

    .libro-show-admin .titulo-libro {
        color: var(--primary-blue-deep);
        line-height: 1.2;
    }

    .libro-show-admin .autor-libro {
        color: var(--ink-soft);
        font-weight: 500;
    }

    /* Categoría pill (sustituye al UUID) */
    .libro-show-admin .categoria-pill {
        background: var(--primary-blue-soft);
        color: var(--primary-blue-dark);
        border: 1px solid #BAE6FD;
    }

    /* Portada placeholder */
    .libro-show-admin .placeholder-portada {
        background: var(--primary-blue-soft);
        color: var(--primary-blue-dark);
        border: 1px dashed #BAE6FD;
    }

    .libro-show-admin .portada-libro {
        border: 1px solid var(--line);
    }

    /* Disponibilidad */
    .libro-show-admin .etiqueta-mini {
        color: var(--muted);
        font-size: 0.7rem;
        letter-spacing: 1px;
    }

    .libro-show-admin .disponibilidad-box {
        border: 1px solid;
    }

    .libro-show-admin .disponibilidad-box.is-disponible {
        background: var(--primary-blue-soft);
        border-color: #BAE6FD;
        color: var(--primary-blue-dark);
    }

    .libro-show-admin .disponibilidad-box.is-agotado {
        background: var(--danger-soft);
        border-color: #FECACA;
        color: var(--danger);
    }

    .libro-show-admin .text-agotado {
        color: var(--danger);
    }

    /* Datos del libro (dl) */
    .libro-show-admin .datos-libro dt {
        color: var(--muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .libro-show-admin .datos-libro dd {
        color: var(--ink);
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        margin-bottom: 0;
        border-bottom: 1px dashed var(--line);
    }

    .libro-show-admin .datos-libro dt {
        border-bottom: 1px dashed var(--line);
    }

    .libro-show-admin .datos-libro>div:last-child dt,
    .libro-show-admin .datos-libro>div:last-child dd {
        border-bottom: 0;
    }

    .libro-show-admin .isbn-chip {
        background: var(--bg-soft);
        color: var(--ink);
        border: 1px solid var(--line);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
    }

    .libro-show-admin .separador-suave {
        border-top: 1px solid var(--line);
        opacity: 1;
    }

    .libro-show-admin .sinopsis {
        color: var(--ink-soft);
        line-height: 1.65;
        text-align: justify;
    }

    /* Botones azules */
    .libro-show-admin .btn-primary-blue {
        background: var(--primary);
        border: 1px solid var(--primary);
        color: #fff;
    }

    .libro-show-admin .btn-primary-blue:hover,
    .libro-show-admin .btn-primary-blue:focus {
        background: var(--primary-blue-dark);
        border-color: var(--primary-blue-dark);
        color: #fff;
    }

    /* Stat cards — paleta consistente */
    .libro-show-admin .stat-card {
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 1rem 0.75rem;
        text-align: center;
        background: #fff;
        height: 100%;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .libro-show-admin .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(2, 132, 199, 0.08);
    }

    .libro-show-admin .stat-numero {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .libro-show-admin .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        color: var(--muted);
    }

    .libro-show-admin .stat-neutral .stat-numero {
        color: var(--primary-blue-deep);
    }

    .libro-show-admin .stat-activo {
        background: var(--primary-blue-soft);
        border-color: #BAE6FD;
    }

    .libro-show-admin .stat-activo .stat-numero {
        color: var(--primary-blue-dark);
    }

    .libro-show-admin .stat-activo .stat-label {
        color: var(--primary-blue-dark);
    }

    .libro-show-admin .stat-devuelto {
        background: var(--success-soft);
        border-color: #A7F3D0;
    }

    .libro-show-admin .stat-devuelto .stat-numero {
        color: var(--success);
    }

    .libro-show-admin .stat-devuelto .stat-label {
        color: var(--success);
    }

    .libro-show-admin .stat-perdido {
        background: var(--danger-soft);
        border-color: #FECACA;
    }

    .libro-show-admin .stat-perdido .stat-numero {
        color: var(--danger);
    }

    .libro-show-admin .stat-perdido .stat-label {
        color: var(--danger);
    }

    /* Filtros — claro y limpio, NO bg-dark */
    .libro-show-admin .filtros-box {
        background: var(--bg-soft);
        border: 1px solid var(--line);
    }

    .libro-show-admin .etiqueta-filtro {
        color: var(--ink-soft);
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    .libro-show-admin .input-group-blue .input-group-text {
        background: #fff;
        border: 1px solid var(--line);
        border-right: 0;
        color: var(--muted);
    }

    .libro-show-admin .input-group-blue .form-control {
        border: 1px solid var(--line);
        border-left: 0;
    }

    .libro-show-admin .input-group-blue .form-control:focus,
    .libro-show-admin .select-blue:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.2);
    }

    .libro-show-admin .select-blue {
        border: 1px solid var(--line);
        color: var(--ink);
    }

    .libro-show-admin .btn-limpiar {
        background: #fff;
        border: 1px solid var(--line);
        color: var(--muted);
    }

    .libro-show-admin .btn-limpiar:hover {
        color: var(--ink);
        border-color: var(--ink-soft);
    }

    /* Tabla préstamos */
    .libro-show-admin .tabla-prestamos thead th {
        background: var(--bg-soft);
        color: var(--muted);
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        border: 0;
    }

    .libro-show-admin .tabla-prestamos thead th:first-child {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }

    .libro-show-admin .tabla-prestamos thead th:last-child {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .libro-show-admin .tabla-prestamos tbody tr {
        border-bottom: 1px solid var(--line);
    }

    .libro-show-admin .tabla-prestamos tbody tr:last-child {
        border-bottom: 0;
    }

    .libro-show-admin .tabla-prestamos tbody tr:hover {
        background: var(--primary-blue-soft);
    }

    .libro-show-admin .celda-lector {
        color: var(--primary-blue-deep);
        font-size: 0.95rem;
    }

    .libro-show-admin .celda-fecha {
        color: var(--ink-soft);
    }

    /* Badges de estado */
    .libro-show-admin .badge-estado {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid;
    }

    .libro-show-admin .badge-activo {
        background: var(--primary-blue-soft);
        color: var(--primary-blue-dark);
        border-color: #BAE6FD;
    }

    .libro-show-admin .badge-devuelto {
        background: var(--success-soft);
        color: var(--success);
        border-color: #A7F3D0;
    }

    .libro-show-admin .badge-tarde {
        background: var(--warning-soft);
        color: var(--warning);
        border-color: #FDE68A;
    }

    .libro-show-admin .badge-perdido {
        background: var(--danger-soft);
        color: var(--danger);
        border-color: #FECACA;
    }

    .libro-show-admin .chip-pendiente {
        background: var(--warning-soft);
        color: var(--warning);
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #FDE68A;
        font-weight: 600;
    }

    .libro-show-admin .btn-detalle {
        background: #fff;
        border: 1px solid var(--line);
        color: var(--primary-blue-dark);
    }

    .libro-show-admin .btn-detalle:hover {
        background: var(--primary-blue-soft);
        border-color: var(--primary-blue);
        color: var(--primary-blue-dark);
    }

    /* Estado vacío */
    .libro-show-admin .estado-vacio {
        background: var(--bg-soft);
        color: var(--muted);
        border: 1px dashed var(--line);
    }

    /* Aviso de moderación */
    .libro-show-admin .aviso-moderacion {
        background: var(--primary-blue-soft);
        color: var(--primary-blue-deep);
        border: 1px solid #BAE6FD;
        border-left: 4px solid var(--primary);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
    }

    .libro-show-admin .aviso-moderacion .bi {
        color: var(--primary-blue-dark);
        font-size: 1.1rem;
    }

    /* Contador de opiniones */
    .libro-show-admin .contador-opiniones {
        background: var(--primary-blue-soft);
        color: var(--primary-blue-dark);
        border: 1px solid #BAE6FD;
        font-weight: 600;
        font-size: 0.8rem;
    }

    /* Comentarios */
    .libro-show-admin .avatar-usuario {
        background: var(--primary-blue-soft);
        color: var(--primary-blue-dark);
        border: 1px solid #BAE6FD;
    }

    .libro-show-admin .comentario-burbuja {
        background: #fff;
        border: 1px solid var(--line);
        transition: border-color 0.15s ease;
    }

    .libro-show-admin .comentario-burbuja:hover {
        border-color: #BAE6FD;
    }

    .libro-show-admin .nombre-usuario {
        color: var(--primary-blue-deep);
    }

    .libro-show-admin .fecha-comentario {
        color: var(--muted);
    }

    .libro-show-admin .estrellas-valoracion {
        color: var(--gold);
    }

    .libro-show-admin .estrellas-valoracion .opacity-25 {
        color: var(--line);
        opacity: 1 !important;
    }

    .libro-show-admin .valoracion-numero {
        color: var(--ink-soft);
        font-weight: 600;
    }

    .libro-show-admin .contenido-comentario {
        color: var(--ink-soft);
        font-size: 0.95rem;
        line-height: 1.55;
    }

    .libro-show-admin .btn-eliminar-comentario {
        background: #fff;
        border: 1px solid #FECACA;
        color: var(--danger);
        font-weight: 500;
    }

    .libro-show-admin .btn-eliminar-comentario:hover {
        background: var(--danger-soft);
        border-color: var(--danger);
        color: var(--danger);
    }

    /* Paginación */
    .libro-show-admin .pagination .page-link {
        color: var(--primary-blue-dark);
        border-color: var(--line);
    }

    .libro-show-admin .pagination .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
</style>
@endsection