<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Acceso Denegado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            background: linear-gradient(135deg, #ff4b1f, #1fddff);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
        .container-denied {
            text-align: center;
            max-width: 480px;
            background: rgba(0,0,0,0.3);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
            animation: fadeInScale 1s ease forwards;
        }
        @keyframes fadeInScale {
            0% {opacity: 0; transform: scale(0.8);}
            100% {opacity: 1; transform: scale(1);}
        }
        .icon-circle {
            background: rgba(255,255,255,0.2);
            width: 120px;
            height: 120px;
            margin: 0 auto 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 15px rgba(255,255,255,0.6);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 15px rgba(255,255,255,0.6); }
            50% { box-shadow: 0 0 30px rgba(255,255,255,0.9); }
        }
        .icon-circle svg {
            width: 70px;
            height: 70px;
            color: #ff6b6b;
        }
        h1 {
            font-weight: 900;
            font-size: 3.5rem;
            margin-bottom: 15px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        }
        p.lead {
            font-size: 1.3rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
        }
        .btn-home {
            font-weight: 700;
            font-size: 1.2rem;
            padding: 12px 40px;
            border-radius: 50px;
            background: #ff6b6b;
            border: none;
            box-shadow: 0 6px 15px rgba(255,107,107,0.6);
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            background: #ff4b4b;
            box-shadow: 0 8px 20px rgba(255,75,75,0.8);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="container-denied">
        <div class="icon-circle" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-shield-exclamation" viewBox="0 0 16 16">
                <path d="M5.338 1.515a61.44 61.44 0 0 1 5.324 0c.296.04.56.21.71.473a2.405 2.405 0 0 1 .405 1.436v4.66a2.405 2.405 0 0 1-.406 1.436c-.15.263-.414.433-.71.473a61.44 61.44 0 0 1-5.324 0c-.296-.04-.56-.21-.71-.473A2.405 2.405 0 0 1 4.55 8.084V3.424a2.405 2.405 0 0 1 .406-1.436c.15-.263.414-.433.71-.473zM8 5.5a.5.5 0 0 0-.5.5v3.5a.5.5 0 0 0 1 0V6a.5.5 0 0 0-.5-.5zm.002 5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5z"/>
            </svg>
        </div>
        <h1>¡Acceso Denegado!</h1>
        <p class="lead">No tienes permiso para acceder a esta sección. Por favor, contacta con el administrador.</p>
        <a href="../MntChart/index.php" class="btn btn-home">Volver al Inicio</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
