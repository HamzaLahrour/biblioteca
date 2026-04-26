@extends('layouts.admin')

@section('title', 'Gestión de Reservas')

@section('content')


{{-- 🔍 PANEL DE FILTROS --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('reservas.index') }}" method="GET">
            <div class="row g-3">

                {{-- Filtro: Usuario --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Buscar Alumno</label>
                    <input type="text" name="buscar_usuario" class="form-control" placeholder="Nombre o email..." value="{{ request('buscar_usuario') }}">
                </div>

                {{-- Filtro: Espacio --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Espacio</label>
                    <select name="espacio_id" class="form-select">
                        <option value="">Todas las salas</option>
                        @foreach($espacios as $espacio)
                        <option value="{{ $espacio->id }}" {{ request('espacio_id') == $espacio->id ? 'selected' : '' }}>
                            {{ $espacio->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro: Estado --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                        <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                {{-- Filtro: Rango de Fechas --}}
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                </div>

                {{-- Botones de Acción --}}
                <div class="col-md-1 d-flex align-items-end">
                    <div class="d-grid w-100 gap-2">
                        <button type="submit" class="btn btn-dark" title="Buscar"><i class="bi bi-search"></i></button>
                        @if(request()->anyFilled(['buscar_usuario', 'espacio_id', 'estado', 'fecha_inicio', 'fecha_fin']))
                        <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- TABLA ORIGINAL INTACTA --}}
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-calendar-check me-2"></i>Listado de Reservas
        </h5>
        <a href="{{ route('reservas.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium">
            <i class="bi bi-plus-circle me-1"></i> Nueva Reserva
        </a>
    </div>

    <div class="card-body p-4">
        @if($reservas->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"> {{-- Nota menor: cambié <header> a <thead> porque HTML lo exige en tablas, el resto está igual --}}
                    <tr>
                        <th scope="col" class="border-0 rounded-start-3 py-3">Fecha</th>
                        <th scope="col" class="border-0 py-3">Espacio</th>
                        <th scope="col" class="border-0 py-3">Horario</th>
                        <th scope="col" class="border-0 py-3 text-center">Estado</th>
                        <th scope="col" class="border-0 rounded-end-3 py-3 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($reservas as $reserva)
                    <tr>
                        <td class="fw-medium text-dark py-3">
                            <i class="bi bi-calendar3 text-muted me-1"></i>
                            {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}
                        </td>
                        <td class="py-3">
                            {{ $reserva->espacio->nombre ?? 'Espacio no encontrado' }}
                        </td>
                        <td class="py-3 text-muted">
                            <i class="bi bi-clock me-1"></i>
                            {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                        </td>
                        <td class="py-3 text-center">
                            @if($reserva->estado === 'activa')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2">
                                Activa
                            </span>
                            @elseif($reserva->estado === 'finalizada')
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                Finalizada
                            </span>
                            @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2">
                                Cancelada
                            </span>
                            @endif
                        </td>
                        <td class="text-end py-3">
                            <div class="btn-group shadow-sm" role="group">
                                <a href="{{ route('reservas.show', $reserva->id) }}"
                                    class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                    <i class="bi bi-eye-fill me-1"></i> Ver
                                </a>
                                @if($reserva->estado === 'activa' && !\Carbon\Carbon::parse($reserva->fecha . ' ' . $reserva->hora_inicio)->isPast())
                                <form action="{{ route('reservas.destroy', $reserva->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta reserva? Liberarás el hueco.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar Reserva">
                                        <i class="bi bi-x-circle-fill me-1"></i> Cancelar
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" disabled title="No disponible">
                                    <i class="bi bi-dash-circle"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            {{ $reservas->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-3 text-secondary"></i>
            <h5 class="fw-bold text-dark">No hay ninguna reserva registrada.</h5>
            <p>Aún no has realizado ninguna reserva en los espacios de la biblioteca.</p>
            <a href="{{ route('reservas.create') }}" class="btn btn-outline-primary rounded-pill px-4 mt-2">
                Hacer mi primera reserva
            </a>
        </div>
        @endif
    </div>
</div>
@endsection