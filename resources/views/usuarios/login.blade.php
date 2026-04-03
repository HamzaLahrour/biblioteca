<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibreLah - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            /* Colores extraídos para combinar con tu logo */
            --primary-blue: #38BDF8;
            --primary-blue-dark: #0284C7;
            --text-main: #111827;
            --text-muted: #6B7280;
            --bg-body: #F3F4F6;
            --bg-input: #F9FAFB;
            --border-input: #E5E7EB;
        }

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-body);
            /* Un degradado radial super sutil para dar profundidad sin ensuciar */
            background-image: radial-gradient(circle at center, #FFFFFF 0%, var(--bg-body) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .card {
            background: #FFFFFF;
            border: none;
            border-radius: 24px;
            /* Sombra muy suave y difuminada, marca la diferencia en 2026 */
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
            padding: 3rem 2.5rem;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            max-width: 180px;
            height: auto;
            margin-bottom: 0.5rem;
        }

        .logo-container p {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            margin: 0;
            letter-spacing: -0.2px;
        }

        .login-title {
            color: var(--text-main);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 0.25rem;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 2rem;
        }

        .form-label {
            color: var(--text-main);
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-control {
            background-color: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 15px;
            color: var(--text-main);
            transition: all 0.2s ease;
            box-shadow: none;
        }

        .form-control::placeholder {
            color: #9CA3AF;
        }

        .form-control:focus {
            background-color: #FFFFFF;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            border: none;
            border-radius: 14px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            padding: 14px;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.3);
            background: linear-gradient(135deg, #16A3E4 0%, #0369A1 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert-danger {
            background-color: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="card">
            <div class="logo-container">
                <img src="{{ asset('img/logolibrelah.png') }}" alt="Logo LibreLah">
                <p>Sistema de Gestión de Biblioteca</p>
            </div>

            <div>
                <h2 class="login-title">Bienvenido de nuevo</h2>
                <p class="login-subtitle">Accede con tu cuenta para continuar</p>

                {{-- Errores --}}
                @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <i class="bi bi-exclamation-circle me-2"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('usuarios.authenticate') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="tucorreo@email.com"
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="••••••••"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Entrar <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>