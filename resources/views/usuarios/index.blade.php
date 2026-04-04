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

    <div class="card-body p-4">
        @if($usuarios->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
                        <td class="py-3 text-center">
                            @if($usuario->rol === 'admin')
                            <i class="bi bi-person-badge-fill fs-3 text-primary"></i>
                            @else
                            @php

                            $palabras = explode(' ', $usuario->name);

                            $iniciales = '';

                            foreach ($palabras as $palabra) {
                            $iniciales .= strtoupper($palabra[0]);
                            }

                            $iniciales = substr($iniciales, 0, 2);

                            $colores = [
                            '#4A90D9', '#E67E22', '#2ECC71', '#9B59B6',
                            '#E74C3C', '#1ABC9C', '#F39C12', '#3498DB'
                            ];

                            $color = $colores[ord($usuario->name[0]) % count($colores)];
                            @endphp

                            <div style="
                                width: 40px; height: 40px;
                                border-radius: 50%;
                                background-color: {{ $color }};
                                display: flex; align-items: center; justify-content: center;
                                font-size: 14px; font-weight: 600; color: white;
                                margin: 0 auto;
                                ">
                                {{ $iniciales }}
                            </div>
                            @endif
                        </td>

                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ $usuario->name }}</div>
                            <div class="text-muted small">
                                <i class="bi bi-envelope-at me-1"></i>{{ $usuario->email }}
                                @if($usuario->telefono)
                                <span class="ms-2"><i class="bi bi-telephone me-1"></i>{{ $usuario->telefono }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="py-3">
                            <span class="font-monospace text-muted small bg-light px-2 py-1 rounded border">
                                {{ $usuario->dni }}
                            </span>
                        </td>

                        <td class="py-3 text-center text-muted">
                            {{ $usuario->edad ? $usuario->edad . ' años' : '---' }}
                        </td>

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
            {{ $usuarios->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 d-block mb-3 text-secondary"></i>
            <h5 class="fw-bold text-dark">No hay usuarios registrados</h5>
            <p>Empieza añadiendo al primer administrador o usuario del sistema.</p>
        </div>
        @endif
    </div>
</div>
@endsection