<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Registro — LibreLah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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

        /* ── Bloque principal de info ── */
        .info-block {
            background: var(--primary-blue-soft);
            border-left: 4px solid var(--primary-blue-dark);
            border-radius: 10px;
            padding: 1.1rem 1.25rem 1.25rem;
            margin-bottom: 1.1rem;
        }

        .info-block-title {
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--primary-blue-dark);
            margin-bottom: .8rem;
        }

        /* ── Lista de requisitos ── */
        .req-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .7rem;
        }

        .req-list li {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            font-size: .875rem;
            color: var(--text-main);
            line-height: 1.5;
        }

        .req-num {
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

        /* ── Horario / ubicación ── */
        .location-block {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 10px;
            padding: .9rem 1.1rem;
            margin-top: .2rem;
        }

        .location-block svg {
            width: 18px;
            height: 18px;
            color: #16A34A;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .location-block p {
            font-size: .825rem;
            color: #14532D;
            line-height: 1.6;
        }

        .location-block strong {
            font-weight: 600;
        }

        /* ── Mensaje de bienvenida ── */
        .welcome-banner {
            margin-top: 1.2rem;
            background: linear-gradient(135deg, var(--primary-blue-deep) 0%, #0369A1 100%);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .85rem;
        }

        .welcome-banner svg {
            width: 22px;
            height: 22px;
            color: var(--primary-blue);
            flex-shrink: 0;
        }

        .welcome-banner p {
            font-size: .875rem;
            color: #E0F2FE;
            line-height: 1.5;
        }

        .welcome-banner strong {
            color: #fff;
            font-weight: 600;
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
            }

            .card {
                padding: 2rem 1.5rem;
                /* Reducimos el relleno general de la tarjeta */
            }

            h1 {
                font-size: 1.25rem;
                /* Ajustamos el título */
            }

            .subtitle {
                font-size: 0.8rem;
                margin-bottom: 1.2rem;
            }

            .info-block {
                padding: 1rem;
            }

            .req-list li {
                font-size: 0.8rem;
                /* Reducimos un poco el texto de la lista */
            }

            /* Apilamos el bloque de ubicación para que no se aplaste el texto */
            .location-block {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 1rem;
                gap: 0.4rem;
            }

            .location-block svg {
                margin-top: 0;
            }

            /* Hacemos lo mismo con el banner de bienvenida para darle espacio */
            .welcome-banner {
                flex-direction: column;
                text-align: center;
                padding: 1.2rem 1rem;
                gap: 0.6rem;
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
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
        </div>

        <h1>¿Quieres ser miembro?</h1>
        <p class="subtitle">
            El alta de nuevos usuarios se realiza de forma<br>
            <strong>presencial en el mostrador principal</strong> de la biblioteca.
        </p>

        {{-- Requisitos --}}
        <div class="info-block">
            <p class="info-block-title">Requisitos para el registro</p>
            <ul class="req-list">
                <li>
                    <span class="req-num">1</span>
                    <span>Presentar tu <strong>DNI o documento de identidad oficial</strong> vigente en el mostrador.</span>
                </li>
                <li>
                    <span class="req-num">2</span>
                    <span>Cumplimentar el <strong>formulario de inscripción física</strong> que te facilitará el personal.</span>
                </li>
            </ul>
        </div>

        {{-- Ubicación / horario --}}
        <div class="location-block">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                <circle cx="12" cy="10" r="3" />
            </svg>
            <p>
                Acércate al <strong>mostrador principal</strong> durante el horario de atención.<br>
                El personal gestionará tu alta y te entregará tu <strong>carné de lector</strong> al momento.
            </p>
        </div>

        {{-- Bienvenida --}}
        <div class="welcome-banner">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
            </svg>
            <p><strong>¡Te esperamos!</strong> Únete a nuestra comunidad lectora y disfruta de todos nuestros fondos bibliográficos.</p>
        </div>

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