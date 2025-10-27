<div id="addProjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="display:none;">
  <div class="bg-[#182234] border border-primary/20 rounded-lg p-6 max-w-4xl w-11/12 relative">
    <button id="closeAddProject" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-primary">
      <span class="material-symbols-outlined text-2xl">close</span>
    </button>
    <h2 class="text-lg font-semibold text-white mb-2">Agregar Proyecto</h2>
    <p class="text-white/60 mb-3 text-sm">Rellena los campos y selecciona materiales disponibles.</p>
    @php
      // Preferir datos inyectados por el controlador; si no vienen, cargarlos aquí desde la DB
      $materials = $materialsList ?? null;
      if (empty($materials)){
          $materials = \App\Models\Material::where('stock','>',0)->orderBy('nombre')->get();
      }
    @endphp

    <form id="formAddProject" action="{{ route('proyectos.store') }}" method="POST" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Nombre</label>
        <input name="nombre" required type="text" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Descripción</label>
        <textarea name="descripcion" rows="4" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white break-words whitespace-normal"></textarea>
      </div>
        <div>
          @php
            // Cargar usuarios con rol 'maestro' para el select de contratistas
            $maestros = \App\Models\Usuario::where('rol','maestro')->orderBy('nombre')->get();
          @endphp
          <label class="block text-sm font-medium text-white/80 mb-2">Contratista (Maestro)</label>
          <select name="contratista" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white">
            <option value="">-- Selecciona un contratista --</option>
            @foreach($maestros as $m)
              @php $fullname = trim(($m->nombre ?? '') . ' ' . ($m->apellido ?? '')); @endphp
              <option value="{{ e($fullname) }}">{{ e($fullname) }} @if(!empty($m->correo)) ({{ $m->correo }})@endif</option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Fecha Inicio</label>
          <input name="fecha_inicio" type="date" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
        </div>
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">Fecha Finalización</label>
            <input name="fecha_fin" type="date" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
          </div>
      </div>
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Presupuesto Estimado</label>
          <input name="presupuesto_est" type="number" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
        </div>
      {{-- El campo 'estado' se gestiona desde el apartado de aprobaciones; no se muestra/establece aquí. --}}
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Materiales (disponibles) — selecciona y añade cantidad</label>
        <div class="grid grid-cols-1 gap-2 max-h-56 overflow-y-auto p-2 bg-gray-900 rounded">
          @forelse($materials as $mat)
            @php
              $mid = $mat->id_material ?? $mat->id ?? null;
              $mname = $mat->nombre ?? $mat->name ?? 'Material';
              $stock = $mat->stock ?? $mat->cantidad ?? 0;
            @endphp
            <label class="flex items-center justify-between gap-3 bg-gray-800 p-2 rounded">
              <div class="flex items-center gap-3">
                <input type="checkbox" class="material-checkbox" data-id="{{ $mid }}" name="materials[{{ $mid }}][selected]" value="1" />
                <div class="text-white">{{ $mname }} <span class="text-xs text-white/60">({{ $stock }} en stock)</span></div>
              </div>
              <div class="flex items-center gap-2">
                <input type="number" name="materials[{{ $mid }}][cantidad]" min="1" max="{{ $stock }}" value="1" class="w-20 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-sm text-white material-qty" data-id="{{ $mid }}" disabled />
              </div>
            </label>
          @empty
            <div class="text-white/60">No hay materiales disponibles</div>
          @endforelse
        </div>
        <p class="text-xs text-white/60 mt-1">Marca cada material y especifica la cantidad necesaria.</p>
      </div>
      <div class="pt-4 flex gap-3">
        <button type="submit" class="w-full bg-primary text-white font-semibold py-2 px-3 rounded hover:bg-primary/90">Crear</button>
        <button id="cancelAddProject" type="button" class="w-full bg-gray-700 text-white font-semibold py-2 px-3 rounded hover:bg-gray-600">Cancelar</button>
      </div>
    </form>
  </div>
</div>
