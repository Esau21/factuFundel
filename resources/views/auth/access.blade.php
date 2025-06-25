<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error 403 - SISTEMA CONTABLE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .full-screen-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            text-align: center;
            padding: 20px;
        }

        .error-image {
            width: 350px;
            height: auto;
            margin-bottom: 20px;
            filter: grayscale(100%);
        }

        h1 {
            font-size: 3.5rem;
            color: #343a40;
            margin-bottom: 15px;
        }

        .message {
            color: #6c757d;
            font-size: 1.1rem;
            margin-top: 20px;
            font-weight: 300;
        }

        .btn-custom a {
            font-size: 1rem;
            padding: 10px 30px;
            margin: 10px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease-in-out;
        }

        .btn-primary {
            background-color: #0d6efd;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.3);
        }

        /* Ajustes para móvil */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.8rem;
            }

            .message {
                font-size: 0.95rem;
            }

            .btn-custom a {
                font-size: 0.9rem;
                padding: 8px 20px;
            }

            .error-image {
                width: 250px;
            }
        }
    </style>
</head>

<body>
    <div class="full-screen-container">
        <!-- Imagen de error 403 -->
        <img src="{{ empresaLogo() }}" alt="Error 403" class="error-image">

        <!-- Título de error -->
        <h1>403</h1>

        <!-- Mensaje de error -->
        <p class="message">Antes de continuar, consulte con su administrador para obtener acceso al sistema.</p>

        <!-- Botones -->
        <div class="btn-custom">
            <a class="btn btn-primary" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Cerrar Sesión
            </a>
            <a class="btn btn-secondary" href="{{ route('dashboard') }}">
                Recargar Página
            </a>
        </div>

        <!-- Formulario de cierre de sesión -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
