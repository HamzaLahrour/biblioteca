@extends('layouts.admin')

@section('title', 'Gestión de Categorías')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-tags me-2"></i>Listado de Categorías
        </h5>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Nueva Categoría
        </a>
    </div>

    <div class="card-body p-4">
        @if($categorias->count() > 0)
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
                        @foreach($categorias as $categoria)
                            <tr>
                                <td class="fw-medium text-dark py-3">{{ $categoria->nombre }}</td>
                                <td class="text-muted py-3">
                                    {{ Str::limit($categoria->descripcion, 60) ?: 'Sin descripción' }}
                                </td>
                                <td class="text-end py-3">
                                    <div class="btn-group shadow-sm" role="group">
                                        <a href="{{ route('categorias.show', $categoria->id) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
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
                {{ $categorias->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                <h5 class="fw-bold text-dark">No hay categorías registradas</h5>
                <p>Empieza añadiendo tu primera categoría para organizar los libros de la biblioteca.</p>
            </div>
        @endif
    </div>
</div>
@endsection