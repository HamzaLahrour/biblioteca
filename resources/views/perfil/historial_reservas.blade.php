@extends('layouts.app')

@section('title', 'Historial de Reservas | LibreLah')

@section('content')
<div class="container py-5 mb-5">

    <div class="mb-5">
        <a href="{{ route('perfil.index') }}" class="btn bg-white rounded-pill shadow-sm mb-3 px-3 py-2 d-inline-flex align-items-center fw-bold transition-hover" style="color: #0D47A1; border: 1px solid rgba(30, 144, 255, 0.15);">
            <i class="bi bi-arrow-left-short fs-5 me-1" style="color: #1E90FF;"></i>
            <span style="font-size: 0.85rem; letter-spacing: 0.3px;">Volver a Mi Espacio</span>
        </a>

        <h2 class="fw-bold mb-0 mt-2" style="color: var(--text-main); letter-spacing: -0.5px;">
            Historial de <span class="text-gradient" style="background: linear-gradient(135deg, #1E90FF 0%, #64B5F6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Reservas</span>
        </h2>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if($reservas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #F8F9FA;">
                        <tr>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0">Fecha</th>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0">Espacio</th>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0">Horario</th>
                            <th class="px-4 py-3 text-uppercase text-muted small fw-bold border-0 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservas as $reserva)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold" style="color: #0D47A1;">
                                    {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 fw-medium">
                                {{ $reserva->espacio->nombre ?? 'Sala no disponible' }}
                            </td>
                            <td class="px-4 py-3 text-muted small fw-bold">
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($reserva->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($reserva->hora_fin)->format('H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($reserva->estado === 'cancelada')
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Cancelada</span>
                                @elseif($reserva->estado === 'finalizada')
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Finalizada</span>
                                @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill shadow-sm">Activa</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 pb-4 pt-3">
                <div class="mt-3 mb-2 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 custom-pagination">
                    <div class="text-muted small bg-light px-3 py-2 rounded-pill border shadow-sm" style="border-color: #f1f5f9 !important;">
                        Mostrando del <span class="fw-bold text-dark">{{ $reservas->firstItem() ?? 0 }}</span> al <span class="fw-bold text-dark">{{ $reservas->lastItem() ?? 0 }}</span> de <span class="fw-bold" style="color: #1E90FF;">{{ $reservas->total() ?? 0 }}</span> resultados
                    </div>

                    <div class="pagination-wrapper">
                        {{ $reservas->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            @else
            <div class="p-5 text-center text-muted">
                <i class="bi bi-clock-history fs-1 mb-3 d-block opacity-50"></i>
                <h6 class="fw-bold">No hay historial</h6>
                <p class="small mb-0">Aún no has realizado ninguna reserva en la biblioteca.</p>
            </div>
            @endif
        </div>
    </div>

</div>

<style>
    .transition-hover {
        transition: all 0.3s ease;
    }

    .transition-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
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