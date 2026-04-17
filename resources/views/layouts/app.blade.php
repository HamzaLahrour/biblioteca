<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LibreLah | Biblioteca')</title>

    {{-- Bootstrap 5 CSS y Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-main: #fafafa;
            --border-light: #f0f0f0;
            --text-dark: #111111;
            --text-muted: #737373;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            font-weight: 400;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        /* Navbar Minimalista */
        .navbar-minimal {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-light);
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 400;
            transition: color 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-dark) !important;
            font-weight: 600;
        }
    </style>
</head>

<body>

    {{-- 🧭 NAVEGACIÓN SUPERIOR --}}
    <nav class="navbar navbar-expand-lg navbar-minimal sticky-top">
        <div class="container">

            {{-- Logo --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('catalogo.index') }}">
                <img src="{{ asset('img/logolibrelah.png') }}" alt="Logo LibreLah" style="height: 28px;">
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAlumno">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarAlumno">

                {{-- Enlaces Centrados Visualmente --}}
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('catalogo.*') ? 'active' : '' }}" href="{{ route('catalogo.index') }}">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}" href="#">Salas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('perfil.*') ? 'active' : '' }}" href="#">Mis Préstamos</a>
                    </li>
                </ul>

                {{-- Avatar y Dropdown (Asumimos que siempre está logueado) --}}
                <div class="d-flex align-items-center mt-3 mt-lg-0">
                    <div class="dropdown">
                        <a class="text-decoration-none dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            @php
                            // Si por algún motivo falla, ponemos "US" de fallback
                            $nombre = Auth::check() ? Auth::user()->name : 'Usuario';
                            $iniciales = substr($nombre, 0, 2);
                            @endphp
                            <div class="bg-dark text-white rounded-circle d-flex justify-content-center align-items-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                {{ strtoupper($iniciales) }}
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border border-light mt-2 rounded-3">
                            <li>
                                <h6 class="dropdown-header text-muted fw-normal">Mi Cuenta</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('perfil.index') }}">Mi Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('usuarios.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Cerrar Sesión</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    {{-- 📦 CONTENIDO PRINCIPAL --}}
    <main>
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>