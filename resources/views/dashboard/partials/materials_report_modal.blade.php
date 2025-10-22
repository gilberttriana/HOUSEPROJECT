<!-- Partial: Modal de reporte de materiales -->
<div id="materialsReportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60">
  <div class="bg-[#182234] border border-primary/20 rounded-xl p-6 max-w-2xl w-full relative">
    <button id="closeMaterialsReport" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-primary">✕</button>
    <h2 class="text-2xl font-bold text-white mb-3">Reporte de Materiales</h2>
    <p class="text-white/70 mb-4">Elige si deseas generar un reporte del material seleccionado o de todos los materiales.</p>

    <div class="space-y-4">
      <div>
        <label class="inline-flex items-center text-white mr-4"><input type="radio" name="materialsScope" value="single" checked class="mr-2"> Material seleccionado</label>
        <label class="inline-flex items-center text-white"><input type="radio" name="materialsScope" value="all" class="mr-2"> Todos los materiales</label>
      </div>

      <div id="selectedMaterialInfo" class="bg-gray-800 p-3 rounded text-white">
        <div><strong>Material:</strong> <span id="matName">-</span></div>
        <div><strong>Descripción:</strong> <span id="matDesc">-</span></div>
        <div><strong>Cantidad:</strong> <span id="matQty">-</span></div>
        <div><strong>Actualizado:</strong> <span id="matFecha">-</span></div>
      </div>

      <div class="pt-4 flex gap-3">
        <button id="btnMaterialsPdf" type="button" class="w-full bg-primary text-white font-bold py-3 px-4 rounded-lg hover:bg-primary/90">Generar PDF</button>
        <button id="btnMaterialsExcel" type="button" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700">Generar Excel</button>
      </div>
    </div>
  </div>
</div>
