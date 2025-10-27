<div id="addProjectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="display:none;">
  <div class="bg-[#182234] border border-primary/20 rounded-xl p-8 max-w-2xl w-full relative">
    <button id="closeAddProject" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-primary">
      <span class="material-symbols-outlined text-2xl">close</span>
    </button>
    <h2 class="text-2xl font-bold text-white mb-2">Agregar Proyecto</h2>
    <p class="text-white/60 mb-4">Complete los datos del proyecto y seleccione los materiales disponibles.</p>
    <form id="formAddProject" action="{{ route('proyectos.store') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Nombre</label>
        <input name="nombre" required type="text" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Descripción</label>
        <textarea name="descripcion" rows="2" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"></textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Contratista</label>
        <input name="contratista" type="text" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Fecha Inicio</label>
          <input name="fecha_inicio" type="date" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
        </div>
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Fecha Fin</label>
          <input name="fecha_fin" type="date" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Presupuesto Estimado</label>
        <input name="presupuesto_est" type="number" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white" />
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Estado</label>
        <select name="estado" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white">
          <option>En Curso</option>
          <option>Completado</option>
          <option>En Espera</option>
          <option>Cancelado</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Materiales (solo disponibles)</label>
        <select name="materials[]" multiple class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white h-36">
          @foreach(($materialsList ?? collect()) as $mat)
            @php $mid = $mat->id_material ?? $mat->id ?? null; $mname = $mat->nombre ?? $mat->name ?? 'Material'; $stock = $mat->stock ?? $mat->cantidad ?? 0; @endphp
            <option value="{{ $mid }}">{{ $mname }} ({{ $stock }})</option>
          @endforeach
        </select>
        <p class="text-xs text-white/60 mt-1">Mantenga presionado Ctrl/Cmd para seleccionar varios materiales.</p>
      </div>
      <div class="pt-4 flex gap-3">
        <button type="submit" class="w-full bg-primary text-white font-bold py-3 px-4 rounded-lg hover:bg-primary/90">Crear Proyecto</button>
        <button id="cancelAddProject" type="button" class="w-full bg-gray-700 text-white font-bold py-3 px-4 rounded-lg hover:bg-gray-600">Cancelar</button>
      </div>
    </form>
  </div>
</div>
