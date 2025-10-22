@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Materiales</h2>
    @if(auth()->check() && in_array(auth()->user()->rol, ['proveedor','admin']))
      <button id="btnNuevoMaterial" class="px-4 py-2 bg-primary text-white rounded" {{ (empty($proveedoresList) ? 'disabled' : '') }}>Nuevo Material</button>
      @if(empty($proveedoresList))
        <div class="text-xs text-yellow-300 mt-2">No hay proveedores registrados. Crea un proveedor antes de agregar materiales.</div>
      @endif
    @endif
  </div>
  @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>@endif
  <div class="bg-white rounded shadow p-4">
    <table class="w-full text-sm table-auto">
      <thead>
        <tr class="text-left text-xs text-gray-500">
          <th>Nombre</th>
              <th>Proveedor</th>
          <th>Precio</th>
          <th>Stock</th>
              <th>Descripción</th>
          <th>Estado</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($materiales as $m)
        <tr class="border-t" data-id="{{ $m->id_material }}">
          <td class="py-2">{{ $m->nombre }}</td>
          <td class="py-2">{{ $m->proveedor->empresa ?? ($m->proveedor->usuario->nombre ?? $m->id_proveedor) }}</td>
          <td class="py-2">{{ $m->precio }}</td>
          <td class="py-2">{{ $m->stock }}</td>
              <td class="py-2">{{ $m->descripcion ?? '' }}</td>
          <td class="py-2">
            @php
              $est = $m->estado ?? 'pendiente';
              $label = $est === 'aprobado' ? 'Aprobado' : ($est === 'desaprobado' ? 'Desaprobado' : 'En espera');
            @endphp
            <span class="text-sm">{{ $label }}</span>
          </td>
          <td class="py-2 text-right space-x-2">
            @if(auth()->check() && in_array(auth()->user()->rol, ['proveedor','admin']))
              <button class="btn-edit px-3 py-1 bg-yellow-500 text-white rounded" data-id="{{ $m->id_material }}">Editar</button>
              <button class="btn-update hidden px-3 py-1 bg-green-600 text-white rounded" data-id="{{ $m->id_material }}">Actualizar</button>
              <button class="btn-delete px-3 py-1 bg-red-600 text-white rounded" data-id="{{ $m->id_material }}">Eliminar</button>
            @else
              <span class="text-sm text-gray-500">Sin permisos</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Modal reutilizable para crear/editar material -->
  <div id="modalMaterial" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded w-11/12 max-w-2xl">
      <h3 id="modalTitle" class="text-xl font-bold mb-4">Nuevo Material</h3>
      <form id="formMaterial" method="POST" action="{{ route('materiales.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id_material" id="id_material" value="">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="w-full border px-3 py-2 rounded" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Proveedor</label>
            @if(!empty($proveedoresList) && count($proveedoresList))
              <select name="id_proveedor" id="id_proveedor" class="w-full border px-3 py-2 rounded" required>
                @foreach($proveedoresList as $p)
                    @php
                      // $p es ahora un Usuario con posible relación proveedor
                      $empresa = trim((string) ($p->proveedor->empresa ?? ''));
                      $label = $empresa !== '' ? $empresa . ' (' . ($p->nombre ?? 'Usuario '.$p->id_usuario) . ')' : ($p->nombre . ' ' . ($p->apellido ?? '') . ' (' . ($p->correo ?? '') . ')');
                    @endphp
                    <option value="{{ $p->id_usuario }}">{{ $label }}</option>
                  @endforeach
              </select>
            @else
              <div class="text-sm text-gray-500">No hay proveedores disponibles. Crea uno desde el panel de proveedores.</div>
            @endif
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Unidad</label>
            <input type="text" name="unidad" id="unidad" class="w-full border px-3 py-2 rounded">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Precio</label>
            <input type="number" name="precio" id="precio" step="0.01" class="w-full border px-3 py-2 rounded" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Stock</label>
            <input type="number" name="stock" id="stock" class="w-full border px-3 py-2 rounded">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Imagen</label>
            <input type="file" name="imagen" id="imagen" class="w-full">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Estado</label>
            <select name="estado" id="estado" class="w-full border px-3 py-2 rounded">
              <option value="pendiente">En espera</option>
              <option value="aprobado">Aprobado</option>
              <option value="desaprobado">Desaprobado</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="w-full border px-3 py-2 rounded" rows="3"></textarea>
          </div>
        </div>
        <div class="mt-4 text-right">
          <button type="button" id="btnCloseModal" class="px-4 py-2 mr-2">Cancelar</button>
          <button type="submit" id="btnSaveMaterial" class="px-4 py-2 bg-primary text-white rounded">Guardar</button>
          <button type="button" id="btnSaveChanges" class="hidden px-4 py-2 bg-green-600 text-white rounded">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Manejo básico del modal y acciones CRUD (peticiones fetch)
    (function(){
      const modal = document.getElementById('modalMaterial');
      const btnNuevo = document.getElementById('btnNuevoMaterial');
      const btnClose = document.getElementById('btnCloseModal');
      const form = document.getElementById('formMaterial');
      const modalTitle = document.getElementById('modalTitle');
      const btnSave = document.getElementById('btnSaveMaterial');
      const btnSaveChanges = document.getElementById('btnSaveChanges');

  function openModal(){ modal.classList.remove('hidden'); modal.classList.add('flex'); }
  function closeModal(){ modal.classList.add('hidden'); modal.classList.remove('flex'); form.reset(); document.getElementById('id_material').value=''; }

  if(btnNuevo && !btnNuevo.dataset.listenerAttached) { btnNuevo.addEventListener('click', ()=>{ modalTitle.textContent='Nuevo Material'; btnSave.classList.remove('hidden'); btnSaveChanges.classList.add('hidden'); openModal(); }); btnNuevo.dataset.listenerAttached = '1'; }
  if(btnClose && !btnClose.dataset.listenerAttached) { btnClose.addEventListener('click', closeModal); btnClose.dataset.listenerAttached = '1'; }

      // Edit button handler
      document.querySelectorAll('.btn-edit').forEach(btn=>{
        if(btn.dataset.listenerAttached) return; btn.dataset.listenerAttached = '1';
        btn.addEventListener('click', async (e)=>{
          const id = btn.getAttribute('data-id');
          // fetch data
          const res = await fetch(`{{ url('/materiales') }}/${id}`);
          if(!res.ok) return alert('No se pudo obtener el material');
          const data = await res.json();
          // fill form
          modalTitle.textContent='Editar Material';
          document.getElementById('id_material').value = data.id_material;
          document.getElementById('nombre').value = data.nombre;
          document.getElementById('unidad').value = data.unidad ?? '';
          document.getElementById('precio').value = data.precio;
          document.getElementById('stock').value = data.stock ?? '';
          // estado
          if(document.getElementById('estado')){
            document.getElementById('estado').value = data.estado ?? 'pendiente';
          }
          // descripcion
          if(document.getElementById('descripcion')){
            document.getElementById('descripcion').value = data.descripcion ?? '';
          }
          // seleccionar proveedor si existe (comparar id_usuario)
          const provSelect = document.getElementById('id_proveedor');
          for(const opt of provSelect.options){ if(parseInt(opt.value) === parseInt(data.id_usuario_proveedor || data.id_proveedor)){ opt.selected = true; break; } }
          btnSave.classList.add('hidden'); btnSaveChanges.classList.remove('hidden'); openModal();
        })
      })

      // Delete handler
      document.querySelectorAll('.btn-delete').forEach(btn=>{
        if(btn.dataset.listenerAttached) return; btn.dataset.listenerAttached = '1';
        btn.addEventListener('click', async ()=>{
          if(!confirm('Eliminar material?')) return;
          const id = btn.getAttribute('data-id');
          const token = '{{ csrf_token() }}';
          const res = await fetch(`{{ url('/materiales') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token }});
          if(res.ok){ btn.closest('tr').remove(); } else { alert('Error al eliminar'); }
        })
      })

      // El estado se muestra desde la base de datos; para cambiarlo use el modal de edición

      // SaveChanges (update)
      btnSaveChanges.addEventListener('click', async (e)=>{
        e.preventDefault();
        const id = document.getElementById('id_material').value;
        const token = '{{ csrf_token() }}';
        const fd = new FormData(form);
        // usar PATCH (simulado via POST + override) pero aceptar JSON de respuesta
        const res = await fetch(`{{ url('/materiales') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'X-HTTP-Method-Override': 'PATCH' }, body: fd });
        if(res.ok){
          // intentar parsear JSON
          let data = null;
          try{ data = await res.json(); } catch(e){ data = null; }
          if(data){
            // actualizar fila en la tabla
            const row = document.querySelector(`tr[data-id='${data.id_material}']`);
            if(row){
              row.querySelector('td:nth-child(1)').textContent = data.nombre ?? row.querySelector('td:nth-child(1)').textContent;
              // proveedor: preferir label devuelto por el servidor si existe
              const provLabel = data.proveedor_label || null;
              if(provLabel){ row.querySelector('td:nth-child(2)').textContent = provLabel; }
              else {
                const provOpt = document.querySelector(`#id_proveedor option[value='${data.id_usuario_proveedor || data.id_proveedor}']`);
                row.querySelector('td:nth-child(2)').textContent = provOpt ? provOpt.textContent : (data.id_proveedor ?? row.querySelector('td:nth-child(2)').textContent);
              }
              row.querySelector('td:nth-child(3)').textContent = data.precio ?? row.querySelector('td:nth-child(3)').textContent;
              row.querySelector('td:nth-child(4)').textContent = data.stock ?? row.querySelector('td:nth-child(4)').textContent;
              row.querySelector('td:nth-child(5)').textContent = data.descripcion ?? row.querySelector('td:nth-child(5)').textContent;
              const est = data.estado ?? 'pendiente';
              const label = est === 'aprobado' ? 'Aprobado' : (est === 'desaprobado' ? 'Desaprobado' : 'En espera');
              row.querySelector('td:nth-child(6)').textContent = label;
            }
            closeModal();
            return;
          }
          // si no hay json, recargar como fallback
          location.reload();
        } else { alert('Error actualizando material'); }
      })

    })();
  </script>
</div>
@endsection
