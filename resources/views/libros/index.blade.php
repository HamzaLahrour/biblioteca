@extends('layouts.admin')

@section('title', 'Gestión de Libros')

@section('content')
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-book-half me-2"></i>Catálogo de Libros
        </h5>
        <a href="{{ route('libros.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Añadir Libro
        </a>
    </div>

    <div class="card-body p-4 pt-3">

        {{-- 🔍 BARRA DE FILTROS TÁCTICOS --}}
        <div class="bg-light p-3 rounded-4 mb-4 border">
            <form action="{{ route('libros.index') }}" method="GET" class="row g-2 align-items-center">

                {{-- Buscador de Texto Libre --}}
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por título, autor o ISBN..." value="{{ request('buscar') }}">
                    </div>
                </div>

                {{-- Filtro de Categoría --}}
                <div class="col-md-3">
                    <select name="categoria_id" class="form-select form-select-sm">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro de Estado Operativo --}}
                <div class="col-md-2">
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Operativos</option>
                        <option value="en_reparacion" {{ request('estado') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                        <option value="descatalogado" {{ request('estado') == 'descatalogado' ? 'selected' : '' }}>Descatalogados</option>
                    </select>
                </div>

                {{-- Botones de Acción --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-medium shadow-sm">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['buscar', 'categoria_id', 'estado']))
                    <a href="{{ route('libros.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLA DE DATOS --}}
        @if($libros->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3" style="width: 70px;">Portada</th>
                        <th scope="col" class="border-0 py-3">Título y Autor</th>
                        <th scope="col" class="border-0 py-3">Inventario</th>
                        <th scope="col" class="border-0 py-3 text-center">Estado</th>
                        <th scope="col" class="border-0 py-3 text-center">Stock Real</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($libros as $libro)
                    <tr>
                        {{-- PORTADA --}}
                        <td class="py-3">
                            @if($libro->portada)
                            <img src="{{ $libro->portada }}" alt="Portada" class="rounded shadow-sm border" style="width: 45px; height: 65px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex justify-content-center align-items-center border rounded shadow-sm" style="width: 45px; height: 65px;">
                                <i class="bi bi-journal-text text-muted fs-5"></i>
                            </div>
                            @endif
                        </td>

                        {{-- TÍTULO Y AUTOR --}}
                        <td class="py-3">
                            <div class="fw-bold text-dark text-wrap" style="max-width: 250px;">{{ $libro->titulo }}</div>
                            <div class="text-muted small mt-1"><i class="bi bi-person me-1"></i>{{ $libro->autor }}</div>
                        </td>

                        {{-- INVENTARIO (ISBN + Categoría) --}}
                        <td class="py-3">
                            <div class="mb-1">
                                @if($libro->isbn)
                                <span class="font-monospace text-muted small"><i class="bi bi-upc-scan me-1"></i>{{ $libro->isbn }}</span>
                                @else
                                <span class="badge bg-light text-secondary border small">Sin ISBN</span>
                                @endif
                            </div>
                            <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle">
                                {{ $libro->categoria->nombre ?? 'Sin Categoría' }}
                            </span>
                        </td>

                        {{-- ESTADO OPERATIVO --}}
                        <td class="py-3 text-center">
                            @if($libro->estado === 'disponible')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Operativo</span>
                            @elseif($libro->estado === 'en_reparacion')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2"><i class="bi bi-tools me-1"></i>Taller</span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2">Baja</span>
                            @endif
                        </td>

                        {{-- COPIAS / STOCK --}}
                        <td class="text-center py-3">
                            @php
                            // Si ya implementaste el Accessor del Caso 1, esto brillará.
                            $disponibles = $libro->disponibles ?? $libro->copias_totales;
                            @endphp

                            <div class="d-flex flex-column align-items-center">
                                <span class="badge rounded-pill fs-6 px-3 py-2 shadow-sm border {{ 
                                    $disponibles <= 0 ? 'bg-danger text-white border-danger' : 
                                    ($disponibles <= 2 ? 'bg-warning text-dark border-warning' : 'bg-light text-dark') 
                                }}">
                                    {{ $disponibles }} <span class="fw-normal text-muted" style="font-size: 0.8em;">/ {{ $libro->copias_totales }}</span>
                                </span>
                                @if($disponibles <= 0)
                                    <span class="text-danger small fw-bold mt-1" style="font-size: 0.7rem;">AGOTADO</span>
                                    @endif
                            </div>
                        </td>

                        {{-- ACCIONES --}}
                        <td class="text-end py-3">
                            <div class="btn-group shadow-sm" role="group">
                                <a href="{{ route('libros.show', $libro->id) }}" class="btn btn-sm btn-light border text-primary" title="Ver detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('libros.edit', $libro->id) }}" class="btn btn-sm btn-light border text-secondary" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('libros.destroy', $libro->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro?');">
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
            {{-- appends() asegura que al cambiar de página no se pierdan los filtros --}}
            {{ $libros->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>

        @else
        <div class="text-center py-5 my-3 text-muted bg-light rounded-4 border border-dashed">
            <i class="bi bi-search fs-1 d-block mb-3 text-secondary opacity-50"></i>
            <h5 class="fw-bold text-dark">No se encontraron resultados</h5>
            <p>Prueba a cambiar los filtros de búsqueda o limpia el formulario.</p>
            @if(request()->anyFilled(['buscar', 'categoria_id', 'estado']))
            <a href="{{ route('libros.index') }}" class="btn btn-outline-primary rounded-pill mt-2">Limpiar Filtros</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection