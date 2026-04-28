@extends('layouts.admin')

@section('title', 'Gestión de Festivos')

@section('content')
<div class="container-fluid py-3">
    <div class="row g-4">

        {{-- COLUMNA IZQUIERDA: Formulario de Registro --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">Nuevo Festivo</h5>
                    <p class="text-muted small">El sistema bloqueará las reservas en esta fecha.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="{{ route('festivos.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Fecha del Festivo</label>
                            <input type="date" name="fecha" class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Motivo / Nombre</label>
                            <input type="text" name="motivo" class="form-control bg-light border-0 py-2" placeholder="Ej: Navidad" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            <i class="bi bi-plus-lg me-2"></i>Añadir al Calendario
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Listado de Festivos --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0 text-dark">Días Marcados</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 text-muted small fw-bold">FECHA</th>
                                    <th class="border-0 text-muted small fw-bold">MOTIVO</th>
                                    <th class="border-0 text-end pe-4 text-muted small fw-bold">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($festivos as $festivo)
                                <tr>
                                    <td class="ps-4 fw-bold">
                                        {{ \Carbon\Carbon::parse($festivo->fecha)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="text-secondary small">{{ $festivo->motivo }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('festivos.destroy', $festivo->id) }}" method="POST" onsubmit="return confirm('¿Seguro que quieres eliminar este festivo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger bg-danger bg-opacity-10 rounded-3">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-check fs-1 opacity-25 d-block mb-2"></i>
                                        No hay días festivos registrados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection