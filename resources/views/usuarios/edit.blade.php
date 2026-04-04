@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        <div class="mb-3">
            <a href="{{ route('usuarios.index') }}" class="text-decoration-none" style="color: var(--text-muted); font-weight: 500;">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                <h4 class="mb-0" style="color: var(--secondary-dark); font-weight: 700;">
                    <i class="bi bi-person-lines-fill me-2" style="color: var(--primary);"></i>Editar Perfil
                </h4>
                <p class="text-muted mt-1 mb-0" style="font-size: 0.9rem;">
                    Modificando los datos de <strong>{{ $usuario->name }}</strong>.
                </p>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold text-dark">Nombre completo <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control form-control-lg @error('name') is-invalid @enderror" value="{{ old('name', $usuario->name) }}">
                        @error('name') <div class="invalid-feedback fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="dni" class="form-label fw-bold text-dark">DNI / NIE <span class="text-danger">*</span></label>
                        <input type="text" name="dni" id="dni" class="form-control form-control-lg @error('dni') is-invalid @enderror" value="{{ old('dni', $usuario->dni) }}">
                        @error('dni') <div class="invalid-feedback fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold text-dark">Correo electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}">
                        @error('email') <div class="invalid-feedback fw-medium">{{ $message }}</div> @enderror
                    </div>



                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold text-dark">
                            Contraseña de acceso <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="text" name="password" id="password"
                                class="form-control form-control-lg @error('password') is-invalid @enderror"
                                placeholder="Escribe solo si quieres cambiarla">
                            <button type="button" class="btn btn-outline-secondary" id="btnGenerar">
                                <i class="bi bi-arrow-clockwise me-1"></i> Generar
                            </button>
                        </div>
                        <div class="form-text text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Déjalo en blanco para mantener la contraseña actual de este usuario.
                        </div>
                        @error('password')
                        <div class="invalid-feedback fw-medium d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="fecha_nacimiento" class="form-label fw-bold text-dark">Fecha de nacimiento <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control form-control-lg @error('fecha_nacimiento') is-invalid @enderror"
                            value="{{ old('fecha_nacimiento', $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('Y-m-d') : '') }}">
                        @error('fecha_nacimiento') <div class="invalid-feedback fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="telefono" class="form-label fw-bold text-dark">Teléfono de contacto</label>
                        <input type="text" name="telefono" id="telefono" class="form-control form-control-lg @error('telefono') is-invalid @enderror" value="{{ old('telefono', $usuario->telefono) }}">
                        @error('telefono') <div class="invalid-feedback fw-medium">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('usuarios.index') }}" class="btn btn-light px-4 fw-medium">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm fw-medium">
                            <i class="bi bi-save me-1"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.getElementById('btnGenerar').addEventListener('click', function() {
        fetch('{{ route("generar.password") }}')
            .then(res => res.json())
            .then(data => {
                document.getElementById('password').value = data.password;
            });
    });
</script>
@endpush
@endsection