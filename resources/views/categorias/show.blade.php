@extends('layouts.admin')

@section('title', 'Detalles de la Categoría')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('categorias.index') }}" class="text-decoration-none" style="color: var(--text-muted); font-weight: 500;">
                <i class="bi bi-arrow-left me-1"></i> Volver al listado
            </a>

            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Editar Categoría
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border-left: 5px solid var(--primary) !important;">
            <div class="card-body p-4">
                <h3 style="color: var(--secondary-dark); font-weight: 700;" class="mb-2">
                    {{ $categoria->nombre }}
                </h3>
                <p class="text-muted mb-0 fs-6">
                    {{ $categoria->descripcion ?: 'No hay descripción disponible para esta categoría.' }}
                </p>
                <hr class="my-3 text-muted">
                <div class="d-flex align-items-center" style="color: var(--text-muted); font-size: 0.9rem;">
                    <i class="bi bi-book-half me-2" style="color: var(--secondary-light);"></i>
                    <strong>Total de libros asociados:</strong> <span class="badge bg-light text-dark border ms-2">{{ $categoria->libros->count() }}</span>
                </div>
            </div>
        </div>

        <h5 class="mb-3 mt-4" style="color: var(--text-main); font-weight: 600;">
            <i class="bi bi-collection me-2 text-primary"></i>Libros en esta categoría
        </h5>

        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-0">

                @if($categoria->libros->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="border-0 ps-4 py-3">Título</th>
                                <th scope="col" class="border-0 py-3">Autor</th>
                                <th scope="col" class="border-0 py-3">ISBN</th>
                                <th scope="col" class="border-0 py-3">Estado</th>
                                <th scope="col" class="border-0 pe-4 py-3 text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @foreach($categoria->libros as $libro)
                            <tr>
                                <td class="fw-medium text-dark ps-4 py-3">{{ $libro->titulo }}</td>
                                <td class="text-muted py-3">{{ $libro->autor }}</td>
                                <td class="text-muted py-3">{{ $libro->isbn }}</td>
                                <td class="py-3">
                                    @if($libro->copias_disponibles > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Disponible ({{ $libro->copias_disponibles }})</span>
                                    @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Agotado</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <a href="{{ route('libros.show', $libro->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm">
                                        Ver Libro
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-journal-x d-block mb-3" style="font-size: 3rem; color: #e0e0e0;"></i>
                    <h6 style="color: var(--text-main); font-weight: 600;">Aún no hay libros aquí</h6>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">
                        Cuando añadas libros al catálogo y les asignes la categoría "{{ $categoria->nombre }}", aparecerán en esta lista.
                    </p>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection