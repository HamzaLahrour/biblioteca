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


    <div class="card-body p-4">
        @if($libros->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3" style="width: 70px;">Portada</th>
                        <th scope="col" class="border-0 py-3">Título y Autor</th>
                        <th scope="col" class="border-0 py-3">ISBN</th>
                        <th scope="col" class="border-0 py-3">Categoría</th>
                        <th scope="col" class="border-0 py-3 text-center">Copias</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($libros as $libro)
                    <tr>
                        <td class="py-3">
                            @if($libro->portada)
                            <img src="{{ $libro->portada }}" alt="Portada de {{ $libro->titulo }}" class="rounded shadow-sm" style="width: 45px; height: 65px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex justify-content-center align-items-center border rounded shadow-sm" style="width: 45px; height: 65px;">
                                <i class="bi bi-journal-text text-muted fs-5"></i>
                            </div>
                            @endif
                        </td>

                        <td class="py-3">
                            <div class="fw-bold text-dark text-wrap" style="max-width: 250px;">{{ $libro->titulo }}</div>
                            <div class="text-muted small"><i class="bi bi-person-fill me-1"></i>{{ $libro->autor }}</div>
                        </td>

                        <td class="py-3">
                            @if($libro->isbn)
                            <span class="font-monospace text-muted small">{{ $libro->isbn }}</span>
                            @else
                            <span class="badge bg-light text-secondary border">Sin ISBN</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info-subtle">
                                <i class="bi bi-bookmark-fill me-1"></i>
                                {{ $libro->categoria->nombre }}
                            </span>
                        </td>

                        <td class="text-center py-3">
                            <span class="badge rounded-pill {{ $libro->copias_totales > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $libro->copias_totales }}
                            </span>
                        </td>

                        <td class="text-end py-3">
                            <div class="btn-group shadow-sm" role="group">
                                <a href="{{ route('libros.show', $libro->id) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('libros.edit', $libro->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('libros.destroy', $libro->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este libro del catálogo?');">
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
            {{ $libros->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bookshelf fs-1 d-block mb-3 text-secondary"></i>
            <h5 class="fw-bold text-dark">No hay libros en el catálogo</h5>
            <p>Empieza añadiendo tu primer libro de forma manual o usando la API de Google Books.</p>
        </div>
        @endif
    </div>
</div>
@endsection