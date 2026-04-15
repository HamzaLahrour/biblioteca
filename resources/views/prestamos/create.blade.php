@extends('layouts.admin')

@section('title', 'Nuevo Préstamo')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">

        <div class="mb-3">
            <a href="{{ route('prestamos.index') }}" class="btn btn-sm btn-light rounded-pill shadow-sm border text-muted fw-medium">
                <i class="bi bi-arrow-left me-1"></i> Volver a Préstamos
            </a>
        </div>

        {{-- Alertas de Error del Backend --}}
        @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h4 class="mb-0 fw-bold" style="color: var(--secondary-dark);">
                    <i class="bi bi-book-half me-2 text-primary"></i>Registrar Préstamo
                </h4>
                <p class="text-muted small mt-1 mb-0">Selecciona el usuario y el ejemplar a prestar.</p>
            </div>

            <div class="card-body p-4 pt-3">
                <form action="{{ route('prestamos.store') }}" method="POST">
                    @csrf

                    {{-- PASO 1: EL USUARIO --}}
                    <div class="mb-4">
                        <label for="user_id" class="form-label fw-bold text-dark">1. Lector (Alumno/Profesor) <span class="text-danger">*</span></label>
                        <select name="user_id" id="buscador-usuarios" class="form-select form-select-lg" placeholder="🔍 Buscar por nombre, email o DNI..." required>
                            <option value="">Selecciona un lector...</option>
                            @foreach($usuarios as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->nombre }} {{ $user->apellidos }} ({{ $user->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PASO 2: EL LIBRO --}}
                    <div class="mb-4 p-3 rounded-4 border bg-light">
                        <label for="libro_id" class="form-label fw-bold text-dark">2. Libro a Prestar <span class="text-danger">*</span></label>
                        <select name="libro_id" id="buscador-libros" class="form-select form-select-lg" placeholder="🔍 Buscar por título, autor o ISBN..." required>
                            <option value="">Selecciona un libro...</option>
                            @foreach($libros as $libro)
                            {{-- Solo mostramos libros con stock para no frustrar al admin --}}
                            @if($libro->copias_totales > 0 && $libro->estado !== 'en_reparacion')
                            <option value="{{ $libro->id }}" {{ old('libro_id') == $libro->id ? 'selected' : '' }}>
                                {{ $libro->titulo }} - {{ $libro->autor }} (Stock: (Stock: {{ $libro->disponibles }} / {{ $libro->copias_totales }}))
                            </option>
                            @endif
                            @endforeach
                        </select>
                        @error('libro_id')
                        <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PASO 3: LA FECHA --}}
                    <div class="mb-4">
                        <label for="fecha_devolucion_prevista" class="form-label fw-bold text-dark">3. Fecha de Devolución <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar-event"></i></span>

                            {{-- MAGIA: Usamos la variable $fechaPorDefecto que calculó el Controlador --}}
                            <input type="date" name="fecha_devolucion_prevista" id="fecha_devolucion_prevista"
                                class="form-control form-control-lg border-start-0 ps-0 @error('fecha_devolucion_prevista') is-invalid @enderror"
                                value="{{ old('fecha_devolucion_prevista', $fechaPorDefecto) }}"
                                min="{{ now()->addDay()->format('Y-m-d') }}" required>
                        </div>

                        {{-- MAGIA: El texto también es dinámico ahora --}}
                        <div class="form-text small">El sistema calcula {{ $diasPrestamo }} días por defecto según la configuración, pero puedes modificarlo.</div>

                        @error('fecha_devolucion_prevista')
                        <div class="text-danger small mt-1 fw-medium">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
                        <a href="{{ route('prestamos.index') }}" class="btn btn-light px-4 fw-medium">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm fw-medium">
                            <i class="bi bi-check2-circle me-1"></i> Confirmar Préstamo
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Buscador de Usuarios
        new TomSelect("#buscador-usuarios", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            maxOptions: 50,
            highlight: true
        });

        // Buscador de Libros
        new TomSelect("#buscador-libros", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            maxOptions: 50,
            highlight: true
        });
    });
</script>
@endpush