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

    <div class="card-body p-4 pt-3">

        {{-- 🔍 BARRA DE FILTROS TÁCTICOS --}}
        <div class="bg-light p-3 rounded-4 mb-4 border">
            <form action="{{ route('espacios.index') }}" method="GET" class="row g-2 align-items-center">

                {{-- Buscador de Texto --}}
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre, código o ubicación..." value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- Filtro de Tipo --}}
                <div class="col-md-3">
                    <select name="tipo_espacio_id" class="form-select form-select-sm">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ request('tipo_espacio_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro de Estado (Disponibilidad) --}}
                <div class="col-md-2">
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Cualquier estado</option>
                        <option value="disponible" {{ request('estado') === 'disponible' ? 'selected' : '' }}>Operativos</option>
                        <option value="mantenimiento" {{ request('estado') === 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-medium shadow-sm">Filtrar</button>
                    @if(request()->anyFilled(['buscar', 'tipo_espacio_id', 'estado']))
                    <a href="{{ route('espacios.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLA DE DATOS --}}
        @if($espacios->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3">Nombre</th>
                        <th scope="col" class="border-0 py-3">Código</th>
                        <th scope="col" class="border-0 py-3">Ubicación</th>
                        <th scope="col" class="border-0 py-3">Tipo</th>
                        <th scope="col" class="border-0 py-3">Capacidad</th>
                        <th scope="col" class="border-0 py-3 text-center">Estado</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($espacios as $espacio)
                    <tr>
                        <td class="fw-bold text-dark py-3">{{ $espacio->nombre }}</td>
                        <td class="py-3"><span class="font-monospace text-muted small bg-light px-2 py-1 rounded border">{{ $espacio->codigo }}</span></td>
                        <td class="text-muted py-3">{{ $espacio->ubicacion }}</td>

                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle px-2 py-1">
                                <i class="bi bi-tag-fill me-1"></i> {{ $espacio->tipoEspacio->nombre ?? 'Sin tipo' }}
                            </span>
                        </td>

                        <td class="text-muted py-3"><i class="bi bi-people me-1"></i>{{ $espacio->capacidad }} pax.</td>

                        <td class="text-center py-3">
                            @if($espacio->disponible)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Disponible
                            </span>
                            @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                <i class="bi bi-tools me-1"></i>Mantenimiento
                            </span>
                            @endif
                        </td>

                        <td class="text-end py-3">
                            <div class="btn-group shadow-sm" role="group">
                                <a href="{{ route('espacios.show', $espacio->id) }}" class="btn btn-sm btn-light border text-primary" title="Ver detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('espacios.edit', $espacio->id) }}" class="btn btn-sm btn-light border text-secondary" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('espacios.destroy', $espacio->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este espacio?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger" title="Eliminar">
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
            {{-- appends() guarda la búsqueda al cambiar de página --}}
            {{ $espacios->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @else
        {{-- ESTADO VACÍO (Con o sin filtros) --}}
        <div class="text-center py-5 text-muted bg-light rounded-4 border border-dashed my-3">
            <i class="bi bi-inbox fs-1 d-block mb-3 text-secondary opacity-50"></i>
            <h5 class="fw-bold text-dark">No se encontraron espacios</h5>
            <p>Prueba a cambiar los filtros o añade un nuevo espacio al sistema.</p>
            @if(request()->anyFilled(['buscar', 'tipo_espacio_id', 'estado']))
            <a href="{{ route('espacios.index') }}" class="btn btn-outline-primary rounded-pill mt-2">Limpiar Filtros</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection