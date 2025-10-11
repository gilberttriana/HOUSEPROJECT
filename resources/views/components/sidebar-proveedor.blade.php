<aside class="flex flex-col justify-between h-screen w-72 bg-[#13202A] border-r border-primary/20 dark:border-primary/30 shadow">
    <!-- Perfil proveedor -->
    <div class="flex flex-col items-center pt-10 pb-8">
        <div class="bg-white rounded-full p-2 shadow mb-4">
            <img src="{{ asset('image/HoseBuilde.png') }}" alt="Proveedor Logo" class="w-16 h-16 object-contain rounded-full">
        </div>
        @if(Auth::check())
            <h2 class="text-xl font-bold text-white text-center mb-1 leading-tight">
                {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}
            </h2>
            <span class="text-primary font-semibold text-center mb-2">Proveedor</span>
            <div class="text-xs text-white/80 text-center leading-relaxed space-y-1 px-4 mb-2">
                <div>
                    <span class="font-medium">Correo:</span>
                    <span class="whitespace-nowrap">{{ Auth::user()->correo }}</span>
                </div>
                <div>
                    <span class="font-medium">ID Proveedor:</span>
                    <span>{{ Auth::user()->id_usuario }}</span>
                </div>
                <div>
                    <span class="font-medium">Fecha de registro:</span>
                    <span>{{ Auth::user()->fecha_registro ?? '---' }}</span>
                </div>
            </div>
        @else
            <h2 class="text-xl font-bold text-white text-center mb-1 leading-tight">
                Invitado
            </h2>
            <span class="text-primary font-semibold text-center mb-2">Sin sesión</span>
            <div class="text-xs text-white/80 text-center leading-relaxed space-y-1 px-4 mb-2">
                <div>
                    <span class="font-medium">Correo:</span>
                    <span class="whitespace-nowrap">-</span>
                </div>
                <div>
                    <span class="font-medium">ID Proveedor:</span>
                    <span>-</span>
                </div>
                <div>
                    <span class="font-medium">Fecha de registro:</span>
                    <span>-</span>
                </div>
            </div>
        @endif
    </div>
    <!-- Botón cerrar sesión pegado abajo y estilizado -->
    <div class="px-6 pb-8 w-full">
        @if(Auth::check())
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full py-3 rounded-xl bg-primary text-white font-bold text-base tracking-wide flex items-center justify-center gap-2 hover:bg-primary/90 transition shadow-lg">
                <span class="material-symbols-outlined">logout</span>
                Cerrar sesión
            </button>
        </form>
        @endif
    </div>
</aside>