<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Proveedor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4caf50; /* Un color verde para la frescura de los negocios */
            --background-color: #f0f2f5;
            --card-background: #ffffff;
            --text-color: #333;
            --heading-color: #1a1a1a;
            --box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .dashboard-container {
            width: 90%;
            max-width: 800px;
            background-color: var(--card-background);
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            padding: 40px;
            text-align: center;
        }
        .header {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: var(--heading-color);
            font-weight: 600;
            margin: 0;
        }
        .user-info {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 400;
        }
        .logout-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            transition: background-color 0.3s ease;
            margin-top: 30px;
            display: inline-block;
        }
        .logout-btn:hover {
            background-color: #3e8e41;
        }
        .content {
            font-size: 1rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Dashboard de Proveedor</h1>
            <p class="user-info">Hola, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
        </div>
        <div class="content">
            <p>Desde aquí puedes gestionar tu inventario, actualizar la información de tu empresa, y ver las solicitudes de los clientes. ¡Tu negocio en un solo lugar!</p>
        </div>
        <a href="{{ route('logout') }}" class="logout-btn">Cerrar Sesión</a>
    </div>
</body>
</html>