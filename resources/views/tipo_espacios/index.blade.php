@extends('layouts.admin')

@section('title', 'Gestión de Tipos de Espacios')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-tags me-2"></i>Listado de Tipos de Espacios
        </h5>
        <a href="{{ route('tipos_espacios.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Tipo de Espacio
        </a>
    </div>

    <div class="card-body p-4">
        @if($tipoEspacios->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0 rounded-start-3 py-3">Nombre</th>
                            <th scope="col" class="border-0 py-3">Descripción</th>
                            <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($tipoEspacios as $tipoEspacio)
                            <tr>
                                <td class="fw-medium text-dark py-3">{{ $tipoEspacio->nombre }}</td>
                                <td class="text-muted py-3">
                                    {{ Str::limit($tipoEspacio->descripcion, 60) ?: 'Sin descripción' }}
                                </td>
                                <td class="text-end py-3">
                                    <div class="btn-group shadow-sm" role="group">
                                        <a href="{{ route('tipos_espacios.show', $tipoEspacio->id) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('tipos_espacios.edit', $tipoEspacio->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('tipos_espacios.destroy', $tipoEspacio->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
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
                {{ $tipoEspacios->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                <h5 class="fw-bold text-dark">No hay ningún Tipo de Espacio registrado.</h5>
                <p>Añade los tipos de espacio que existen en tu biblioteca (por ejemplo, estudio, informática o lectura) para estructurar y gestionar correctamente cada área.</p>
            </div>
        @endif
    </div>
</div>
@endsection