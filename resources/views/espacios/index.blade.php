@extends('layouts.admin')

@section('title', 'Gestión de Espacios')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-tags me-2"></i>Listado de Espacios
        </h5>
        <a href="{{ route('espacios.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Espacio
        </a>
    </div>

    <div class="card-body p-4">
        @if($espacios->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="border-0 rounded-start-3 py-3">Nombre</th>
                            <th scope="col" class="border-0 py-3">Código</th>
                            <th scope="col" class="border-0 py-3">Ubicación</th>
                            <th scope="col" class="border-0 py-3">Tipo</th>
                            <th scope="col" class="border-0 py-3">Capacidad</th>
                            <th scope="col" class="border-0 py-3">Estado</th>
                            <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($espacios as $espacio)
                            <tr>
                                <td class="fw-medium text-dark py-3">{{ $espacio->nombre }}</td>
                                <td class="text-muted py-3">{{ $espacio->codigo }}</td>
                                <td class="text-muted py-3">{{ $espacio->ubicacion }}</td>
                                
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-tag-fill me-1"></i> 
                                        {{ $espacio->tipoEspacio->nombre }}
                                    </span>
                                </td>

                                <td class="text-muted py-3">{{ $espacio->capacidad }} pax.</td>
                                
                                <td>
                                    @if($espacio->disponible)
                                        <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Disponible</span>
                                    @else
                                        <span class="badge text-bg-danger"><i class="bi bi-tools me-1"></i>Mantenimiento</span>
                                    @endif
                                </td>
                                
                                <td class="text-end py-3">
                                    <div class="btn-group shadow-sm" role="group">
                                        <a href="{{ route('espacios.show', $espacio->id) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('espacios.edit', $espacio->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('espacios.destroy', $espacio->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este espacio?');">
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
                {{ $espacios->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary"></i>
                <h5 class="fw-bold text-dark">No hay espacios registrados</h5>
                <p>Empieza añadiendo tu primer espacio para ver las instalaciones de la biblioteca.</p>
            </div>
        @endif
    </div>
</div>
@endsection