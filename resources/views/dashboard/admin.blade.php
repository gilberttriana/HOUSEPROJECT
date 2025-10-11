<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/js/admin.js'])
</head>
<body>

    <div class="dashboard-container">
        
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="profile-info-minimal">
                    <h3>{{ Auth::user()->nombre }}</h3>
                    <span class="role ">Administrador</span>
                    <button class="toggle-btn" id="toggle-btn">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                </div>
    
            </div>
            
            <nav class="sidebar-nav">
                
                <ul>
                    <li class="has-submenu">
                        <a href="#" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
                        <ul class="submenu">
                            <li><a href="#">Administradores</a></li>
                            <li><a href="#">Clientes</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#" class="nav-item"><i class="fas fa-users"></i><span>Gestión de Usuarios</span></a>
                        <ul class="submenu">
                            <li><a href="#"><i class="fas fa-user"></i>Clientes</a></li>
                            <li><a href="#"><i class="fas fa-truck"></i>Proveedores</a></li>
                            <li><a href="#"><i class="fas fa-hard-hat"></i>Maestros de Obra</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#" class="nav-item"><i class="fas fa-truck"></i><span>Gestión de Proveedores</span></a>
                         <ul class="submenu">
                            <li><a href="#">Tipo de Insumos</a></li>
                            <li><a href="#">Unidades de Medida</a></li>
                            <li><a href="#">Categorías</a></li>
                        </ul>
                    </li>

                    <li class="has-submenu">
                        <a href="#" class="nav-item"><i class="fas fa-hard-hat"></i><span>Gestión de Maestros</span></a>
                        <ul class="submenu">
                            <li><a href="#">Tipo de Insumos</a></li>
                            <li><a href="#">Unidades de Medida</a></li>
                            <li><a href="#">Categorías</a></li>
                        </ul>
                    </li>

                   <li class="has-submenu">
                        <a href="#" class="nav-item"><i class="fas fa-cog"></i><span>Configuración</span></a>
                        <ul class="submenu">
                            <li><a href="#">Tipo de Insumos</a></li>
                            <li><a href="#">Unidades de Medida</a></li>
                            <li><a href="#">Categorías</a></li>
                        </ul>
                    </li> 
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="logout-btn"><i class="fas fa-sign-out-alt"></i><span>Cerrar Sesión</span></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="welcome-message">
                    <h1>Bienvenido a <span style="color:#e18011 ">HouseBuild</span></h1>
                </div>
            </header>
            
            <section class="content-section">
                <div class="card">
                            @hasSection('content')
                @yield('content')
            @else
                    <h2>Resumen de la Plataforma</h2>
                    <p>Accede a estadísticas clave, gestiona usuarios y administra los servicios de la plataforma HouseBuilde.</p>
                </div>
                 @endif
            </section>
        </main>
    </div>
</body>
</html>