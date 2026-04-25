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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* 🧬 NUESTRO ADN GLOBAL (Paleta Fase Gold) 🧬 */
        :root {
            --primary: #1E90FF;
            /* Azul Dodger */
            --secondary-dark: #0D47A1;
            /* Azul Oscuro */
            --secondary-light: #64B5F6;
            /* Azul Claro */
            --text-main: #212121;
            /* Gris Oscuro */
            --text-muted: #757575;
            /* Gris Medio */
            --bg-light: #F8F9FA;
            /* Fondo App */

            /* Utilidades translúcidas */
            --primary-soft: rgba(30, 144, 255, 0.1);
            --danger-soft: rgba(239, 68, 68, 0.1);
            --success-main: #22c55e;
            --success-soft: rgba(34, 197, 94, 0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        /* 🧭 NAVBAR PREMIUM */
        .navbar-minimal {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            /* Efecto cristal Apple */
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--secondary-dark) !important;
        }

        /* Indicador de sección activa con nuestro azul */
        .nav-link.active {
            color: var(--primary) !important;
            font-weight: 700;
        }

        /* Línea sutil debajo del link activo en PC */
        @media (min-width: 992px) {
            .nav-link::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 2px;
                background-color: var(--primary);
                transition: width 0.3s ease;
                border-radius: 2px;
            }

            .nav-link.active::after {
                width: 80%;
            }
        }

        /* 👤 AVATAR PREMIUM */
        .avatar-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary-dark));
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 2px 8px var(--primary-soft);
            transition: transform 0.2s ease;
        }

        .dropdown-toggle:hover .avatar-wrapper {
            transform: scale(1.05);
        }

        /* Ocultar flecha por defecto del dropdown para más limpieza */
        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu-custom {
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background-color: var(--bg-light);
            color: var(--primary);
        }

        .custom-alert-danger {
            background-color: rgba(239, 68, 68, 0.08);
            /* Fondo rojo muy suave */
            color: #ef4444;
            /* Texto rojo intenso */
            border: 1px solid rgba(239, 68, 68, 0.15);
            /* Borde sólido ultra sutil, NADA de punteados */
            border-radius: 16px;
        }

        .custom-alert-success {
            background-color: rgba(16, 185, 129, 0.08);
            /* Fondo verde muy suave */
            color: #10b981;
            /* Texto verde intenso */
            border: 1px solid rgba(16, 185, 129, 0.15);
            /* Borde sólido ultra sutil */
            border-radius: 16px;
        }
    </style>
</head>

<body>

    {{-- 🧭 NAVEGACIÓN SUPERIOR --}}
    <nav class="navbar navbar-expand-lg navbar-minimal sticky-top">
        <div class="container">

            {{-- Logo --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('catalogo.index') }}">
                {{-- Si el logo tiene texto oscuro, destacará perfecto sobre el blanco --}}
                <img src="{{ asset('img/logolibrelah.png') }}" alt="Logo LibreLah" style="height: 32px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            </a>

            <button class="navbar-toggler border-0 shadow-none text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAlumno">
                <i class="bi bi-list fs-1"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarAlumno">

                {{-- Enlaces Centrados con Indicadores Activos --}}
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-2 mt-3 mt-lg-0 text-center text-lg-start">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('catalogo.*') ? 'active' : '' }}" href="{{ route('catalogo.index') }}">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reservas.*') ? 'active' : '' }}" href="{{ route('reservas_usuario.index') }}">Salas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('perfil.*') ? 'active' : '' }}" href="{{ route('perfil.index') }}">Mi Espacio</a>
                    </li>
                </ul>

                {{-- Avatar y Dropdown Inyectados con ADN --}}
                <div class="d-flex justify-content-center mt-3 mt-lg-0 pb-3 pb-lg-0">
                    <div class="dropdown">
                        <a class="text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                            $nombre = Auth::check() ? Auth::user()->name : 'Usuario';
                            $palabras = explode(' ', $nombre);
                            $iniciales = count($palabras) > 1
                            ? substr($palabras[0], 0, 1) . substr($palabras[1], 0, 1)
                            : substr($nombre, 0, 2);
                            @endphp

                            <div class="avatar-wrapper">
                                {{ strtoupper($iniciales) }}
                            </div>
                        </a>

                        {{-- Dropdown Premium (Mini Tarjeta) --}}
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom mt-2 p-3" style="width: 250px;">
                            {{-- Info del Usuario --}}
                            <li class="mb-3 text-center">
                                <div class="avatar-wrapper mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                    {{ strtoupper($iniciales) }}
                                </div>
                                <h6 class="fw-bold mb-0 text-truncate" style="color: var(--secondary-dark);">{{ Auth::user()->name ?? 'Lector' }}</h6>
                                <span class="text-muted small text-truncate d-block">{{ Auth::user()->email ?? 'usuario@email.com' }}</span>
                            </li>

                            <li>
                                <hr class="dropdown-divider opacity-10 mb-2">
                            </li>

                            {{-- (Opcional) Si en el futuro tienes una vista para cambiar contraseña o foto, iría aquí --}}
                            {{-- <li><a class="dropdown-item py-2 fw-medium rounded-3" href="#"><i class="bi bi-gear me-2"></i> Ajustes de cuenta</a></li> --}}

                            {{-- Botón de Salir --}}
                            <li>
                                <form action="{{ route('usuarios.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger fw-bold rounded-3 d-flex align-items-center justify-content-center mt-1" style="background-color: var(--danger-soft);">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                    </button>
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
        {{-- Envolvemos las alertas en un container para que NO se estiren --}}
        <div class="container mt-4">

            @if(session('success'))
            <div class="alert custom-alert-success alert-dismissible fade show d-flex align-items-start gap-3 shadow-sm mb-4 position-relative pe-5" role="alert">
                <i class="bi bi-check-circle-fill fs-5 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">¡Completado!</h6>
                    <p class="mb-0 small">{{ session('success') }}</p>
                </div>
                <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-3" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            @endif

            {{-- Alerta de Error --}}
            @if($errors->any())
            <div class="alert custom-alert-danger alert-dismissible fade show d-flex align-items-start gap-3 shadow-sm mb-4 position-relative pe-5" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Revisa los datos</h6>
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close position-absolute top-50 end-0 translate-middle-y me-3" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>
            @endif

        </div>

        {{-- Aquí se inyecta el contenido de tus vistas --}}
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>