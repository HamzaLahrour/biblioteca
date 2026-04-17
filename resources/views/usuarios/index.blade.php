@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-people-fill me-2 text-primary"></i>Listado de Usuarios
        </h5>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario
        </a>
    </div>

    <div class="card-body p-4 pt-3">

        {{-- 🔍 BARRA DE FILTROS (Limpia y útil) --}}
        <div class="bg-light p-3 rounded-4 mb-4 border">
            <form action="{{ route('usuarios.index') }}" method="GET" class="row g-2 align-items-center">

                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre, email o DNI..." value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- Filtro de Estado del Lector (La paranoia útil) --}}
                <div class="col-md-4">
                    <select name="estado_lector" class="form-select form-select-sm">
                        <option value="">Todos los usuarios</option>
                        <option value="con_prestamos" {{ request('estado_lector') == 'con_prestamos' ? 'selected' : '' }}>Con libros prestados actualmente</option>
                        <option value="sancionados" {{ request('estado_lector') == 'sancionados' ? 'selected' : '' }}>Sancionados (Morosos)</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-medium shadow-sm">Filtrar</button>
                    @if(request()->anyFilled(['buscar', 'estado_lector']))
                    <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLA DE DATOS CLÁSICA --}}
        @if($usuarios->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3" style="width: 50px;"></th>
                        <th scope="col" class="border-0 py-3">Nombre y Contacto</th>
                        <th scope="col" class="border-0 py-3">DNI</th>
                        <th scope="col" class="border-0 py-3 text-center">Edad</th>
                        <th scope="col" class="border-0 py-3">Rol</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($usuarios as $usuario)
                    <tr>
                        {{-- AVATAR --}}
                        <td class="py-3 text-center">
                            @if($usuario->rol === 'admin')
                            <i class="bi bi-person-badge-fill fs-3 text-primary"></i>
                            @else
                            @php
                            $palabras = explode(' ', $usuario->name);
                            $iniciales = '';
                            foreach ($palabras as $palabra) {
                            if(!empty($palabra)) $iniciales .= strtoupper($palabra[0]);
                            }
                            $iniciales = substr($iniciales, 0, 2);
                            $colores = ['#4A90D9', '#E67E22', '#2ECC71', '#9B59B6', '#E74C3C', '#1ABC9C', '#F39C12', '#3498DB'];
                            $color = $colores[ord(strtoupper($usuario->name[0] ?? 'U')) % count($colores)];
                            @endphp
                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: {{ $color }}; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: white; margin: 0 auto;">
                                {{ $iniciales }}
                            </div>
                            @endif
                        </td>

                        {{-- NOMBRE Y CONTACTO --}}
                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ $usuario->name }}</div>
                            <div class="text-muted small">
                                <i class="bi bi-envelope-at me-1"></i>{{ $usuario->email }}
                                @if($usuario->telefono)
                                <span class="ms-2"><i class="bi bi-telephone me-1"></i>{{ $usuario->telefono }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- DNI --}}
                        <td class="py-3">
                            <span class="font-monospace text-muted small bg-light px-2 py-1 rounded border">
                                {{ $usuario->dni ?? '---' }}
                            </span>
                        </td>

                        {{-- EDAD --}}
                        <td class="py-3 text-center text-muted">
                            {{ $usuario->edad ? $usuario->edad . ' años' : '---' }}
                        </td>

                        {{-- ROL --}}
                        <td class="py-3">
                            @if($usuario->rol === 'admin')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">
                                <i class="bi bi-shield-lock-fill me-1"></i>Admin
                            </span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3">
                                <i class="bi bi-person-fill me-1"></i>Usuario
                            </span>
                            @endif
                        </td>

                        {{-- ACCIONES --}}
                        <td class="text-end py-3">
                            <div class="btn-group shadow-sm" role="group">
                                <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" {{ auth()->id() === $usuario->id ? 'disabled' : '' }}>
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            {{ $usuarios->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>

        @else
        <div class="text-center py-5 text-muted bg-light rounded-4 border border-dashed my-3">
            <i class="bi bi-people fs-1 d-block mb-3 text-secondary opacity-50"></i>
            <h5 class="fw-bold text-dark">No se encontraron usuarios</h5>
            <p>Prueba a cambiar los filtros o añade un nuevo usuario al sistema.</p>
            @if(request()->anyFilled(['buscar', 'estado_lector']))
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-primary rounded-pill mt-2">Limpiar Filtros</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection