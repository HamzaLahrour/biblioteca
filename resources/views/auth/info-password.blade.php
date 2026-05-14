<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña — LibreLah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #38BDF8;
            --primary-blue-dark: #0284C7;
            --primary-blue-deep: #1E90FF;
            --primary-blue-soft: #E0F2FE;
            --text-main: #0F172A;
            --text-muted: #64748B;
            --bg-body: #F8FAFC;
            --bg-input: #F8FAFC;
            --border-input: #E2E8F0;
            --white: #FFFFFF;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            /* Sutil patrón de puntos de fondo, igual que el login */
            background-image: radial-gradient(circle, #CBD5E1 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .06), 0 20px 60px -10px rgba(8, 47, 73, .10);
            padding: 2.8rem 2.5rem 2.5rem;
            width: 100%;
            max-width: 460px;
            animation: fadeUp .5s ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Logo ── */
        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            height: 64px;
            width: auto;
            object-fit: contain;
            margin-bottom: .5rem;
        }

        .logo-tagline {
            font-size: .75rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        /* ── Separador ── */
        .divider {
            border: none;
            border-top: 1px solid var(--border-input);
            margin-bottom: 1.8rem;
        }

        /* ── Icono central ── */
        .icon-badge {
            width: 56px;
            height: 56px;
            background: var(--primary-blue-soft);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.4rem;
        }

        .icon-badge svg {
            width: 28px;
            height: 28px;
            color: var(--primary-blue-dark);
        }

        /* ── Encabezado ── */
        h1 {
            font-family: 'Inter', sans-serif;
            font-size: 1.45rem;
            font-weight: 600;
            color: var(--text-main);
            text-align: center;
            margin-bottom: .55rem;
            line-height: 1.25;
        }

        .subtitle {
            font-size: .875rem;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.55;
            margin-bottom: 1.6rem;
        }

        /* ── Bloque de información ── */
        .info-block {
            background: var(--primary-blue-soft);
            border-left: 4px solid var(--primary-blue-dark);
            border-radius: 10px;
            padding: 1.1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .info-block p {
            font-size: .875rem;
            color: var(--primary-blue-deep);
            line-height: 1.6;
        }

        /* ── Lista de pasos ── */
        .steps {
            list-style: none;
            margin: 1.1rem 0;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .steps li {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            font-size: .875rem;
            color: var(--text-main);
            line-height: 1.5;
        }

        .step-num {
            flex-shrink: 0;
            width: 22px;
            height: 22px;
            background: var(--primary-blue-dark);
            color: #fff;
            border-radius: 50%;
            font-size: .7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
        }

        /* ── Email pill ── */
        .email-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--white);
            border: 1.5px solid var(--primary-blue);
            border-radius: 999px;
            padding: .35rem .85rem;
            font-size: .875rem;
            font-weight: 500;
            color: var(--primary-blue-dark);
            margin: .5rem auto 0;
            width: fit-content;
        }

        .email-pill svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        /* ── Aviso de seguridad ── */
        .security-note {
            background: #FFF7ED;
            border: 1px solid #FED7AA;
            border-radius: 10px;
            padding: .9rem 1.1rem;
            margin-top: 1.1rem;
            display: flex;
            gap: .65rem;
            align-items: flex-start;
        }

        .security-note svg {
            width: 18px;
            height: 18px;
            color: #C2410C;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .security-note p {
            font-size: .8rem;
            color: #7C2D12;
            line-height: 1.55;
        }

        .security-note strong {
            font-weight: 600;
        }

        /* ── Plazo ── */
        .deadline {
            text-align: center;
            font-size: .8rem;
            color: var(--text-muted);
            margin-top: 1.2rem;
        }

        .deadline span {
            font-weight: 600;
            color: var(--primary-blue-dark);
        }

        /* ── Botón volver ── */
        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
            margin-top: 1.8rem;
            padding: .78rem;
            background: var(--primary-blue-deep);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background .2s, transform .15s;
        }

        .btn-back:hover {
            background: var(--primary-blue-dark);
            transform: translateY(-1px);
        }

        .btn-back svg {
            width: 16px;
            height: 16px;
        }


        @media (max-width: 480px) {
            body {
                padding: 1rem;
                /* Reducimos el margen exterior */
            }

            .card {
                padding: 2rem 1.5rem;
                /* Reducimos el relleno interior drásticamente */
            }

            h1 {
                font-size: 1.25rem;
                /* Ajustamos el título para que no salte de línea feo */
            }

            .subtitle {
                font-size: 0.8rem;
                margin-bottom: 1.2rem;
            }

            .info-block {
                padding: 1rem;
            }

            .email-pill {
                font-size: 0.8rem;
                padding: 0.35rem 0.7rem;
            }

            /* Hacemos que el aviso de seguridad se apile para que no se asfixie el texto */
            .security-note {
                padding: 0.85rem;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 0.4rem;
            }

            .security-note svg {
                margin-top: 0;
            }
        }
    </style>
</head>

<body>

    <div class="card">

        {{-- Logo --}}
        <div class="logo-container">
            <img src="{{ asset('img/logolibrelah.png') }}" alt="LibreLah">
            <p class="logo-tagline">Sistema de Gestión de Biblioteca</p>
        </div>

        <hr class="divider">

        {{-- Icono --}}
        <div class="icon-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
        </div>

        <h1>Restablecimiento de Credenciales</h1>
        <p class="subtitle">
            Por motivos de seguridad y gestión interna de la biblioteca,<br>
            la recuperación de contraseñas se realiza de forma <strong>manual</strong>.
        </p>

        {{-- Pasos --}}
        <div class="info-block">
            <p>Para solicitar el cambio, envía un correo a:</p>
            <div style="text-align:center; margin-top:.6rem;">
                <span class="email-pill">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                    soporte@biblioteca.com
                </span>
            </div>

            <ul class="steps" style="margin-top:1rem;">
                <li>
                    <span class="step-num">1</span>
                    <span>Indica tu <strong>nombre completo</strong> tal como está registrado en el sistema.</span>
                </li>
                <li>
                    <span class="step-num">2</span>
                    <span>Incluye tu <strong>DNI</strong> desde la dirección de correo que tienes asociada a tu cuenta. Esto nos permite verificar que eres el titular real y evitar que alguien solicite el cambio en tu nombre.</span>
                </li>
            </ul>
        </div>

        {{-- Aviso de seguridad --}}
        <div class="security-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <p>
                <strong>Por tu seguridad:</strong> nunca compartiremos tu contraseña por teléfono ni por redes sociales. La solicitud <strong>sólo se tramita</strong> desde tu correo registrado. Si no reconoces ninguna solicitud, ignora este proceso.
            </p>
        </div>

        <p class="deadline">Recibirás una clave temporal en un plazo de <span>24 horas</span>.</p>

        {{-- Volver --}}
        <a href="{{ route('login') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6" />
            </svg>
            Volver al inicio de sesión
        </a>

    </div>

</body>

</html>