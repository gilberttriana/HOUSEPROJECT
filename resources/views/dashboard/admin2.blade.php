@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  <!-- Resumen Ejecutivo -->
  <section class="mb-10">
    <h2 class="text-4xl font-bold text-white mb-8">Resumen Ejecutivo</h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 bg-[#182234] border border-primary/20 rounded-xl p-6">
        <h3 class="text-xl font-medium text-white mb-4">Estado de Proyectos</h3>
        <div class="h-80 flex items-center justify-center">
          <canvas id="projectsChart" width="600" height="300"></canvas>
        </div>
      </div>
      <div class="space-y-8">
        <div class="bg-[#182234] border border-primary/20 rounded-xl p-6 text-white">
          <h3 class="text-lg font-medium">Proyectos en Curso</h3>
          <p class="text-5xl font-bold mt-2">15</p>
        </div>
        <div class="bg-[#182234] border border-primary/20 rounded-xl p-6 text-white">
          <h3 class="text-lg font-medium">Proyectos por Aceptar</h3>
          <p class="text-5xl font-bold mt-2">8</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Tabla de proyectos -->
  <section>
    <h2 class="text-3xl font-bold text-white mb-6">Proyectos</h2>
    <div class="overflow-x-auto bg-[#182234] border border-primary/20 rounded-xl">
      <table class="w-full text-left" id="projectsTable">
        <thead class="bg-primary/20">
          <tr>
            <th class="p-5 text-base font-semibold text-white">Nombre del Proyecto</th>
            <th class="p-5 text-base font-semibold text-white">Estado</th>
            <th class="p-5 text-base font-semibold text-white">Progreso</th>
            <th class="p-5 text-base font-semibold text-white">Fecha de Inicio</th>
            <th class="p-5 text-base font-semibold text-white">Fecha de Finalización</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-primary/20">
          <tr class="cursor-pointer hover:bg-primary/10"
              data-name="Proyecto A"
              data-contractor="Constructora XYZ"
              data-materials="Cemento, Acero, Madera, Vidrio"
              data-estimated_time="24"
              data-material_costs="100000"
              data-labor_costs="50000"
              data-status="En Curso">
            <td class="p-5 text-base text-white font-medium"><span class="hover:underline">Proyecto A</span></td>
            <td class="p-5 text-base">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary">En Curso</span>
            </td>
            <td class="p-5 text-base text-white">
              <div class="flex items-center gap-3">
                <div class="w-32 bg-primary/20 rounded-full h-2.5">
                  <div class="bg-primary h-2.5 rounded-full" style="width: 60%"></div>
                </div>
                <span>60%</span>
              </div>
            </td>
            <td class="p-5 text-base text-white">2025-01-15</td>
            <td class="p-5 text-base text-white">2025-06-30</td>
          </tr>
          <tr class="cursor-pointer hover:bg-primary/10"
              data-name="Proyecto B"
              data-contractor="Constructora ABC"
              data-materials="Acero, Arena, Ladrillo"
              data-estimated_time="18"
              data-material_costs="80000"
              data-labor_costs="40000"
              data-status="Completado">
            <td class="p-5 text-base text-white font-medium"><span class="hover:underline">Proyecto B</span></td>
            <td class="p-5 text-base">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-500">Completado</span>
            </td>
            <td class="p-5 text-base text-white">
              <div class="flex items-center gap-3">
                <div class="w-32 bg-primary/20 rounded-full h-2.5">
                  <div class="bg-primary h-2.5 rounded-full" style="width: 100%"></div>
                </div>
                <span>100%</span>
              </div>
            </td>
            <td class="p-5 text-base text-white">2025-11-01</td>
            <td class="p-5 text-base text-white">2026-03-15</td>
          </tr>
          <tr class="cursor-pointer hover:bg-primary/10"
              data-name="Proyecto C"
              data-contractor="Constructora QRS"
              data-materials="Madera, Vidrio"
              data-estimated_time="14"
              data-material_costs="45000"
              data-labor_costs="32000"
              data-status="En Curso">
            <td class="p-5 text-base text-white font-medium"><span class="hover:underline">Proyecto C</span></td>
            <td class="p-5 text-base">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary">En Curso</span>
            </td>
            <td class="p-5 text-base text-white">
              <div class="flex items-center gap-3">
                <div class="w-32 bg-primary/20 rounded-full h-2.5">
                  <div class="bg-primary h-2.5 rounded-full" style="width: 30%"></div>
                </div>
                <span>30%</span>
              </div>
            </td>
            <td class="p-5 text-base text-white">2025-02-20</td>
            <td class="p-5 text-base text-white">2025-08-20</td>
          </tr>
          <tr class="cursor-pointer hover:bg-primary/10"
              data-name="Proyecto D"
              data-contractor="Constructora DEF"
              data-materials="Hormigón, Arena"
              data-estimated_time="20"
              data-material_costs="70000"
              data-labor_costs="37000"
              data-status="En Espera">
            <td class="p-5 text-base text-white font-medium"><span class="hover:underline">Proyecto D</span></td>
            <td class="p-5 text-base">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500/20 text-yellow-500">En Espera</span>
            </td>
            <td class="p-5 text-base text-white">
              <div class="flex items-center gap-3">
                <div class="w-32 bg-primary/20 rounded-full h-2.5">
                  <div class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                </div>
                <span>0%</span>
              </div>
            </td>
            <td class="p-5 text-base text-white">2025-03-10</td>
            <td class="p-5 text-base text-white">2025-09-10</td>
          </tr>
          <tr class="cursor-pointer hover:bg-primary/10"
              data-name="Proyecto E"
              data-contractor="Constructora ZZZ"
              data-materials="Cemento, Hierro"
              data-estimated_time="16"
              data-material_costs="60000"
              data-labor_costs="28000"
              data-status="En Curso">
            <td class="p-5 text-base text-white font-medium"><span class="hover:underline">Proyecto E</span></td>
            <td class="p-5 text-base">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary">En Curso</span>
            </td>
            <td class="p-5 text-base text-white">
              <div class="flex items-center gap-3">
                <div class="w-32 bg-primary/20 rounded-full h-2.5">
                  <div class="bg-primary h-2.5 rounded-full" style="width: 85%"></div>
                </div>
                <span>85%</span>
              </div>
            </td>
            <td class="p-5 text-base text-white">2024-12-05</td>
            <td class="p-5 text-base text-white">2025-05-05</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</div>
<!-- MODAL DEL REPORTE DE PROYECTO -->
<div id="projectReportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="display:none;">
  <div class="bg-[#182234] border border-primary/20 rounded-xl p-8 max-w-2xl w-full relative">
    <button id="closeReport" type="button" class="absolute top-4 right-4 text-gray-400 hover:text-primary">
      <span class="material-symbols-outlined text-2xl">close</span>
    </button>
    <h2 class="text-3xl font-bold text-white mb-2">Detalles del Proyecto para Reporte</h2>
    <p class="text-white/60 mb-8" id="reportProjectDesc">Información consolidada para la generación del reporte.</p>
    <form class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Nombre del Proyecto</label>
        <input id="reportProjectName" type="text" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Contratista</label>
        <input id="reportProjectContractor" type="text" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"/>
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Materiales Utilizados</label>
        <textarea id="reportProjectMaterials" rows="2" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"></textarea>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Tiempo Estimado (Semanas)</label>
          <input id="reportProjectEstimatedTime" type="number" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"/>
        </div>
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Costos de Materiales ($)</label>
          <input id="reportProjectMaterialCosts" type="number" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"/>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-white/80 mb-2">Costos de Mano de Obra ($)</label>
          <input id="reportProjectLaborCosts" type="number" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white"/>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-white/80 mb-2">Estado Actual del Proyecto</label>
        <select id="reportProjectStatus" class="w-full bg-gray-800 border-gray-600 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-white">
          <option>En Curso</option>
          <option>Completado</option>
          <option>En Espera</option>
          <option>Cancelado</option>
        </select>
      </div>
      <div class="pt-6 flex gap-3">
        <button id="btnPdfReport" type="button" class="w-full bg-primary text-white font-bold py-3 px-4 rounded-lg hover:bg-primary/90 transition flex items-center justify-center gap-2">
          <span class="material-symbols-outlined">picture_as_pdf</span> Generar PDF
        </button>
        <button id="btnExcelReport" type="button" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2">
          <span class="material-symbols-outlined">table</span> Generar Excel
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.19.2/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Chart.js
  const ctx = document.getElementById('projectsChart');
  if (ctx) {
    const isDarkMode = document.documentElement.classList.contains('dark');
    const gridColor = isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
    const textColor = isDarkMode ? 'rgba(255,255,255,0.8)' : 'rgba(0,0,0,0.8)';
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Proyecto A', 'Proyecto B', 'Proyecto C', 'Proyecto D', 'Proyecto E'],
        datasets: [{
          label: 'Progreso del Proyecto (%)',
          data: [60, 100, 30, 0, 85],
          backgroundColor: [
            'rgba(17, 115, 212, 0.6)',
            'rgba(34, 197, 94, 0.6)',
            'rgba(17, 115, 212, 0.6)',
            'rgba(234, 179, 8, 0.6)',
            'rgba(17, 115, 212, 0.6)'
          ],
          borderColor: [
            'rgba(17, 115, 212, 1)',
            'rgba(34, 197, 94, 1)',
            'rgba(17, 115, 212, 1)',
            'rgba(234, 179, 8, 1)',
            'rgba(17, 115, 212, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: gridColor },
            ticks: { color: textColor }
          },
          x: {
            grid: { display: false },
            ticks: { color: textColor }
          }
        },
        plugins: {
          legend: { labels: { color: textColor } }
        }
      }
    });
  }

  // Modal JS puro
  document.querySelectorAll('#projectsTable tbody tr').forEach(function(row){
    row.addEventListener('click', function(){
      document.getElementById('projectReportModal').style.display = "flex";
      document.getElementById('reportProjectName').value = row.dataset.name || '';
      document.getElementById('reportProjectContractor').value = row.dataset.contractor || '';
      document.getElementById('reportProjectMaterials').value = row.dataset.materials || '';
      document.getElementById('reportProjectEstimatedTime').value = row.dataset.estimated_time || '';
      document.getElementById('reportProjectMaterialCosts').value = row.dataset.material_costs || '';
      document.getElementById('reportProjectLaborCosts').value = row.dataset.labor_costs || '';
      document.getElementById('reportProjectStatus').value = row.dataset.status || '';
      document.getElementById('reportProjectDesc').textContent =
        "Información consolidada del " + (row.dataset.name || 'proyecto') + " para la generación del reporte.";
    });
  });
  document.getElementById('closeReport').addEventListener('click', function(){
    document.getElementById('projectReportModal').style.display = "none";
  });
  document.getElementById('projectReportModal').addEventListener('click', function(e){
    if(e.target === this){ this.style.display = "none"; }
  });

  // PDF export
  document.getElementById('btnPdfReport').addEventListener('click', function(){
    const nombre = document.getElementById('reportProjectName').value;
    const contratista = document.getElementById('reportProjectContractor').value;
    const materiales = document.getElementById('reportProjectMaterials').value;
    const tiempo = document.getElementById('reportProjectEstimatedTime').value;
    const costoMat = document.getElementById('reportProjectMaterialCosts').value;
    const costoObra = document.getElementById('reportProjectLaborCosts').value;
    const estado = document.getElementById('reportProjectStatus').value;

    const doc = new window.jspdf.jsPDF();
    doc.setFont('Inter', 'normal');
    doc.setFontSize(18);
    doc.text("Reporte de Proyecto", 20, 20);
    doc.setFontSize(12);
    doc.text([
      `Nombre: ${nombre}`,
      `Contratista: ${contratista}`,
      `Materiales: ${materiales}`,
      `Tiempo estimado: ${tiempo} semanas`,
      `Costo de materiales: $${costoMat}`,
      `Costo de mano de obra: $${costoObra}`,
      `Estado actual: ${estado}`
    ], 20, 40);
    doc.save(`Reporte_${nombre.replace(/ /g,'_')}.pdf`);
  });

  // Excel export
  document.getElementById('btnExcelReport').addEventListener('click', function(){
    const nombre = document.getElementById('reportProjectName').value;
    const contratista = document.getElementById('reportProjectContractor').value;
    const materiales = document.getElementById('reportProjectMaterials').value;
    const tiempo = document.getElementById('reportProjectEstimatedTime').value;
    const costoMat = document.getElementById('reportProjectMaterialCosts').value;
    const costoObra = document.getElementById('reportProjectLaborCosts').value;
    const estado = document.getElementById('reportProjectStatus').value;

    const wb = XLSX.utils.book_new();
    const ws_data = [
      ["Campo", "Valor"],
      ["Nombre", nombre],
      ["Contratista", contratista],
      ["Materiales", materiales],
      ["Tiempo estimado (semanas)", tiempo],
      ["Costo de materiales ($)", costoMat],
      ["Costo de mano de obra ($)", costoObra],
      ["Estado actual", estado]
    ];
    const ws = XLSX.utils.aoa_to_sheet(ws_data);
    XLSX.utils.book_append_sheet(wb, ws, "Reporte");
    XLSX.writeFile(wb, `Reporte_${nombre.replace(/ /g,'_')}.xlsx`);
  });
});
</script>
@endsection