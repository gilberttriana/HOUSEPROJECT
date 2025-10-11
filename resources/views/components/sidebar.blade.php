<aside class="flex flex-col justify-between w-72 bg-[#101922] border-r border-primary/20 dark:border-primary/30">
  <div>
    <!-- Sidebar Header con logo, perfil y botón -->
    <div class="flex items-center gap-3 p-6 border-b border-primary/20 dark:border-primary/30 sidebar-header">
      <!-- Logo a la izquierda -->
      <div class="logo1 flex items-center justify-center size-10 bg-white rounded-full overflow-hidden mr-2">
        <img src="{{ asset('image/HoseBuilde.png') }}" alt="MedTalk Logo" class="w-8 h-8 object-contain">
      </div>
      <div class="flex flex-col ml-2 profile-info-minimal">
        <h1 class="text-xl font-bold text-white">Dashboard</h1>
        <div class="flex items-center gap-2">
          <h3 class="text-base font-semibold text-white mt-1">{{ Auth::user()->nombre }}</h3>
        </div>
        <span class="role text-xs text-white/60">Administrador</span>
      </div>
      <button class="toggle-btn ml-auto text-white hover:text-primary" id="toggle-btn">
        <i class="fas fa-bars"></i>
      </button>
    </div>
    <div class="flex flex-col p-6">
      <h2 class="text-base font-medium text-white/60">Panel de Administrador</h2>
      <p class="text-base text-white/60">Bienvenido, {{ Auth::user()->nombre }}</p>
    </div>
    <nav class="flex flex-col gap-2 px-4 pb-4" id="sidebarNav">
      <a href="{{ route('admin2.dashboard') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-white/80 hover:bg-primary/10 hover:text-primary transition-colors" data-section="dashboard">
        <span class="material-symbols-outlined text-xl">dashboard</span>
        Dashboard
      </a>
      @if(Route::has('reports'))
      <a href="{{ route('reports') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-white/80 hover:bg-primary/10 hover:text-primary transition-colors" data-section="reports">
        <span class="material-symbols-outlined text-xl">bar_chart</span>
        Generación de Reportes
      </a>
      @endif
      <a href="{{ route('aprobaciones.dashboard') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-white/80 hover:bg-primary/10 hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-xl">approval</span>
        Aprobaciones de Proyectos
      </a>
      @if(Route::has('materials'))
      <a href="{{ route('materials') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-white/80 hover:bg-primary/10 hover:text-primary transition-colors" data-section="materials">
        <span class="material-symbols-outlined text-xl">inventory_2</span>
        Gestión y Aprobación de Materiales
      </a>
      @endif
      @if(Route::has('users'))
      <a href="{{ route('users') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-white/80 hover:bg-primary/10 hover:text-primary transition-colors" data-section="users">
        <span class="material-symbols-outlined text-xl">group</span>
        Gestión de Usuarios según su Rol
      </a>
      @endif
      <a href="{{ route('usuarios.dashboard') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-white/80 hover:bg-primary/10 hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-xl">group</span>
        Gestión de Usuarios según su Rol
      </a>
      <!-- Sección de Acciones rápidas -->
      <a href="#" class="sidebar-link opacity-50 pointer-events-none text-white">
        <span class="material-symbols-outlined text-xl text-white">add_circle</span>
        Agregar Proyecto <span class="text-white/70">(En construcción)</span>
      </a>
      <a href="#" class="sidebar-link opacity-50 pointer-events-none text-white">
        <span class="material-symbols-outlined text-xl text-white">add_box</span>
        Agregar Material <span class="text-white/70">(En construcción)</span>
      </a>
      <!-- Fin acciones rápidas -->
    </nav>
  </div>
  <div class="p-6">
    <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-primary text-white font-medium mb-6 transition-colors">
      <span class="material-symbols-outlined text-white">Cerrar</span>
    </a>
    <div class="flex justify-center">
      <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAwa5aZxusfqMj_MQBlDKAAAiW6Yo2k0NcXSRrdKTE67XShjUCDnSAdS7Ju2u7Y7XEOStqFMz6KjFSGug97cvCbQvcUp73IMTDyYMPYUgdCKJZ1kkW-bARtP7tJs3YxFJuaaNw_VyqkNm9cdQQfa3odRQlEcq__p9G4jy-_EfPsIu8nhHDyIyf3gqZAj_b0P_hpnYq2kFDTn5is8TPe5fRNoupJ8C_HFWh7XKqCGxickxGUXVQbpnlLjl_3t7XbH6eLAZpblmAwryI");'></div>
    </div>
  </div>
</aside>