<aside class="fixed inset-y-0 left-0 flex flex-col justify-between w-72 bg-[#0f172a] border-r border-[#273449] shadow-lg overflow-y-auto">
    <!-- Perfil proveedor -->
    <div class="flex flex-col items-center pt-10 pb-8">
        <div class="bg-[#f5e6c1] rounded-full p-2 shadow mb-4 border border-[#223146]">
            <img src="{{ asset('image/HoseBuilde.png') }}" alt="Proveedor Logo" class="w-16 h-16 object-contain rounded-full">
        </div>

        @if(Auth::check())
            <h2 class="text-xl font-bold text-white text-center mb-1 leading-tight">
                {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
            </h2>
            <span class="text-[#d4af37] font-semibold text-center mb-2">Proveedor</span>
            <div class="text-xs text-white/80 text-center leading-relaxed space-y-1 px-4 mb-2">
                <div><span class="font-medium">Correo:</span> <span class="whitespace-nowrap">{{ Auth::user()->correo }}</span></div>
                <div><span class="font-medium">ID Proveedor:</span> <span>{{ Auth::user()->id_usuario }}</span></div>
                <div><span class="font-medium">Fecha de registro:</span> <span>{{ Auth::user()->fecha_registro ?? '---' }}</span></div>
            </div>
        @else
            <h2 class="text-xl font-bold text-white text-center mb-1 leading-tight">Invitado</h2>
            <span class="text-[#3b82f6] font-semibold text-center mb-2">Sin sesión</span>
            <div class="text-xs text-white/80 text-center leading-relaxed space-y-1 px-4 mb-2">
                <div><span class="font-medium">Correo:</span> <span class="whitespace-nowrap">-</span></div>
                <div><span class="font-medium">ID Proveedor:</span> <span>-</span></div>
                <div><span class="font-medium">Fecha de registro:</span> <span>-</span></div>
            </div>
        @endif
    </div>

    <!-- Navegación rápida para proveedor -->
    <div class="px-6 mb-4">
        <nav class="flex flex-col gap-2">
            @if(auth()->check() && auth()->user()->rol === 'proveedor')
                <a href="{{ route('proveedor.stats') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#0b1220]/40 text-white hover:bg-[#0b1220]/60 transition">
                    <span class="material-symbols-outlined">bar_chart</span>
                    <span class="font-medium">Estadísticas</span>
                </a>
            @endif
        </nav>
    </div>
    
    <!-- Botón volver al dashboard del proveedor -->
    <div class="px-6 mb-4">
        <a href="{{ route('proveedor.dashboard') }}" class="w-full inline-flex items-center justify-center gap-3 px-4 py-3 rounded-xl bg-transparent border border-[#2b3b4b] text-white hover:bg-[#0b1220]/60 transition">
            <span class="material-symbols-outlined">arrow_back</span>
            <span class="font-medium">Volver al dashboard</span>
        </a>
    </div>

    <!-- Botón cerrar sesión -->
    <div class="px-6 pb-8 border-t border-[#273449]">
        @if(Auth::check())
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-[#d4af37] text-[#0f1724] font-bold text-base tracking-wide flex items-center justify-center gap-2 hover:bg-[#b78f2a] transition shadow-lg">
                    <span class="material-symbols-outlined">logout</span>
                    Cerrar sesión
                </button>
            </form>
        @endif
    </div>
</aside>
