<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibreLah - @yield('title', 'Panel de Administración')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #1E90FF;       /* Azul Dodger */
            --secondary-dark: #0D47A1; /* Azul Oscuro */
            --secondary-light: #64B5F6;/* Azul Claro */
            --text-main: #212121;     /* Gris Oscuro */
            --text-muted: #757575;    /* Gris Medio */
            --bg-light: #F5F5F5;      /* Gris Claro */
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-main);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Sidebar Limpio y Blanco */
        .sidebar {
            min-height: 100vh;
            background-color: #ffffff;
            border-right: 1px solid #e0e0e0;
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: var(--text-muted);
            border-radius: 0.375rem;
            padding: 0.6rem 1rem;
            margin-bottom: 0.2rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: var(--primary);
            background-color: rgba(30, 144, 255, 0.05);
        }

        .sidebar .nav-link.active {
            color: var(--secondary-dark);
            background-color: rgba(13, 71, 161, 0.08);
            font-weight: 600;
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.15rem;
        }

        .sidebar-heading {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--secondary-light);
            letter-spacing: 0.05rem;
            text-transform: uppercase;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            padding: 0 1rem;
        }

        /* Header / Topbar */
        .topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Botones Globales adaptados a tu paleta */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: var(--secondary-dark);
            border-color: var(--secondary-dark);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse px-3">
            <div class="position-sticky pt-3">
                
<a href="{{ route('dashboard') ?? '#' }}" class="d-flex justify-content-center align-items-center mb-5 mt-4 text-decoration-none px-2 logo-container">
    <img src="{{ asset('img/logolibrelah.png') }}" alt="Logo LibreLah" 
         style="max-height: 120px; width: auto; max-width: 100%; object-fit: contain;" 
         class="img-fluid p-2">
</a>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="bi bi-grid-1x2"></i> Panel de Control
                        </a>
                    </li>

                    <h6 class="sidebar-heading">A. Catálogo</h6>
                    <li class="nav-item">
                        <a href="{{ route('libros.index') }}" class="nav-link">
                            <i class="bi bi-book"></i> Libros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('categorias.index') }}" class="nav-link">
                            <i class="bi bi-tags"></i> Categorías
                        </a>
                    </li>

                    <h6 class="sidebar-heading">B. Usuarios</h6>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-people"></i> Listado de Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-person-plus"></i> Alta Manual
                        </a>
                    </li>

                    <h6 class="sidebar-heading">C. Préstamos</h6>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-arrow-left-right"></i> Préstamos Activos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-clock-history"></i> Historial de Devoluciones
                        </a>
                    </li>

                    <h6 class="sidebar-heading">D. Espacios</h6>
                    <li class="nav-item">
                        <a href="{{ route('tipos_espacios.index') }}" class="nav-link">
                            <i class="bi bi-geo-alt"></i> Gestión de Tipos de Espacios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('espacios.index') }}" class="nav-link">
                            <i class="bi bi-geo-alt"></i> Gestión de Espacios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-calendar-check"></i> Reservas Activas
                        </a>
                    </li>

                    <h6 class="sidebar-heading">E. Estadísticas</h6>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-bar-chart-line"></i> Informes de Uso
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            
            <header class="d-flex justify-content-between align-items-center pb-3 mb-4 topbar bg-transparent border-bottom">
                <div class="d-flex align-items-center">
                    <button class="navbar-toggler d-md-none me-3 border-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
                        <i class="bi bi-list fs-2 text-dark"></i>
                    </button>
                    <h1 class="h3 mb-0" style="color: var(--secondary-dark); font-weight: 700;">@yield('title', 'Inicio')</h1>
                </div>

                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center me-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2 text-white shadow-sm" style="width: 36px; height: 36px; background-color: var(--primary);">
                            {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                        </div>
                        <div class="d-none d-sm-block">
                            <span class="d-block lh-1" style="color: var(--text-main); font-weight: 500;">{{ Auth::user()->nombre ?? 'Administrador' }}</span>
                            <small style="color: var(--text-muted); font-size: 0.8rem;">Admin</small>
                        </div>
                    </div>

                    <form action="{{ route('usuarios.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm" style="color: var(--text-muted); border: 1px solid #e0e0e0;">
                            <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline ms-1">Salir</span>
                        </button>
                    </form>
                </div>
            </header>

            @if(session('success'))
                <div class="alert alert-dismissible fade show border-0 text-white" style="background-color: var(--secondary-light);" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>