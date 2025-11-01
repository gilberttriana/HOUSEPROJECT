<div id="projectDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="display:none;">
  <div class="bg-[#182234] border border-primary/20 rounded-lg p-4 max-w-2xl w-11/12 relative text-sm">
    <button id="closeProjectDetails" type="button" class="absolute top-3 right-3 text-gray-400 hover:text-primary">
      <span class="material-symbols-outlined text-2xl">close</span>
    </button>
    <h3 id="pd_name" class="text-lg font-semibold text-white mb-1">Proyecto</h3>
    <div class="text-white/60 text-sm mb-3" id="pd_solicitante">Solicitante</div>

    <div class="grid grid-cols-1 gap-3">
      <div>
        <label class="block text-xs text-white/60">Descripción</label>
        <div id="pd_descripcion" class="text-white text-sm break-words whitespace-normal"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
        <div>
          <label class="block text-xs text-white/60">Fecha Inicio</label>
          <div id="pd_fecha_inicio" class="text-white text-sm"></div>
        </div>
        <div>
          <label class="block text-xs text-white/60">Fecha Final</label>
          <div id="pd_fecha_fin" class="text-white text-sm"></div>
        </div>
        <div>
          <label class="block text-xs text-white/60">Presupuesto</label>
          <div id="pd_presupuesto" class="text-white text-sm"></div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
        <div>
          <label class="block text-xs text-white/60">Estado</label>
          <div id="pd_estado" class="text-white text-sm"></div>
        </div>
        <div>
          <label class="block text-xs text-white/60">Progreso</label>
          <div id="pd_progreso" class="text-white text-sm"></div>
        </div>
      </div>

      <div>
        <label class="block text-xs text-white/60">Materiales</label>
        <div id="pd_materiales" class="mt-1 bg-gray-900 p-2 rounded max-h-48 overflow-y-auto text-sm text-white"></div>
      </div>

    </div>

    <div class="mt-4 text-right">
      <button id="pd_close_btn" class="inline-flex h-8 items-center justify-center rounded-md bg-gray-700 px-3 text-xs font-medium text-white hover:bg-gray-600">Cerrar</button>
    </div>
  </div>
</div>