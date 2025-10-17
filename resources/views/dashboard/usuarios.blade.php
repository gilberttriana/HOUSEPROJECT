@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  @if(session('success'))
  <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
  @endif
    <header class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Gestionar Usuarios</h2>
    @if(auth()->check() && auth()->user()->rol === 'admin')
    <div class="flex items-center space-x-3">
      <button id="btnAbrirReporte" class="inline-flex items-center px-3 py-2 border rounded bg-white text-gray-800 hover:bg-gray-50">Generar PDF</button>
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
    <!-- Modal para generar reporte -->
    <div id="reporteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
      <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Generar reporte de usuarios</h3>
          <button id="closeReporteModal" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form id="formReporte" method="GET" action="{{ route('usuarios.report') }}" target="_blank">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-700">Rol</label>
              <select name="role" class="w-full mt-1 px-3 py-2 border rounded">
                <option value="all">Todos</option>
                @foreach($roles as $r)
                  <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-sm text-gray-700">Desde</label>
              <input type="date" name="from" class="w-full mt-1 px-3 py-2 border rounded" />
            </div>
            <div>
              <label class="block text-sm text-gray-700">Hasta</label>
              <input type="date" name="to" class="w-full mt-1 px-3 py-2 border rounded" />
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <button type="button" id="cancelReporte" class="mr-3 px-4 py-2 rounded border">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Generar</button>
          </div>
        </form>
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
  <tbody data-rol="{{ $rol }}" class="bg-white dark:bg-transparent divide-y divide-gray-200 dark:divide-gray-800">
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
              @php $isCurrent = auth()->user()->id_usuario === $usuario->id_usuario; @endphp
              @if(!$isCurrent)
                <form method="POST" action="{{ route('usuarios.cambiarRol') }}" class="role-change-form">
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
                <div class="flex items-center space-x-2">
                  <select disabled class="border rounded px-2 py-1 text-sm bg-gray-100 text-gray-600" aria-disabled="true">
                    @foreach($roles as $r)
                      <option value="{{ $r }}" {{ $usuario->rol === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                  </select>
                  <button disabled class="ml-2 text-gray-400" title="No puedes cambiar tu propio rol">Guardar</button>
                  <span class="ml-2 text-xs text-gray-500">No puedes cambiar tu propio rol</span>
                </div>
              @endif
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
  
  // Report modal handlers
  function initReporteModal(){
    const btn = document.getElementById('btnAbrirReporte');
    const modal = document.getElementById('reporteModal');
    const close = document.getElementById('closeReporteModal');
    const cancel = document.getElementById('cancelReporte');
    if(btn && modal){
      // prevent duplicate listeners by cloning
      btn.replaceWith(btn.cloneNode(true));
      const newBtn = document.getElementById('btnAbrirReporte');
      if(newBtn) newBtn.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); setTimeout(()=>{ modal.querySelector('select[name=role]')?.focus(); }, 10); });
    }
    if(close && modal){ close.replaceWith(close.cloneNode(true)); const nc = document.getElementById('closeReporteModal'); if(nc) nc.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }); }
    if(cancel && modal){ cancel.replaceWith(cancel.cloneNode(true)); const can = document.getElementById('cancelReporte'); if(can) can.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }); }
    if(modal){
      if(!modal._listenerAdded){
        modal.addEventListener('click', function(e){ if(e.target === this){ this.classList.add('hidden'); this.classList.remove('flex'); } });
        modal._listenerAdded = true;
      }
    }
  }
  window.initReporteModal = initReporteModal;
  // Run on first load (handle case where DOMContentLoaded already fired)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReporteModal);
  } else {
    initReporteModal();
  }
  // Ensure it's called after AJAX navigation as well
  if (typeof window.afterAjaxLoad === 'function'){
    const __prevAfterReporte = window.afterAjaxLoad;
    window.afterAjaxLoad = function(){ try{ initReporteModal(); }catch(e){ console.error(e); } __prevAfterReporte(); }
  } else {
    window.afterAjaxLoad = initReporteModal;
  }
  
  // AJAX submit for role change forms using event delegation
  function initRoleChangeAjax(){
    // ensure we only attach one delegated listener
    if(window._roleChangeDelegated) return; window._roleChangeDelegated = true;
    document.addEventListener('submit', async function(e){
      const form = e.target;
      if(!form || !form.classList || !form.classList.contains('role-change-form')) return;
      e.preventDefault();
      // disable submit button to prevent double submits
      const submitBtn = form.querySelector('button[type=submit]');
      if(submitBtn) submitBtn.disabled = true;
      const data = new FormData(form);
      const tokenEl = document.querySelector('meta[name="csrf-token"]');
      const token = tokenEl ? tokenEl.getAttribute('content') : '';
      try{
        const res = await fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' }, body: data });
        const json = await res.json().catch(()=>({ success: false, message: 'Respuesta inválida' }));
        if(res.ok && json.success){
          showToast(json.message || 'Rol actualizado');
          // determine new role from response or fallback to select value
          const newRole = json.rol || form.querySelector('select[name=rol]')?.value;
          // update the select to reflect the new role
          const select = form.querySelector('select[name=rol]');
          if(select && newRole) select.value = newRole;
          // move the table row to the corresponding tbody for the new role
          if(newRole){
            const tr = form.closest('tr');
            const destTbody = document.querySelector('tbody[data-rol="' + newRole + '"]');
            if(tr && destTbody && tr.parentElement !== destTbody){
              destTbody.appendChild(tr);
            }
          }
        } else {
          showToast(json.message || 'Error al actualizar', true);
        }
      }catch(err){ console.error(err); showToast('Error de red', true); }
      finally{ if(submitBtn) submitBtn.disabled = false; }
    });
  }

  function showToast(message, isError){
    const containerId = 'toastContainerUsuarios';
    let container = document.getElementById(containerId);
    if(!container){ container = document.createElement('div'); container.id = containerId; container.style.position = 'fixed'; container.style.right = '20px'; container.style.bottom = '20px'; container.style.zIndex = 9999; document.body.appendChild(container); }
    const el = document.createElement('div');
    el.textContent = message;
    el.className = 'px-4 py-2 rounded shadow-md text-white ' + (isError ? 'bg-red-600' : 'bg-green-600');
    el.style.marginTop = '8px';
    container.appendChild(el);
    setTimeout(()=>{ el.style.transition = 'opacity 300ms'; el.style.opacity = '0'; setTimeout(()=> el.remove(), 350); }, 3000);
  }

  // ensure role-change AJAX handlers are bound after AJAX navigation
  document.addEventListener('DOMContentLoaded', function(){ initRoleChangeAjax(); });
  if(typeof window.afterAjaxLoad === 'function'){
    const prev = window.afterAjaxLoad;
    window.afterAjaxLoad = function(){ try{ initRoleChangeAjax(); }catch(e){console.error(e);} prev(); }
  } else {
    window.afterAjaxLoad = initRoleChangeAjax;
  }
</script>
@endsection
@endsection