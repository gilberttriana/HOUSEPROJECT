@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Materiales</h2>
    @if(auth()->check() && in_array(auth()->user()->rol, ['proveedor','admin']))
      <div class="flex items-center gap-3">
        <button id="btnNuevoMaterial" class="px-4 py-2 bg-primary text-white rounded" {{ (empty($proveedoresList) ? 'disabled' : '') }}>Nuevo Material</button>
        <button id="btnExportAllMaterialsPdf" type="button" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" title="Exportar todos los materiales a PDF">Exportar PDF</button>
      </div>
      @if(empty($proveedoresList))
        <div class="text-xs text-yellow-300 mt-2">No hay proveedores registrados. Crea un proveedor antes de agregar materiales.</div>
      @endif
    @endif
  </div>
  @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>@endif
  <div class="overflow-x-auto bg-card-blue border border-accent-gold/6 rounded-xl p-6">
    <table class="w-full text-left" id="materialsTableStyled">
      <thead class="bg-accent-gold/6">
        <tr>
          <th class="p-5 text-base font-semibold text-white">Nombre</th>
          <th class="p-5 text-base font-semibold text-white">Proveedor</th>
          <th class="p-5 text-base font-semibold text-white">Precio</th>
          <th class="p-5 text-base font-semibold text-white">Stock</th>
          <th class="p-5 text-base font-semibold text-white">Descripción</th>
          <th class="p-5 text-base font-semibold text-white">Estado</th>
          <th class="p-5 text-base font-semibold text-white">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-accent-gold/10">
        @foreach($materiales as $m)
        @php
          $estRaw = $m->estado ?? ($m->ESTADO ?? 'pendiente');
          $estadoInfo = \App\Helpers\EstadoHelper::normalize($estRaw);
          $label = $estadoInfo['label'];
          $statusClass = $estadoInfo['class'];
          $fecha = '';
          try {
            $updatedVal = data_get($m,'updated_at') ?? data_get($m,'UPDATED_AT') ?? null;
            if ($updatedVal) { $fecha = \Carbon\Carbon::parse($updatedVal)->format('Y-m-d H:i'); }
          } catch (\Throwable $e) { $fecha = (string)($updatedVal ?? ''); }
        @endphp
  <tr class="cursor-pointer hover:bg-primary/10 material-row" data-id="{{ $m->id_material }}" data-nombre="{{ e($m->nombre) }}" data-descripcion="{{ e($m->descripcion ?? '') }}" data-cantidad="{{ intval($m->stock ?? $m->cantidad ?? 0) }}" data-estado="{{ e($estRaw) }}" data-fecha="{{ $fecha }}">
          <td class="p-5 text-base text-white">{{ $m->nombre }}</td>
          <td class="p-5 text-base text-white/80">{{ $m->proveedor->empresa ?? ($m->proveedor->usuario->nombre ?? $m->id_proveedor) }}</td>
          <td class="p-5 text-base text-white">{{ $m->precio }}</td>
          <td class="p-5 text-base text-white">{{ $m->stock }}</td>
          <td class="p-5 text-base text-white/80">{{ $m->descripcion ?? '' }}</td>
          <td class="p-5 text-base">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">{{ $label }}</span>
          </td>
          <td class="p-5 text-base">
            <div class="flex justify-end gap-2">
              @if(auth()->check() && in_array(auth()->user()->rol, ['proveedor','admin']))
                <button class="btn-edit px-3 py-1 bg-accent-gold text-[#111] rounded" data-id="{{ $m->id_material }}">Editar</button>
                <button class="btn-delete px-3 py-1 bg-red-600 text-white rounded" data-id="{{ $m->id_material }}">Eliminar</button>
              @else
                <span class="text-sm text-white/60">Sin permisos</span>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Modal reutilizable para crear/editar material (estilo igual al modal de proyectos) -->
  <div id="modalMaterial" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="display:none;">
    <div class="bg-[#182234] border border-primary/20 rounded-lg p-6 max-w-2xl w-11/12 relative">
      <button id="closeModalMaterial" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-primary">
        <span class="material-symbols-outlined text-2xl">close</span>
      </button>
      <h2 id="modalTitle" class="text-lg font-semibold text-white mb-2">Nuevo Material</h2>
      <p class="text-white/60 mb-3 text-sm">Rellena los campos para crear un nuevo material.</p>

      <form id="formMaterial" method="POST" action="{{ route('materiales.store') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input type="hidden" name="id_material" id="id_material" value="">
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Nombre</label>
          <input type="text" name="nombre" id="nombre" required class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Proveedor</label>
          @if(!empty($proveedoresList) && count($proveedoresList))
            <select name="id_proveedor" id="id_proveedor" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" required>
              @foreach($proveedoresList as $p)
                @php
                  $empresa = trim((string) ($p->proveedor->empresa ?? ''));
                  $label = $empresa !== '' ? $empresa . ' (' . ($p->nombre ?? 'Usuario '.$p->id_usuario) . ')' : ($p->nombre . ' ' . ($p->apellido ?? '') . ' (' . ($p->correo ?? '') . ')');
                @endphp
                <option value="{{ $p->id_usuario }}">{{ $label }}</option>
              @endforeach
            </select>
          @else
            <div class="text-sm text-yellow-300">No hay proveedores disponibles. Crea uno desde el panel de proveedores.</div>
          @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Unidad</label>
            <input type="text" name="unidad" id="unidad" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Precio</label>
            <input type="number" name="precio" id="precio" step="0.01" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" required />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Stock</label>
            <input type="number" name="stock" id="stock" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Imagen</label>
            <input type="file" name="imagen" id="imagen" class="w-full text-white" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Estado</label>
          <select name="estado" id="estado" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white">
            <option value="pendiente">En espera</option>
            <option value="aprobado">Aprobado</option>
            <option value="desaprobado">Desaprobado</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Descripción</label>
          <textarea name="descripcion" id="descripcion" rows="3" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"></textarea>
        </div>

        <div class="pt-4 flex gap-3">
          <button type="submit" id="btnSaveMaterial" class="w-full bg-primary text-white font-semibold py-2 px-3 rounded hover:bg-primary/90">Guardar</button>
          <button type="button" id="btnCloseModal" class="w-full bg-gray-700 text-white font-semibold py-2 px-3 rounded hover:bg-gray-600">Cancelar</button>
          <button type="button" id="btnSaveChanges" class="hidden w-full bg-green-600 text-white font-semibold py-2 px-3 rounded">Guardar Cambios</button>
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

      function openModal(){ if(modal) modal.style.display = 'flex'; }
      function closeModal(){ if(modal) modal.style.display = 'none'; if(form){ form.reset(); const hid = document.getElementById('id_material'); if(hid) hid.value = ''; } }

      try{
        if(btnNuevo && !btnNuevo.dataset.listenerAttached) {
          btnNuevo.addEventListener('click', ()=>{
            modalTitle.textContent='Nuevo Material';
            if(btnSave) btnSave.classList.remove('hidden');
            if(btnSaveChanges) btnSaveChanges.classList.add('hidden');
            openModal();
          });
          btnNuevo.dataset.listenerAttached = '1';
        }
        if(btnClose && !btnClose.dataset.listenerAttached) { btnClose.addEventListener('click', closeModal); btnClose.dataset.listenerAttached = '1'; }
        const closeX = document.getElementById('closeModalMaterial');
        if(closeX && !closeX.dataset.listenerAttached){ closeX.addEventListener('click', closeModal); closeX.dataset.listenerAttached = '1'; }
      } catch(e){ console.error('Modal init error', e); }

      // Edit button handler
      document.querySelectorAll('.btn-edit').forEach(btn=>{
        if(btn.dataset.listenerAttached) return; btn.dataset.listenerAttached = '1';
        btn.addEventListener('click', async (e)=>{
          try{
            const id = btn.getAttribute('data-id');
            console.log('[Materiales] fetching material for edit id=', id);
            const res = await fetch(`{{ url('/materiales') }}/${id}`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            if(!res.ok){ console.error('[Materiales] fetch edit failed', res.status); return alert('No se pudo obtener el material'); }
            const data = await res.json();
            console.log('[Materiales] fetched data for edit:', data);
            modalTitle.textContent='Editar Material';
            if(document.getElementById('id_material')) document.getElementById('id_material').value = data.id_material ?? data.id ?? id;
            if(document.getElementById('nombre')) document.getElementById('nombre').value = data.nombre ?? '';
            if(document.getElementById('unidad')) document.getElementById('unidad').value = data.unidad ?? '';
            if(document.getElementById('precio')) document.getElementById('precio').value = data.precio ?? '';
            if(document.getElementById('stock')) document.getElementById('stock').value = data.stock ?? '';
            if(document.getElementById('estado')) document.getElementById('estado').value = data.estado ?? 'pendiente';
            if(document.getElementById('descripcion')) document.getElementById('descripcion').value = data.descripcion ?? '';
            // seleccionar proveedor si existe (comparar id_usuario)
            const provSelect = document.getElementById('id_proveedor');
            if(provSelect && provSelect.options){
              for(const opt of provSelect.options){ if(parseInt(opt.value) === parseInt(data.id_usuario_proveedor || data.id_proveedor || 0)){ opt.selected = true; break; } }
            }
            if(btnSave) btnSave.classList.add('hidden');
            if(btnSaveChanges) btnSaveChanges.classList.remove('hidden');
            openModal();
          } catch(err){ console.error('Edit fetch error', err); alert('Error cargando material'); }
        })
      })

      // Delete handler
      document.querySelectorAll('.btn-delete').forEach(btn=>{
        if(btn.dataset.listenerAttached) return; btn.dataset.listenerAttached = '1';
        btn.addEventListener('click', async ()=>{
          try{
            if(!confirm('Eliminar material?')) return;
            const id = btn.getAttribute('data-id');
            const token = '{{ csrf_token() }}';
            const res = await fetch(`{{ url('/materiales') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, credentials: 'same-origin' });
            if(res.ok){ btn.closest('tr')?.remove(); } else { const text = await res.text(); console.error('Delete failed', res.status, text); alert('Error al eliminar: ' + res.status); }
          } catch(err){ console.error('Delete error', err); alert('Error al eliminar'); }
        })
      })

      // Update (SaveChanges) handler
      if(btnSaveChanges){
        btnSaveChanges.addEventListener('click', async (e)=>{
          e.preventDefault();
          try{
            const id = document.getElementById('id_material').value;
            const token = '{{ csrf_token() }}';
            const fd = new FormData(form);
            console.log('[Materiales] sending update, fd.estado=', fd.get('estado'));
            const res = await fetch(`{{ url('/materiales') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'X-HTTP-Method-Override': 'PATCH', 'Accept': 'application/json' }, body: fd, credentials: 'same-origin' });
            if(!res.ok){
              let errText = await res.text();
              try{ const j = JSON.parse(errText); errText = JSON.stringify(j); }catch(e){}
              console.error('Update failed', res.status, errText);
              alert('Error actualizando material: ' + res.status + '\n' + (errText || 'Revisa la consola para más detalles'));
              return;
            }
            const data = await res.json();
            console.log('[Materiales] server response data for update:', data);
            // actualizar fila en la tabla si existe
            const row = document.querySelector(`tr[data-id='${data.id_material ?? data.id ?? id}']`);
            if(row){
              // nombre
              if(row.querySelector('td:nth-child(1)')) row.querySelector('td:nth-child(1)').textContent = data.nombre ?? row.querySelector('td:nth-child(1)').textContent;
              row.dataset.nombre = row.querySelector('td:nth-child(1)').textContent;
              // proveedor
              if(data.proveedor_label){ if(row.querySelector('td:nth-child(2)')) row.querySelector('td:nth-child(2)').textContent = data.proveedor_label; }
              else {
                const provOpt = document.querySelector(`#id_proveedor option[value='${data.id_usuario_proveedor || data.id_proveedor}']`);
                if(row.querySelector('td:nth-child(2)')) row.querySelector('td:nth-child(2)').textContent = provOpt ? provOpt.textContent : (data.id_proveedor ?? row.querySelector('td:nth-child(2)').textContent);
              }
              if(row.querySelector('td:nth-child(3)')) row.querySelector('td:nth-child(3)').textContent = data.precio ?? row.querySelector('td:nth-child(3)').textContent;
              if(row.querySelector('td:nth-child(4)')) row.querySelector('td:nth-child(4)').textContent = data.stock ?? row.querySelector('td:nth-child(4)').textContent;
              row.dataset.cantidad = row.querySelector('td:nth-child(4)').textContent;
              if(row.querySelector('td:nth-child(5)')) row.querySelector('td:nth-child(5)').textContent = data.descripcion ?? row.querySelector('td:nth-child(5)').textContent;
              // estado: preferir el valor del formulario (fd) para reflejar la actualización inmediatamente;
              // si no existe, usar el valor devuelto por el servidor o el dataset actual.
              const estadoVal = (fd && fd.get && fd.get('estado')) ? fd.get('estado') : (data.estado ?? row.dataset.estado ?? 'pendiente');
              const estNorm = (estadoVal || 'pendiente').toString().toLowerCase();
              // normalizar posibles valores numéricos/booleanos a etiquetas conocidas
              let label = 'En espera';
              let statusClass = 'bg-yellow-500/20 text-yellow-500';
              // comprobar primero 'desap'/'rechaz' antes de 'apro' para evitar colisiones con la subcadena 'apro' en 'desaprobado'
              if(estNorm.indexOf('desap') !== -1 || estNorm.indexOf('rechaz') !== -1 || estNorm === '2' || estNorm === '-1'){
                label = 'Desaprobado'; statusClass = 'bg-red-600 text-white';
              } else if(estNorm === '1' || estNorm === 'true' || estNorm.indexOf('apro') !== -1){
                label = 'Aprobado'; statusClass = 'bg-green-600 text-white';
              } else if(estNorm === '0' || estNorm === 'false' || estNorm === '' || estNorm.indexOf('esper') !== -1 || estNorm.indexOf('pend') !== -1){
                label = 'En espera'; statusClass = 'bg-yellow-500/20 text-yellow-500';
              } else {
                label = estadoVal || data.estado || 'En espera'; statusClass = 'bg-primary/20 text-primary';
              }
              if(row.querySelector('td:nth-child(6)')){
                console.log('[Materiales] actualizar estado -> estadoVal:', estadoVal, 'estNorm:', estNorm, 'label:', label, 'statusClass:', statusClass, 'server.estado:', data.estado);
                row.querySelector('td:nth-child(6)').innerHTML = `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass}">${label}</span>`;
                console.log('[Materiales] after innerHTML, cell text:', row.querySelector('td:nth-child(6)').textContent);
              }
              // update dataset.estado
              row.dataset.estado = estadoVal ?? data.estado ?? row.dataset.estado ?? '';
              console.log('[Materiales] row.dataset.estado set to:', row.dataset.estado);
            }
            closeModal();
          } catch(err){ console.error('SaveChanges error', err); alert('Error actualizando material'); }
        });
      }

    })();
  </script>
  <!-- jsPDF import + export-all handler -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
    (function(){
      const btn = document.getElementById('btnExportAllMaterialsPdf');
      if(!btn) return;
      btn.addEventListener('click', async function(){
        try{
          const res = await fetch('{{ url('/materiales/json-all') }}', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
          if(!res.ok) throw new Error('No se pudo obtener la lista de materiales: ' + res.status);
          const rows = await res.json();

          const { jsPDF } = window.jspdf;
          const doc = new jsPDF();
          doc.setFillColor(16,34,52);
          doc.rect(0,0,210,25,'F');
          doc.setFontSize(16); doc.setTextColor(255,255,255); doc.text('Reporte de Inventario - Todos los Materiales', 14, 16);
          doc.setFontSize(11); doc.setTextColor(0,0,0);
          let y = 36;
          rows.forEach(r=>{
            doc.setFontSize(12); doc.text(`${r.nombre}`, 14, y);
            doc.setFontSize(11); doc.text(`Cantidad: ${r.cantidad}`, 110, y);
            doc.text(`Estado: ${r.estado ?? '-'}`, 150, y);
            y += 6;
            doc.setFontSize(10); doc.text(`Descripción: ${r.descripcion ?? '-'}`, 14, y);
            y += 12;
            if(y > 270){ doc.addPage(); y = 20; }
          });
          const fileName = 'Reporte_Materiales_Todos_' + new Date().toISOString().replace(/[:.]/g,'') + '.pdf';
          doc.save(fileName);
        }catch(err){ console.error('Export error', err); alert('Error generando PDF: ' + err.message); }
      });
    })();
  </script>
</div>
@endsection
