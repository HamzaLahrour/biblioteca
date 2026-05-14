@extends('layouts.app')

@section('title', 'Historial de Préstamos | LibreLah')

@section('content')
<div class="container py-5 mb-5" style="max-width: 900px;">

    <div class="mb-5">
        <a href="{{ route('perfil.index') }}" class="btn bg-white rounded-pill shadow-sm mb-3 px-3 py-2 d-inline-flex align-items-center fw-bold transition-hover" style="color: #0D47A1; border: 1px solid rgba(30, 144, 255, 0.15);">
            <i class="bi bi-arrow-left-short fs-5 me-1" style="color: #1E90FF;"></i>
            <span style="font-size: 0.85rem; letter-spacing: 0.3px;">Volver a Mi Espacio</span>
        </a>

        <h2 class="fw-bold mb-1 mt-2" style="color: #0D47A1; letter-spacing: -0.5px;">
            Historial de <span style="color: #1E90FF;">Préstamos</span>
        </h2>
        <p class="text-muted small">Consulta todos los libros que has leído y tus devoluciones pendientes.</p>
    </div>

    @if($prestamos->count() > 0)
    <div class="d-flex flex-column gap-4">
        @foreach($prestamos as $prestamo)
        @php
        $vence = \Carbon\Carbon::parse($prestamo->fecha_devolucion_prevista);
        $hoy = \Carbon\Carbon::today();
        $diasRestantes = $hoy->diffInDays($vence, false);
        @endphp

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden transition-hover" style="background-color: #ffffff;">
            <div class="card-body p-4">
                <div class="row align-items-center">

                    <div class="col-auto">
                        <div class="rounded-3 overflow-hidden shadow-sm border border-light" style="width: 80px; height: 115px; background-color: #f8f9fa;">
                            @if($prestamo->libro->portada)
                            <img src="{{ asset('storage/' . $prestamo->libro->portada) }}" alt="Portada" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                            <div class="w-100 h-100 d-flex justify-content-center align-items-center text-muted">
                                <i class="bi bi-book fs-3 opacity-50"></i>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="col">
                        <h5 class="fw-bold mb-1" style="color: #0D47A1;">{{ $prestamo->libro->titulo }}</h5>
                        <p class="text-muted small fw-medium mb-3">{{ $prestamo->libro->autor }}</p>

                        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                            <div class="text-muted small d-flex align-items-center">
                                <i class="bi bi-calendar3 me-2 opacity-50"></i>
                                Prestado el {{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y') }}
                            </div>

                            <div class="vr d-none d-md-block" style="opacity: 0.1;"></div>

                            @if($prestamo->estado === 'activo')
                            @if($diasRestantes < 0)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Vencido hace {{ abs($diasRestantes) }} días
                                </span>
                                @elseif($diasRestantes == 0)
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-clock-fill me-1"></i> Devolver hoy
                                </span>
                                @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-calendar2-check-fill me-1"></i> Devolver en {{ $diasRestantes }} días
                                </span>
                                @endif
                                @elseif($prestamo->estado === 'devuelto')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> Devuelto el {{ \Carbon\Carbon::parse($prestamo->fecha_devolucion_real)->format('d/m/Y') }}
                                </span>
                                @elseif($prestamo->estado === 'devuelto_tarde')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Devuelto con retraso
                                </span>
                                @endif
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light mt-1">
                            <div class="small fw-medium">
                                @if($prestamo->estado === 'activo')
                                @if($prestamo->renovaciones >= App\Models\Configuracion::get('max_renovaciones', 2))
                                <span class="text-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i> Límite de renovaciones alcanzado</span>
                                @elseif($diasRestantes < 0)
                                    <span class="text-danger"><i class="bi bi-info-circle me-1"></i> Préstamo vencido</span>
                                    @elseif($diasRestantes <= 3)
                                        <form action="{{ route('perfil.prestamos.renovar', $prestamo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Quieres solicitar una ampliación para este libro?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm text-white rounded-pill px-3 fw-bold shadow-sm" style="background-color: #1E90FF; font-size: 0.75rem;">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Renovar préstamo
                                        </button>
                                        </form>
                                        @else
                                        <span class="text-muted"><i class="bi bi-clock me-1"></i> Podrás renovar a partir del {{ $vence->copy()->subDays(3)->format('d/m/Y') }}</span>
                                        @endif
                                        @else
                                        <span class="text-muted"><i class="bi bi-arrow-repeat me-1 opacity-50"></i> Renovado {{ $prestamo->renovaciones }} veces</span>
                                        @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="px-4 pb-4 pt-3">
        <div class="mt-3 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 custom-pagination">
            <div class="text-muted small bg-light px-3 py-2 rounded-pill border shadow-sm" style="border-color: #f1f5f9 !important;">
                Mostrando del <span class="fw-bold text-dark">{{ $prestamos->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $prestamos->lastItem() ?? 0 }}</span> de <span class="fw-bold" style="color: #1E90FF;">{{ $prestamos->total() ?? 0 }}</span> resultados
            </div>

            <div class="pagination-wrapper">
                {{ $prestamos->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @else
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center" style="background-color: #ffffff;">
        <div class="d-inline-flex justify-content-center align-items-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
            <i class="bi bi-journal-text fs-1" style="color: #1E90FF;"></i>
        </div>
        <h5 class="fw-bold mb-2" style="color: #0D47A1;">No hay historial de lecturas</h5>
        <p class="text-muted small mb-4">Aún no has solicitado ningún libro en la biblioteca.</p>
        <a href="{{ route('catalogo.index') }}" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #1E90FF;">
            Explorar Catálogo
        </a>
    </div>
    @endif

</div>

<style>
    .transition-hover {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 0, 0, 0.03) !important;
    }

    .transition-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05), 0 8px 24px rgba(30, 144, 255, 0.15) !important;
        border-color: rgba(30, 144, 255, 0.2) !important;
    }

    .book-img-wrapper {
        overflow: hidden;
    }

    .book-img-wrapper img {
        transition: transform 0.5s ease;
    }

    .transition-hover:hover .book-img-wrapper img {
        transform: scale(1.08);
    }

    .custom-pagination nav>div.d-flex.justify-content-between.flex-fill.d-sm-none {
        display: none !important;
    }

    .custom-pagination nav>div.d-none.flex-sm-fill.d-sm-flex>div:first-child {
        display: none !important;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
    }

    .custom-pagination .pagination {
        margin-bottom: 0;
        gap: 5px;
        border: none;
    }

    .custom-pagination .page-item:first-child .page-link,
    .custom-pagination .page-item:last-child .page-link {
        border-radius: 50px;
    }

    .custom-pagination .page-link {
        border-radius: 50px;
        color: #475569;
        background-color: transparent;
        border: 1px solid #e2e8f0;
        padding: 0.45rem 0.9rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .custom-pagination .page-item.active .page-link {
        color: white;
        background-color: #1E90FF;
        border-color: #1E90FF;
        box-shadow: 0 2px 5px rgba(30, 144, 255, 0.2);
    }

    .custom-pagination .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: transparent;
        border-color: #e2e8f0;
        opacity: 0.6;
    }

    .custom-pagination .page-link:hover:not(.active):not(.disabled) {
        color: #1E90FF;
        background-color: rgba(30, 144, 255, 0.05);
        border-color: #bfdbfe;
    }
</style>
@endsection