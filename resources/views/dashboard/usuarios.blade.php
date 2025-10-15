@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  @if(session('success'))
  <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
  @endif
  <header class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Gestionar Usuarios</h2>
    @if(auth()->check() && auth()->user()->rol === 'admin')
    <div>
      <button id="btnNuevoUsuario" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">Nuevo usuario</button>
    </div>
    @endif
  </header>
  <div class="mb-6">
    <div class="relative">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
      <input class="w-full pl-10 pr-4 py-2 rounded-lg bg-white dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 focus:ring-primary focus:border-primary text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="Buscar usuarios por nombre o correo electrónico" type="text"/>
    </div>
  </div>
  <div class="space-y-12">
   @foreach($roles as $rol)
  <div>
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
      {{ ucfirst($rol) }}{{ $rol == 'maestro' ? 's de Obra' : ($rol == 'usuario' ? 's' : 'es') }}
    </h3>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
      <thead class="bg-gray-50 dark:bg-gray-800/50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nombre</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Correo Electrónico</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
          <th class="relative px-6 py-3"><span class="sr-only">Acciones</span></th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-800">
        @forelse($usuariosPorRol[$rol] as $usuario)
        <tr>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
            {{ $usuario->nombre }} {{ $usuario->apellido }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
            {{ $usuario->correo }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            @php
              $estado = strtolower($usuario->estado);
              $badgeClass =
                $estado === 'activo'
                  ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300'
                  : ($estado === 'inactivo'
                    ? 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300'
                    : 'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300');
            @endphp
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
              {{ ucfirst($usuario->estado) }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            @if(auth()->check() && auth()->user()->rol === 'admin')
            <form method="POST" action="{{ route('usuarios.cambiarRol') }}">
              @csrf
              <input type="hidden" name="usuario_id" value="{{ $usuario->id_usuario }}" />
              <select name="rol" class="border rounded px-2 py-1 text-sm">
                @foreach($roles as $r)
                  <option value="{{ $r }}" {{ $usuario->rol === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
              </select>
              <button type="submit" class="ml-2 text-primary hover:text-primary/80">Guardar</button>
            </form>
            @else
            <span class="text-gray-500">Sin permisos</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="px-6 py-4 text-center text-gray-400">No hay usuarios en este rol.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endforeach
  </div>
</div>
<!-- Modal para crear usuario -->
<div id="nuevoUsuarioModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
  <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-2xl p-6">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Registrar nuevo usuario</h3>
      <button id="closeNuevoUsuario" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>
    <form method="POST" action="{{ route('usuarios.store') }}">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Nombre</label>
          <input name="nombre" type="text" required class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Apellido</label>
          <input name="apellido" type="text" required class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Correo electrónico</label>
          <input name="correo" type="email" required class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Rol</label>
          <select name="rol" required class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @foreach($roles as $r)
              <option value="{{ $r }}">{{ ucfirst($r) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Contraseña</label>
          <input name="contrasena" type="password" required class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
        </div>
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Confirmar contraseña</label>
          <input name="contrasena_confirmation" type="password" required class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
        </div>
      </div>
      <div class="mt-4 flex justify-end">
        <button type="button" id="cancelNuevoUsuario" class="mr-3 px-4 py-2 rounded border">Cancelar</button>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Crear usuario</button>
      </div>
    </form>
  </div>
</div>

@section('scripts')
<script>
  // Expose initializer so layout AJAX loader can rebind handlers
  function initUsuariosModal(){
    const btn = document.getElementById('btnNuevoUsuario');
    const modal = document.getElementById('nuevoUsuarioModal');
    const close = document.getElementById('closeNuevoUsuario');
    const cancel = document.getElementById('cancelNuevoUsuario');
    if(btn && modal){
      // prevent adding duplicate listeners: remove by cloning
      btn.replaceWith(btn.cloneNode(true));
      const newBtn = document.getElementById('btnNuevoUsuario');
      newBtn.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); setTimeout(()=>{ modal.querySelector('input[name=nombre]')?.focus(); }, 10); });
    }
    if(close && modal){ close.replaceWith(close.cloneNode(true)); document.getElementById('closeNuevoUsuario').addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }); }
    if(cancel && modal){ cancel.replaceWith(cancel.cloneNode(true)); document.getElementById('cancelNuevoUsuario').addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }); }
    if(modal){ modal.addEventListener('click', function(e){ if(e.target === this){ this.classList.add('hidden'); this.classList.remove('flex'); } }); }
  }

  window.initUsuariosModal = initUsuariosModal;
  // run on first load
  document.addEventListener('DOMContentLoaded', function(){ initUsuariosModal(); });
</script>
@endsection
@endsection