@extends('layouts.app')

@section('title', 'Detalles del Libro | LibreLah')

@section('content')
<div class="container py-4 mb-5">

    {{-- BOTÓN DE VOLVER --}}
    <div class="mb-4">
        <a href="{{ route('catalogo.index') }}" class="back-link d-inline-flex align-items-center fw-medium px-3 py-2 rounded-3">
            <i class="bi bi-arrow-left me-2"></i> Volver al Catálogo
        </a>
    </div>

    {{-- BLOQUE SUPERIOR: EL LIBRO --}}
    <div class="row g-4 g-lg-5 mb-5 align-items-center">

        <div class="col-md-4 col-lg-3 d-flex justify-content-center">
            @if($libro->portada)
            <img src="{{ $libro->portada }}" alt="{{ $libro->titulo }}" class="img-fluid rounded-4 book-cover-elegant w-100" style="max-width: 300px;">
            @else
            <div class="no-cover-box rounded-4 d-flex flex-column justify-content-center align-items-center w-100" style="max-width: 300px;">
                <i class="bi bi-book fs-1 mb-3 text-primary opacity-50"></i>
                <span class="small fw-bold text-muted text-uppercase tracking-wider">Sin portada</span>
            </div>
            @endif
        </div>

        <div class="col-md-8 col-lg-9 pt-2">

            @php
            // Calculamos los disponibles reales basándonos en tu lógica
            $prestados = $libro->prestamos()->where('estado', 'activo')->count();
            $disponibles = $libro->copias_totales - $prestados;
            @endphp

            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <span class="px-3 py-1 rounded-pill fw-bold d-flex align-items-center" style="font-size: 0.75rem; background-color: var(--primary-soft); color: var(--primary); border: 1px solid rgba(37,99,235,0.2);">
                    <i class="bi bi-tag-fill me-1 opacity-75"></i> {{ $libro->categoria->nombre ?? 'Sin Categoría' }}
                </span>

                @if($disponibles > 0)
                <span class="px-3 py-1 rounded-pill fw-bold d-flex align-items-center" style="font-size: 0.75rem; background-color: var(--success-soft); color: #16a34a; border: 1px solid rgba(22,163,74,0.2);">
                    <i class="bi bi-check-circle-fill me-1"></i> Disponible ({{ $disponibles }})
                </span>
                @else
                <span class="px-3 py-1 rounded-pill fw-bold d-flex align-items-center" style="font-size: 0.75rem; background-color: var(--danger-soft); color: #dc2626; border: 1px solid rgba(220,38,38,0.2);">
                    <i class="bi bi-x-circle-fill me-1"></i> Agotado
                </span>
                @endif
            </div>

            <h1 class="fw-bold mb-2 text-slate-900" style="font-size: 2.25rem; letter-spacing: -0.02em; line-height: 1.2;">
                {{ $libro->titulo }}
            </h1>
            <h4 class="mb-4 text-primary fw-medium">
                {{ $libro->autor }}
            </h4>

            <hr class="elegant-divider mb-4">

            <div class="mb-5">
                <h6 class="fw-bold mb-3 d-flex align-items-center text-slate-800">
                    <i class="bi bi-text-left text-primary me-2 fs-5"></i> Sinopsis
                </h6>
                <p class="text-slate-600" style="font-size: 1.05rem; line-height: 1.8;">
                    {{ $libro->descripcion ?? 'Sumérgete en esta obra de ' . $libro->autor . '. No hay descripción detallada disponible actualmente.' }}
                </p>
            </div>

            <div class="row g-3">
                <div class="col-sm-6 col-md-4">
                    <div class="data-card p-3 rounded-4 d-flex flex-column h-100">
                        <i class="bi bi-upc-scan fs-4 text-primary mb-2 opacity-75"></i>
                        <span class="text-muted small fw-bold text-uppercase tracking-wider mb-1">ISBN</span>
                        <span class="fw-bold text-slate-900">{{ $libro->isbn ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4">
                    <div class="data-card p-3 rounded-4 d-flex flex-column h-100">
                        <i class="bi bi-buildings fs-4 text-primary mb-2 opacity-75"></i>
                        <span class="text-muted small fw-bold text-uppercase tracking-wider mb-1">Editorial</span>
                        <span class="fw-bold text-slate-900 text-truncate w-100">{{ $libro->editorial ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="data-card p-3 rounded-4 d-flex flex-column h-100">
                        <i class="bi bi-calendar3 fs-4 text-primary mb-2 opacity-75"></i>
                        <span class="text-muted small fw-bold text-uppercase tracking-wider mb-1">Publicación</span>
                        <span class="fw-bold text-slate-900">{{ $libro->anio_publicacion ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <hr class="elegant-divider my-5">

    {{-- SECCIÓN OPINIONES --}}
    <div class="row">
        <div class="col-lg-9 text-start">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <h3 class="fw-bold m-0 text-slate-900 d-flex align-items-center">
                    <i class="bi bi-chat-quote-fill text-primary me-2"></i> Opiniones de la comunidad
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle rounded-pill ms-3 fs-6 align-middle">
                        {{ $libro->comentarios->count() }}
                    </span>
                </h3>

                @if($libro->comentarios->count() > 1)
                <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center">
                    <label for="orden" class="text-muted small me-2 text-nowrap fw-medium">
                        <i class="bi bi-sort-down me-1"></i>Ordenar:
                    </label>
                    <select name="orden" id="orden" class="form-select form-select-sm shadow-none border-secondary-subtle rounded-3" onchange="this.form.submit()" style="width: auto;">
                        <option value="recientes" {{ request('orden', 'recientes') === 'recientes' ? 'selected' : '' }}>Más recientes</option>
                        <option value="antiguos" {{ request('orden') === 'antiguos' ? 'selected' : '' }}>Más antiguas</option>
                    </select>
                </form>
                @endif
            </div>

            @auth
            @php
            $miComentario = $libro->comentarios->where('user_id', auth()->id())->first();
            @endphp

            {{-- Alerta de éxito (oculta por defecto, se muestra solo al enviar) --}}
            <div id="alerta-exito" class="success-card p-4 rounded-4 d-flex align-items-center gap-3 border border-success border-opacity-25 mb-5 d-none">
                <i class="bi bi-check-circle-fill text-success fs-2"></i>
                <div>
                    <h6 class="fw-bold text-success-emphasis mb-1">¡Reseña publicada!</h6>
                    <p class="small text-success-emphasis opacity-75 mb-0">Ya has compartido tu opinión. ¡Gracias!</p>
                </div>
            </div>

            {{-- Formulario: visible si no ha comentado, oculto si ya comentó --}}
            <div id="bloque-form" class="form-card p-4 rounded-4 shadow-sm border border-light bg-white mb-5 {{ $miComentario ? 'd-none' : '' }}">
                <h6 class="fw-bold mb-3 text-slate-900">
                    <i class="bi bi-pen-fill text-primary me-2"></i>
                    <span id="form-titulo">Escribe tu reseña</span>
                </h6>

                <form id="form-comentario" action="{{ route('comentarios.store', $libro->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="metodo-form" value="POST">

                    <div id="error-estrellas" class="text-danger small mb-2 d-none">
                        <i class="bi bi-exclamation-circle-fill me-1"></i> Debes seleccionar una puntuación.
                    </div>

                    <div class="mb-3">
                        <div class="star-rating interactive-stars">
                            <input type="radio" id="star5" name="estrellas" value="5" />
                            <label for="star5"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" id="star4" name="estrellas" value="4" />
                            <label for="star4"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" id="star3" name="estrellas" value="3" />
                            <label for="star3"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" id="star2" name="estrellas" value="2" />
                            <label for="star2"><i class="bi bi-star-fill"></i></label>
                            <input type="radio" id="star1" name="estrellas" value="1" />
                            <label for="star1"><i class="bi bi-star-fill"></i></label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <textarea id="texto-comentario" name="contenido" class="form-control custom-textarea rounded-3 p-3" rows="3"
                            placeholder="¿Qué te transmitió esta lectura?"
                            style="resize: none;"></textarea>
                        <div class="d-flex justify-content-end mt-2">
                            <span id="contador-caracteres" class="small text-muted fw-medium">0 / 1000</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" id="btn-cancelar-edicion" class="btn btn-light text-muted fw-bold rounded-pill px-4 d-none">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-submit-comentario" class="btn btn-primary-elegant rounded-pill fw-bold px-4">
                            Publicar reseña
                        </button>
                    </div>
                </form>
            </div>

            @else
            {{-- No autenticado --}}
            <div class="login-card p-4 rounded-4 text-start border border-primary border-opacity-10 mb-5 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <div>
                    <h6 class="fw-bold text-slate-900 mb-1"><i class="bi bi-lock-fill text-primary opacity-50 me-2"></i>Inicia sesión para opinar</h6>
                    <p class="small text-muted mb-0">Necesitas una cuenta para valorar y ayudar a otros lectores.</p>
                </div>
                <a href="{{ route('login') }}" class="btn btn-primary-elegant rounded-pill px-4 py-2 fw-bold">Acceder</a>
            </div>
            @endauth

            {{-- LISTADO DE OPINIONES --}}
            <div class="comentarios-lista d-flex flex-column gap-3">
                @php
                $comentariosOrdenados = $libro->comentarios;
                if(request('orden') === 'antiguos') {
                $comentariosOrdenados = $comentariosOrdenados->sortBy('created_at');
                } else {
                $comentariosOrdenados = $comentariosOrdenados->sortByDesc('created_at');
                }
                @endphp

                @forelse($comentariosOrdenados as $comentario)
                <div class="review-card p-4 rounded-4">
                    <div class="d-flex align-items-start gap-3 mb-2">
                        <x-user-avatar :user="$comentario->user" size="46px" fontSize="16px" class="shadow-sm" />

                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-slate-900">{{ $comentario->user->name }}</h6>
                                <span class="text-muted small fw-medium">{{ $comentario->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="text-primary mt-1" style="font-size: 0.85rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <=$comentario->estrellas)
                                    <i class="bi bi-star-fill"></i>
                                    @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                    @endif
                                    @endfor
                            </div>
                        </div>
                    </div>

                    @if($comentario->contenido)
                    <div class="comentario-contenedor mt-3">
                        <p class="mb-0 text-slate-600 comentario-texto text-truncate-multiline" style="font-size: 0.95rem; line-height: 1.6; word-break: break-word;">{{ $comentario->contenido }}</p>
                        <button class="btn-ver-mas btn btn-link p-0 text-primary small fw-bold text-decoration-none mt-1 d-none" onclick="toggleTexto(this)">Ver más</button>
                    </div>
                    @endif

                    @if(auth()->check() && (auth()->id() === $comentario->user_id || auth()->user()->rol === 'admin'))
                    <div class="d-flex gap-2 mt-3 pt-3 border-top-subtle">
                        @if(auth()->id() === $comentario->user_id)
                        <button type="button" class="btn-action-ghost text-muted"
                            onclick="prepararEdicion('{{ $comentario->id }}', '{{ $comentario->estrellas }}', '{{ addslashes($comentario->contenido) }}')">
                            <i class="bi bi-pencil-fill me-1"></i> Editar
                        </button>
                        @endif
                        <form action="{{ route('comentarios.destroy', $comentario->id) }}" method="POST" class="m-0"
                            onsubmit="return confirm('¿Seguro que deseas eliminar esta opinión?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action-ghost text-danger opacity-75">
                                <i class="bi bi-trash3-fill me-1"></i> Borrar
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                @empty
                <div class="empty-review-box p-5 text-center rounded-4 mt-2">
                    <i class="bi bi-stars text-primary opacity-50 mb-3 d-block" style="font-size: 2.5rem;"></i>
                    <h6 class="fw-bold text-slate-900">Aún no hay opiniones</h6>
                    <p class="text-muted small mb-0">Sé el primero en valorar este libro y ayuda a otros lectores.</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</div>

<style>
    :root {
        --primary: #3b82f6;
        --primary-hover: #2563eb;
        --primary-soft: #eff6ff;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-600: #475569;
        --bg-body: #f8fafc;
        --success-soft: #f0fdf4;
        --danger-soft: #fef2f2;
    }

    body {
        background-color: var(--bg-body);
        color: var(--slate-800);
    }

    .tracking-wider {
        letter-spacing: 0.05em;
    }

    .elegant-divider {
        border-top: 1px solid #e2e8f0;
        opacity: 1;
        margin: 0;
    }

    .border-top-subtle {
        border-top: 1px solid #f1f5f9;
    }

    .back-link {
        color: var(--slate-600);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        color: var(--primary);
        background-color: var(--primary-soft);
        transform: translateX(-3px);
    }

    .book-cover-elegant {
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .no-cover-box {
        background: linear-gradient(to bottom right, #ffffff, #f1f5f9);
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        aspect-ratio: 2/3;
    }

    .data-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .data-card:hover {
        background-color: var(--primary-soft);
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.08);
    }

    .review-card {
        background-color: #ffffff;
        border: 1px solid #f1f5f9;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .review-card:hover {
        box-shadow: 0 5px 15px rgba(15, 23, 42, 0.03);
        border-color: #e2e8f0;
    }

    .btn-action-ghost {
        background: transparent;
        border: none;
        padding: 4px 10px;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .btn-action-ghost:hover {
        background-color: #f1f5f9;
        color: var(--slate-900) !important;
    }

    .btn-action-ghost.text-danger:hover {
        background-color: var(--danger-soft);
        color: #dc2626 !important;
    }

    .btn-primary-elegant {
        background-color: var(--primary);
        color: white;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2);
    }

    .btn-primary-elegant:hover {
        background-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
    }

    .btn-primary-elegant:disabled {
        background-color: #94a3b8;
        box-shadow: none;
        transform: none;
        cursor: not-allowed;
    }

    .interactive-stars {
        direction: rtl;
        display: inline-flex;
        font-size: 2rem;
        gap: 4px;
    }

    .interactive-stars input[type=radio] {
        display: none;
    }

    .interactive-stars label {
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .interactive-stars label:hover,
    .interactive-stars label:hover~label,
    .interactive-stars input[type=radio]:checked~label {
        color: var(--primary);
    }

    .interactive-stars label:active {
        transform: scale(0.85);
    }

    .custom-textarea {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .custom-textarea:focus {
        background-color: #ffffff;
        border-color: #93c5fd;
        box-shadow: 0 0 0 4px var(--primary-soft);
        outline: none;
    }

    .empty-review-box {
        border: 2px dashed #e2e8f0;
        background-color: transparent;
    }

    .success-card {
        background-color: var(--success-soft);
    }

    .login-card {
        background-color: var(--primary-soft);
    }

    .min-w-0 {
        min-width: 0;
    }

    /* Clase nueva para recortar a 3 líneas */
    .text-truncate-multiline {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Lógica para mostrar botón de "Ver más" si el texto es muy largo
        document.querySelectorAll('.comentario-texto').forEach(function(el) {
            if (el.scrollHeight > el.clientHeight) {
                el.nextElementSibling.classList.remove('d-none');
            }
        });

        window.toggleTexto = function(btn) {
            const texto = btn.previousElementSibling;
            if (texto.classList.contains('text-truncate-multiline')) {
                texto.classList.remove('text-truncate-multiline');
                btn.innerText = 'Ver menos';
            } else {
                texto.classList.add('text-truncate-multiline');
                btn.innerText = 'Ver más';
            }
        };

        // Lógica del formulario de comentarios
        const form = document.getElementById('form-comentario');
        if (!form) return;

        const urlBase = "{{ url('/comentarios') }}";
        const urlOriginal = form.action;
        const btnSubmit = document.getElementById('btn-submit-comentario');
        const textarea = document.getElementById('texto-comentario');
        const contador = document.getElementById('contador-caracteres');
        const MAX_CANTIDAD = 1000;

        textarea.addEventListener('input', function() {
            const longitud = this.value.length;
            contador.textContent = `${longitud} / ${MAX_CANTIDAD}`;

            if (longitud > MAX_CANTIDAD) {
                contador.classList.remove('text-muted');
                contador.classList.add('text-danger', 'fw-bold');
                btnSubmit.disabled = true;
            } else {
                contador.classList.add('text-muted');
                contador.classList.remove('text-danger', 'fw-bold');
                btnSubmit.disabled = false;
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const estrellasSeleccionadas = form.querySelector('input[name="estrellas"]:checked');
            const errorEstrellas = document.getElementById('error-estrellas');

            if (!estrellasSeleccionadas) {
                errorEstrellas.classList.remove('d-none');
                return;
            }
            errorEstrellas.classList.add('d-none');

            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok && response.status !== 400 && response.status !== 403) {
                        throw new Error('Error en la solicitud al servidor.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const alerta = document.getElementById('alerta-exito');
                        const bloqueForm = document.getElementById('bloque-form');

                        if (alerta) alerta.classList.remove('d-none');
                        if (bloqueForm) bloqueForm.classList.add('d-none');

                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    } else {
                        alert(data.error || 'Hubo un error al procesar la solicitud.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error de conexión o validación en el servidor.');
                });
        });

        window.prepararEdicion = function(id, estrellas, contenido) {
            const alerta = document.getElementById('alerta-exito');
            const bloqueForm = document.getElementById('bloque-form');
            if (alerta) alerta.classList.add('d-none');
            bloqueForm.classList.remove('d-none');

            form.action = `${urlBase}/${id}`;
            document.getElementById('metodo-form').value = 'PUT';

            textarea.value = contenido;
            textarea.dispatchEvent(new Event('input'));

            document.getElementById(`star${estrellas}`).checked = true;
            document.getElementById('form-titulo').innerText = 'Editar tu reseña';
            document.getElementById('btn-submit-comentario').innerText = 'Actualizar reseña';
            document.getElementById('btn-cancelar-edicion').classList.remove('d-none');

            bloqueForm.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            textarea.focus();
        };

        document.getElementById('btn-cancelar-edicion').addEventListener('click', function() {
            const alerta = document.getElementById('alerta-exito');
            const bloqueForm = document.getElementById('bloque-form');

            if (alerta) alerta.classList.add('d-none');
            bloqueForm.classList.add('d-none');

            form.action = urlOriginal;
            document.getElementById('metodo-form').value = 'POST';

            form.reset();
            textarea.dispatchEvent(new Event('input'));

            document.getElementById('form-titulo').innerText = 'Escribe tu reseña';
            document.getElementById('btn-submit-comentario').innerText = 'Publicar reseña';
            this.classList.add('d-none');
        });
    });
</script>

@endsection