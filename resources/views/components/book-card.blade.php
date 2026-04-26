@props(['libro'])

@php
// Calculamos los disponibles reales
$prestados = $libro->prestamos()->where('estado', 'activo')->count();
$disponibles = $libro->copias_totales - $prestados;
@endphp


<a href="{{ route('catalogo.show', $libro->id) }}" class="text-decoration-none d-block h-100">
    <div class="card h-100 book-card border-0 shadow-sm-hover">
        <div class="book-cover-container position-relative skeleton-bg">
            @if($libro->portada)
            <img src="{{ $libro->portada }}" alt="{{ $libro->titulo }}" class="book-cover" onload="this.parentElement.classList.remove('skeleton-bg'); this.style.opacity=1;" style="opacity: 0;">
            @else
            <div class="book-cover d-flex justify-content-center align-items-center" style="background-color: var(--bg-light);">
                <i class="bi bi-book fs-1" style="color: var(--secondary-light); opacity: 0.5;"></i>
            </div>
            @endif
        </div>
        <div class="card-body p-3 d-flex flex-column">
            <span class="category-tag mb-1">{{ $libro->categoria->nombre ?? 'General' }}</span>
            <h6 class="book-title mb-1 text-truncate" title="{{ $libro->titulo }}">{{ $libro->titulo }}</h6>
            <p class="book-author mb-3 text-truncate" style="font-size: 0.8rem;">{{ $libro->autor }}</p>

            <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top-subtle">
                <div class="status-indicator">
                    @if($disponibles > 0)
                    <span class="status-dot available"></span>
                    <span class="status-text" style="font-size: 0.7rem;">Disponible</span>
                    @else
                    <span class="status-dot unavailable" style="background-color: #ff0000;"></span>
                    <span class="status-text text-muted" style="font-size: 0.7rem;">Agotado</span>
                    @endif
                </div>
                <span class="btn btn-sm btn-outline-details rounded-pill py-1 px-3" style="font-size: 0.75rem; font-weight: bold;">Detalles</span>
            </div>
        </div>
    </div>
</a>