<aside class="w-64 bg-card-dark flex flex-col p-6 border-r border-border-dark">
    <!-- Logo y Información -->
    <div class="flex items-center gap-4 mb-10">
        <img alt="Logo de HouseBuild" class="h-12 w-12 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhL1Sn4cjqlUXnC8h_zf5gZYs4vPwVSdaeki-Xknsmcx1Z3-QzzX8JJnj51edeMV-vqh1TLQ1wjReWUoCDZjOJ2SQZGX7c7QlWkA_casqzyMjKZOmVcLBLFZWgou4UBBcr0WHG-Fh_0kGC29yG3YgAR8qPDFYaYH1VopSoXw9plrcTaGLvZX_qvuGXtcXqkbW5j_-qbnNR55bj9vsfQl2CudPq_rkfldORyHcEPw5hknjiOTITvUnkHlZlL_s7eUmHAa0_uMai0lY"/>
        <div>
            <h2 class="font-bold text-lg text-text-dark">HouseBuild</h2>
            <span class="text-sm text-text-secondary-dark">Maestro de Obras</span>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="flex flex-col gap-4">
        <a class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('maestro.dashboard') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark' }}"
           href="{{ route('maestro.dashboard') }}">
            <span class="material-icons">dashboard</span>
            <span>Dashboard</span>
        </a>

        {{-- Comentar temporalmente las rutas no definidas --}}
        {{--
        <a class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('maestro.proyectos*') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark' }}"
           href="{{ route('maestro.proyectos') }}">
            <span class="material-icons">engineering</span>
            <span>Proyectos</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('maestro.equipos*') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark' }}"
           href="{{ route('maestro.equipos') }}">
            <span class="material-icons">groups</span>
            <span>Equipos</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('maestro.materiales*') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark' }}"
           href="{{ route('maestro.materiales') }}">
            <span class="material-icons">inventory_2</span>
            <span>Materiales</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2 rounded-lg {{ request()->routeIs('maestro.solicitudes*') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark' }}"
           href="{{ route('maestro.solicitudes') }}">
            <span class="material-icons">request_quote</span>
            <span>Solicitudes</span>
        </a>
        --}}

        <!-- Enlaces temporales sin rutas -->
        <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark"
           href="#">
            <span class="material-icons">engineering</span>
            <span>Proyectos</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark"
           href="#">
            <span class="material-icons">groups</span>
            <span>Equipos</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark"
           href="#">
            <span class="material-icons">inventory_2</span>
            <span>Materiales</span>
        </a>

        <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark"
           href="#">
            <span class="material-icons">request_quote</span>
            <span>Solicitudes</span>
        </a>
    </nav>

    <!-- Cerrar Sesión -->
    <div class="mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-4 py-2 rounded-lg text-text-secondary-dark hover:bg-gray-700 hover:text-text-dark w-full">
                <span class="material-icons">logout</span>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>
