<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Usuario</title>
    </head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Bienvenido a tu Dashboard</h1>
            {{-- Comprobación para evitar el error --}}
            @if(Auth::check())
                <p class="user-info">Hola, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
            @else
                {{-- Esto se mostrará si por alguna razón el usuario no está autenticado --}}
                <p class="user-info">Hola, invitado</p>
            @endif
        </div>
        <div class="content">
            <p>Aquí puedes gestionar tus preferencias, revisar tus pedidos y explorar nuestra red de servicios. ¡Gracias por ser parte de HouseBuilde!</p>
        </div>
        <a href="{{ route('logout') }}" class="logout-btn">Cerrar Sesión</a>
    </div>
</body>
</html>