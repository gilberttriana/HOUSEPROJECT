<aside class="fixed left-0 top-0 flex flex-col w-72 bg-[#0f2a2a] border-r border-accent-gold/10 h-screen z-20">
  <!-- Header (logo + profile + toggle) -->
  <div class="flex items-center gap-3 p-6 border-b border-accent-gold/10 sidebar-header">
    <div class="flex items-center">
      <div class="logo1 flex items-center justify-center w-10 h-10 bg-accent-gold rounded-full overflow-hidden mr-3">
        <img src="{{ asset('image/HoseBuilde.png') }}" alt="MedTalk Logo" class="w-8 h-8 object-contain">
      </div>
      <div class="flex flex-col">
        <h1 class="text-xl font-bold text-[#F8F4EA]">HouseBuild</h1>
      </div>
    </div>
    <button class="toggle-btn ml-auto text-white hover:text-primary" id="toggle-btn" aria-label="Toggle sidebar">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  
  <div class="flex-1">
    <div class="p-6">
    </div>
    <nav class="flex flex-col gap-2 px-4 pb-6" id="sidebarNav">
      <a href="{{ route('admin2.dashboard') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-[#ECE7D9] hover:bg-accent-gold/5 hover:text-accent-gold transition-colors" data-section="dashboard">
        <span class="material-symbols-outlined text-xl">dashboard</span>
        Dashboard
      </a>
      @if(Route::has('reports'))
      <a href="{{ route('reports') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-white/80 hover:bg-primary/10 hover:text-primary transition-colors" data-section="reports">
        <span class="material-symbols-outlined text-xl">bar_chart</span>
        Generación de Reportes
      </a>
      @endif
      <a href="{{ route('aprobaciones.dashboard') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-[#ECE7D9] hover:bg-accent-gold/5 hover:text-accent-gold transition-colors">
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
      <a href="{{ route('usuarios.dashboard') }}" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl font-medium text-[#ECE7D9] hover:bg-accent-gold/5 hover:text-accent-gold transition-colors">
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

  <!-- Footer fixed area -->
    <div class="p-6 border-t border-accent-gold/10">
    <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-accent-gold text-[#2b2413] font-medium mb-4 transition-colors">
      <span class="material-symbols-outlined">logout</span>
      <span>Cerrar sesión</span>
    </a>
    <div class="flex justify-center">
      <div class="bg-center bg-no-repeat w-12 h-12 bg-cover rounded-full" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAwa5aZxusfqMj_MQBlDKAAAiW6Yo2k0NcXSRrdKTE67XShjUCDnSAdS7Ju2u7Y7XEOStqFMz6KjFSGug97cvCbQvcUp73IMTDyYMPYUgdCKJZ1kkW-bARtP7tJs3YxFJuaaNw_VyqkNm9cdQQfa3odRQlEcq__p9G4jy-_EfPsIu8nhHDyIyf3gqZAj_b0P_hpnYq2kFDTn5is8TPe5fRNoupJ8C_HFWh7XKqCGxickxGUXVQbpnlLjl_3t7XbH6eLAZpblmAwryI");'></div>
    </div>
  </div>
</aside>