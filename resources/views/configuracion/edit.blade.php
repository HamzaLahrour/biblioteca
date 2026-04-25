@extends('layouts.admin')

@section('title', 'Ajustes del Sistema')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold text-dark">
                <i class="bi bi-gear-fill me-2 text-primary"></i>Configuración de la Biblioteca
            </h4>
        </div>



        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Ocurrió un error al guardar:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('configuracion.update') }}" method="POST">
            @csrf
            @method('PUT')

            @foreach($configuraciones as $seccion => $configs)
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
                    <h5 class="fw-bold text-secondary text-uppercase mb-0" style="letter-spacing: 0.5px;">
                        @if($seccion === 'horario') <i class="bi bi-clock-history me-2 text-primary"></i>
                        @elseif($seccion === 'reservas') <i class="bi bi-calendar-check me-2 text-success"></i>
                        @elseif($seccion === 'prestamos') <i class="bi bi-book me-2 text-info"></i>
                        @elseif($seccion === 'sanciones') <i class="bi bi-exclamation-octagon me-2 text-danger"></i>
                        @elseif($seccion === 'acceso') <i class="bi bi-person-badge me-2 text-warning"></i>
                        @else <i class="bi bi-sliders me-2 text-secondary"></i>
                        @endif
                        {{ $seccion }}
                    </h5>
                </div>

                <div class="card-body p-4 pt-3 border-top">
                    <div class="row g-4">
                        @foreach($configs as $config)
                        <div class="{{ $config->tipo === 'json' ? 'col-12' : 'col-md-6' }}">
                            <div class="form-group h-100 d-flex flex-column justify-content-between bg-white p-3 rounded-3 border">

                                <div class="mb-3">
                                    <label for="{{ $config->clave }}" class="form-label fw-bold text-dark mb-1">
                                        {{ $config->etiqueta }}
                                    </label>
                                    <p class="text-muted small mb-2" style="font-size: 0.8rem;">
                                        {{ $config->descripcion }}
                                    </p>
                                </div>

                                <div class="mt-auto">
                                    @if($config->tipo === 'boolean')
                                    <div class="form-check form-switch fs-5">
                                        <input type="hidden" name="configuraciones[{{ $config->clave }}]" value="false">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="{{ $config->clave }}"
                                            name="configuraciones[{{ $config->clave }}]"
                                            value="true"
                                            {{ old('configuraciones.'.$config->clave, $config->valor) === 'true' ? 'checked' : '' }}>
                                    </div>

                                    @elseif($config->tipo === 'time')
                                    <input type="time" name="configuraciones[{{ $config->clave }}]" id="{{ $config->clave }}"
                                        class="form-control bg-light"
                                        value="{{ old('configuraciones.'.$config->clave, $config->valor) }}">

                                    @elseif($config->tipo === 'integer')
                                    <input type="number" name="configuraciones[{{ $config->clave }}]" id="{{ $config->clave }}"
                                        class="form-control bg-light"
                                        value="{{ old('configuraciones.'.$config->clave, $config->valor) }}">

                                    @elseif($config->tipo === 'json')
                                    @php
                                    // Intentamos pillar lo que haya enviado (si hay error) o decodificamos lo de la BD
                                    $valorActual = old('configuraciones.'.$config->clave, json_decode($config->valor, true));
                                    if(is_string($valorActual)) {
                                    $valorActual = json_decode($valorActual, true);
                                    }
                                    $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                                    @endphp

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Día</th>
                                                    <th>Abierto</th>
                                                    <th>Apertura</th>
                                                    <th>Cierre</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($diasSemana as $dia)
                                                @php
                                                $dataDia = $valorActual[$dia] ?? ['abierto' => false, 'apertura' => '', 'cierre' => ''];
                                                // Manejar que 'abierto' pueda venir como string 'true'/'false' del formulario
                                                $isAbierto = filter_var($dataDia['abierto'], FILTER_VALIDATE_BOOLEAN);
                                                @endphp
                                                <tr>
                                                    <td class="text-capitalize fw-bold">{{ $dia }}</td>
                                                    <td>
                                                        <div class="form-check form-switch fs-5 mb-0">
                                                            <input type="hidden" name="configuraciones[{{ $config->clave }}][{{ $dia }}][abierto]" value="false">
                                                            <input class="form-check-input switch-dia" type="checkbox" role="switch"
                                                                data-dia="{{ $dia }}"
                                                                name="configuraciones[{{ $config->clave }}][{{ $dia }}][abierto]"
                                                                value="true"
                                                                {{ $isAbierto ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control form-control-sm input-hora-{{ $dia }}"
                                                            name="configuraciones[{{ $config->clave }}][{{ $dia }}][apertura]"
                                                            value="{{ $dataDia['apertura'] ?? '' }}"
                                                            {{ $isAbierto ? '' : 'disabled' }}>
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control form-control-sm input-hora-{{ $dia }}"
                                                            name="configuraciones[{{ $config->clave }}][{{ $dia }}][cierre]"
                                                            value="{{ $dataDia['cierre'] ?? '' }}"
                                                            {{ $isAbierto ? '' : 'disabled' }}>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @else
                                    <input type="text" name="configuraciones[{{ $config->clave }}]" id="{{ $config->clave }}"
                                        class="form-control bg-light"
                                        value="{{ old('configuraciones.'.$config->clave, $config->valor) }}">
                                    @endif
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            <div class="d-flex justify-content-end bg-white p-3 rounded-4 shadow-sm border mt-4 sticky-bottom" style="bottom: 20px; z-index: 1000;">
                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                    <i class="bi bi-save-fill me-2"></i>Guardar Ajustes
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const switches = document.querySelectorAll('.switch-dia');

        switches.forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const dia = this.getAttribute('data-dia');
                const inputs = document.querySelectorAll('.input-hora-' + dia);

                inputs.forEach(function(input) {
                    input.disabled = !toggle.checked;
                    if (!toggle.checked) {
                        input.value = ''; // Opcional: limpiar la hora si cierran
                    }
                });
            });
        });
    });
</script>
@endsection