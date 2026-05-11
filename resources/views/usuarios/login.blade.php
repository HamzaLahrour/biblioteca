<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibreLah — Inicia sesión</title>

    {{-- Preload de la imagen para que el LCP sea rápido --}}
    <link rel="preload" as="image" href="{{ asset('img/biblioteca-fondo.jpg') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            /* Paleta de marca — azules */
            --primary-blue: #38BDF8;
            --primary-blue-dark: #0284C7;
            --primary-blue-deep: #082F49;
            --primary-blue-soft: #E0F2FE;

            /* Neutros */
            --text-main: #0F172A;
            --text-muted: #64748B;
            --bg-body: #F8FAFC;
            --bg-input: #F8FAFC;
            --border-input: #E2E8F0;
            --white: #FFFFFF;
        }

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: var(--bg-body);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ============================================
           LAYOUT PRINCIPAL — SPLIT 45 / 55
           ============================================ */
        .auth-shell {
            display: grid;
            grid-template-columns: 45% 55%;
            min-height: 100vh;
            width: 100%;
        }

        /* ============================================
           PANEL IZQUIERDO — FORMULARIO
           ============================================ */
        .auth-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            background-color: var(--white);
            position: relative;
            z-index: 2;
        }

        .auth-form-inner {
            width: 100%;
            max-width: 420px;
        }

        /* Logo */
        .logo-container {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-container img {
            max-width: 220px;
            height: auto;
            margin-bottom: 0.75rem;
            display: inline-block;
        }

        .logo-tagline {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            margin: 0;
            letter-spacing: 0.2px;
            text-transform: uppercase;
        }

        /* Títulos */
        .login-title {
            color: var(--text-main);
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.6px;
            line-height: 1.2;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        /* Form */
        .form-label {
            color: var(--text-main);
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 8px;
            letter-spacing: 0.1px;
        }

        .form-control {
            background-color: var(--bg-input);
            border: 1.5px solid var(--border-input);
            border-radius: 12px;
            padding: 13px 16px;
            font-size: 15px;
            color: var(--text-main);
            transition: all 0.2s ease;
            box-shadow: none;
        }

        .form-control::placeholder {
            color: #94A3B8;
        }

        .form-control:hover {
            border-color: #CBD5E1;
        }

        .form-control:focus {
            background-color: var(--white);
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #EF4444;
        }

        /* Input con icono (password con ojito) */
        .input-wrapper {
            position: relative;
        }

        .input-wrapper .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.2s ease;
        }

        .input-wrapper .toggle-password:hover {
            color: var(--primary-blue-dark);
        }

        /* Fila "recordarme" + "olvidé contraseña" */
        .form-extras {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 14px;
        }

        .form-extras a {
            color: var(--primary-blue-dark);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .form-extras a:hover {
            color: var(--primary-blue-deep);
            text-decoration: underline;
        }

        .form-check-input:checked {
            background-color: var(--primary-blue-dark);
            border-color: var(--primary-blue-dark);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
            border-color: var(--primary-blue);
        }

        .form-check-label {
            color: var(--text-muted);
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        /* Botón principal */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            padding: 14px;
            width: 100%;
            transition: transform 0.2s ease, box-shadow 0.3s ease, background 0.3s ease;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.35);
            background: linear-gradient(135deg, #16A3E4 0%, #0369A1 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(2, 132, 199, 0.25);
        }

        .btn-primary i {
            transition: transform 0.2s ease;
        }

        .btn-primary:hover i {
            transform: translateX(3px);
        }

        /* Footer del formulario */
        .form-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--primary-blue-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        /* Alert */
        .alert-danger {
            background-color: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ============================================
           PANEL DERECHO — IMAGEN + QUOTE
           ============================================ */
        .auth-image-panel {
            position: relative;
            overflow: hidden;
            background-color: var(--primary-blue-deep);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 4rem 4rem 3.5rem;
        }

        /* Imagen de fondo */
        .auth-image-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("img/fondo 4.jpg") }}');
            background-size: cover;
            background-position: center center;
            z-index: 0;
        }

        /*
           OVERLAY DEGRADADO — La clave del contraste sin blur.
           Va de azul marino oscuro (abajo-izq donde está el texto)
           a un azul más translúcido arriba-derecha.
           Esto garantiza WCAG AA para texto blanco.
        */
        .auth-image-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg,
                    rgba(8, 47, 73, 0.92) 0%,
                    rgba(2, 132, 199, 0.65) 55%,
                    rgba(2, 132, 199, 0.45) 100%);
            z-index: 1;
        }

        /* Línea sutil de "costura" entre paneles */
        .auth-image-panel {
            box-shadow: inset 4px 0 0 0 var(--primary-blue);
        }

        /* Contenido del panel — quote */
        .image-content {
            position: relative;
            z-index: 2;
            color: var(--white);
            max-width: 560px;
        }

        .image-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--white);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 1.5rem;
            letter-spacing: 0.2px;
        }

        .image-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--primary-blue);
            box-shadow: 0 0 8px var(--primary-blue);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.3);
            }
        }

        .image-quote {
            font-size: 38px;
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 1.25rem;
            color: var(--white);
            text-shadow: 0 2px 16px rgba(0, 0, 0, 0.3);
            position: relative;
            padding-left: 1.5rem;
        }

        /* Barra vertical azul brillante a la izquierda del quote */
        .image-quote::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 4px;
            background: var(--primary-blue);
            border-radius: 4px;
            box-shadow: 0 0 16px rgba(56, 189, 248, 0.6);
        }

        .image-quote span {
            color: var(--primary-blue);
        }

        .image-subtext {
            font-size: 16px;
            font-weight: 400;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.85);
            margin: 0 0 0 1.5rem;
            max-width: 500px;
        }

        /* Mini features list abajo */
        .image-features {
            display: flex;
            gap: 2rem;
            margin-top: 2.5rem;
            padding-left: 1.5rem;
            flex-wrap: wrap;
        }

        .image-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.95);
            font-size: 14px;
            font-weight: 500;
        }

        .image-feature i {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(56, 189, 248, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.4);
            border-radius: 8px;
            color: var(--primary-blue);
            font-size: 16px;
        }

        /* ============================================
           ANIMACIÓN DE ENTRADA
           ============================================ */
        .auth-form-inner {
            animation: fadeInUp 0.6s ease-out;
        }

        .image-content {
            animation: fadeInUp 0.7s ease-out 0.1s backwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================
           RESPONSIVE — TABLET
           ============================================ */
        @media (max-width: 1024px) {
            .auth-shell {
                grid-template-columns: 50% 50%;
            }

            .auth-image-panel {
                padding: 3rem 2.5rem 2.5rem;
            }

            .image-quote {
                font-size: 30px;
            }

            .image-features {
                gap: 1.25rem;
            }
        }

        /* ============================================
           RESPONSIVE — MÓVIL
           ============================================ */
        @media (max-width: 768px) {
            .auth-shell {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .auth-image-panel {
                min-height: 240px;
                padding: 2rem 1.5rem;
                justify-content: flex-end;
                box-shadow: inset 0 -4px 0 0 var(--primary-blue);
            }

            .image-badge {
                margin-bottom: 0.75rem;
            }

            .image-quote {
                font-size: 22px;
                padding-left: 1rem;
                margin-bottom: 0;
            }

            .image-quote::before {
                width: 3px;
            }

            .image-subtext,
            .image-features {
                display: none;
            }

            .auth-form-panel {
                padding: 2rem 1.25rem 3rem;
            }

            .logo-container {
                margin-bottom: 1.75rem;
            }

            .logo-container img {
                max-width: 180px;
            }

            .login-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <main class="auth-shell">

        {{-- ============================================
             PANEL IZQUIERDO — FORMULARIO
             ============================================ --}}
        <section class="auth-form-panel" aria-labelledby="login-title">
            <div class="auth-form-inner">

                <div class="logo-container">
                    <img src="{{ asset('img/logolibrelah.png') }}" alt="LibreLah">
                    <p class="logo-tagline">Sistema de Gestión de Biblioteca</p>
                </div>

                <h1 id="login-title" class="login-title">Bienvenido de nuevo</h1>
                <p class="login-subtitle">Accede con tu cuenta para gestionar préstamos y reservar espacios.</p>

                {{-- Errores --}}
                @if($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <form action="{{ route('usuarios.authenticate') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="tucorreo@email.com"
                            autocomplete="email"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-wrapper">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required>
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggleIcon" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-extras">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>
                        <a href="#">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <span>Iniciar sesión</span>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <p class="form-footer">
                    ¿Aún no tienes cuenta? <a href="#">Solicitar acceso</a>
                </p>

            </div>
        </section>

        {{-- ============================================
             PANEL DERECHO — IMAGEN + QUOTE
             ============================================ --}}
        <aside class="auth-image-panel" aria-hidden="true">
            <div class="image-content">

                <span class="image-badge">
                    <span class="dot"></span>
                    Tu biblioteca, simplificada
                </span>

                <h2 class="image-quote">
                    Pide <span>préstamos</span> y reserva <span>espacios</span> en segundos.
                </h2>

                <p class="image-subtext">
                    Gestiona libros, salas de estudio y reservas desde un único lugar. Diseñado para estudiantes, bibliotecarios y lectores que valoran su tiempo.
                </p>

                <div class="image-features">
                    <div class="image-feature">
                        <i class="bi bi-book" aria-hidden="true"></i>
                        <span>Préstamos al instante</span>
                    </div>
                    <div class="image-feature">
                        <i class="bi bi-calendar-check" aria-hidden="true"></i>
                        <span>Reserva de espacios</span>
                    </div>
                    <div class="image-feature">
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                        <span>100% seguro</span>
                    </div>
                </div>

            </div>
        </aside>

    </main>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            const button = icon.parentElement;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                button.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                button.setAttribute('aria-label', 'Mostrar contraseña');
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>