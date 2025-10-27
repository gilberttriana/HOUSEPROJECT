@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
  <!-- Top bar -->
    <div class="flex items-center justify-between mb-8">
    <h1 class="text-4xl font-bold text-[#F8F4EA]">Dashboard Administrador</h1>
    <div class="flex items-center gap-4">
      <div class="text-right mr-4">
        <div class="text-sm text-[#F0EAD6]">{{ auth()->user()->nombre ?? 'Administrador' }}</div>
        <div class="text-xs text-[#DAD2BC]">{{ ucfirst(auth()->user()->rol ?? 'administrador') }}</div>
      </div>
      <div class="w-12 h-12 rounded-full bg-cover bg-center" style="background-image: url('{{ auth()->user()->foto ?? 'https://i.pravatar.cc/100' }}')"></div>
    </div>
  </div>

  <!-- Resumen Ejecutivo -->
  
  @php
    $totalProyectos = isset($proyectos) ? $proyectos->count() : \App\Models\Proyecto::count();
    $activo = 0; $cancelado = 0; $espera = 0;
    if (isset($proyectos) && $proyectos instanceof \Illuminate\Support\Collection){
      foreach($proyectos as $pp){
        $s = strtolower(trim($pp->estado ?? ''));
        if ($s === ''){ // treat empty as waiting
          $espera++;
        } elseif (strpos($s,'activo') !== false){
          $activo++;
        } elseif (strpos($s,'cancel') !== false){
          $cancelado++;
        } elseif (strpos($s,'esper') !== false || strpos($s,'pend') !== false){
          $espera++;
        }
      }
    } else {
      // fallback to DB counts if collection not available
      $activo = \App\Models\Proyecto::whereRaw("lower(estado) like ?", ['%activo%'])->count();
      $cancelado = \App\Models\Proyecto::whereRaw("lower(estado) like ?", ['%cancel%'])->count();
      $espera = \App\Models\Proyecto::whereRaw("lower(estado) like ?", ['%esper%'])->count();
    }
  @endphp

  <!-- Contadores horizontales simplificados -->
  <div class="flex flex-col lg:flex-row gap-4 mb-6">
    <div class="flex-1 bg-card-blue border border-accent-gold/6 rounded-xl p-6 text-[#F8F4EA] text-center">
      <h3 class="text-lg font-medium">Activos</h3>
      <p class="text-4xl font-bold mt-2">{{ $activo }}</p>
    </div>
    <div class="flex-1 bg-card-blue border border-accent-gold/6 rounded-xl p-6 text-[#F8F4EA] text-center">
      <h3 class="text-lg font-medium">Cancelados</h3>
      <p class="text-4xl font-bold mt-2">{{ $cancelado }}</p>
    </div>
    <div class="flex-1 bg-card-blue border border-accent-gold/6 rounded-xl p-6 text-[#F8F4EA] text-center">
      <h3 class="text-lg font-medium">En espera</h3>
      <p class="text-4xl font-bold mt-2">{{ $espera }}</p>
    </div>
  </div>

  <!-- Inventario de Materiales -->
  <section class="mt-8">
    <h2 class="text-3xl font-bold text-[#F8F4EA] mb-6">Inventario de Materiales</h2>
    <div class="overflow-x-auto bg-card-blue border border-accent-gold/6 rounded-xl p-6">
      <table class="w-full text-left" id="materialsTable">
        <thead class="bg-accent-gold/6">
          <tr>
            <th class="p-5 text-base font-semibold text-white">Material</th>
            <th class="p-5 text-base font-semibold text-white">Descripción</th>
            <th class="p-5 text-base font-semibold text-white">Cantidad</th>
            <th class="p-5 text-base font-semibold text-white">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-accent-gold/10">
          @php
            $sample = [
              ['nombre'=>'Cemento','descripcion'=>'Saco 50kg','cantidad'=>120,'precio'=>'50.00'],
              ['nombre'=>'Acero','descripcion'=>'Varilla 12mm','cantidad'=>0,'precio'=>'150.00'],
              ['nombre'=>'Madera','descripcion'=>'Tablón 2x4','cantidad'=>45,'precio'=>'25.00'],
            ];
            $list = isset($materiales) ? $materiales : collect($sample);
          @endphp
          @foreach($list as $mat)
            @php
              $nombre = data_get($mat, 'nombre', data_get($mat, 'NOMBRE', '—'));
              $qty = intval(data_get($mat, 'cantidad') ?? data_get($mat, 'stock') ?? data_get($mat, 'CANTIDAD') ?? 0);
                $estRaw = data_get($mat, 'estado') ?? data_get($mat, 'ESTADO') ?? 'pendiente';
                $estadoInfo = \App\Helpers\EstadoHelper::normalize($estRaw);
                $label = $estadoInfo['label'];
                $statusClass = $estadoInfo['class'];
              $desc = data_get($mat, 'descripcion') ?? data_get($mat, 'DESCRIPCION') ?? '';
              $fecha = data_get($mat, 'updated_at') ? (
                
                (is_string(data_get($mat,'updated_at')) ? 
                  
                  
                  
                  data_get($mat,'updated_at') : data_get($mat,'updated_at')->format('Y-m-d'))
              ) : '';
            @endphp
            <tr class="material-row cursor-pointer hover:bg-primary/10" data-id="{{ data_get($mat,'id_material') ?? data_get($mat,'id') ?? '' }}" data-nombre="{{ e($nombre) }}" data-descripcion="{{ e($desc) }}" data-cantidad="{{ $qty }}" data-estado="{{ e($estRaw) }}" data-fecha="{{ $fecha }}">
              <td class="p-5 text-base text-white font-medium">{{ $nombre }}</td>
              <td class="p-5 text-base text-white/80">{{ $desc }}</td>
              <td class="p-5 text-base text-white">{{ $qty }}</td>
              <td class="p-5 text-base">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">{{ $label }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>
  <!-- Modal de exportar materiales eliminado: solo se mostrará la tabla de materiales -->
  <!-- Tabla de proyectos -->
  <section>
    <div class="flex items-center justify-between">
      <h2 class="text-3xl font-bold text-[#F8F4EA] mb-6">Proyectos</h2>
    </div>
    <div class="overflow-x-auto bg-card-blue border border-accent-gold/6 rounded-xl">
      <table class="w-full text-left" id="projectsTable">
        <thead class="bg-accent-gold/6">
          <tr>
            <th class="p-5 text-base font-semibold text-white">Nombre del Proyecto</th>
            <th class="p-5 text-base font-semibold text-white">Estado</th>
            <th class="p-5 text-base font-semibold text-white">Progreso</th>
            <th class="p-5 text-base font-semibold text-white">Fecha de Inicio</th>
            <th class="p-5 text-base font-semibold text-white">Fecha de Finalización</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-accent-gold/10">
          @if(isset($proyectos) && $proyectos->count())
        @foreach($proyectos as $p)
          @php
          $name = $p->nombre ?? $p->nombre_proyecto ?? 'Proyecto';
          $estado = $p->estado ?? $p->status ?? 'En Curso';
          $inicio = $p->fecha_inicio ?? $p->fecha_inicio_estimado ?? ($p->created_at ?? '');
          $fin = $p->fecha_fin ?? $p->fecha_finalizacion ?? '';
        // Preferir el valor persistido en DB (`progreso`) para que la UI muestre la verdad de la BD.
        if (isset($p->progreso) && is_numeric($p->progreso)){
          $progreso = intval(max(0, min(100, $p->progreso)));
        } elseif (isset($p->progress) && is_numeric($p->progress)){
          $progreso = intval(max(0, min(100, $p->progress)));
        } elseif (isset($p->computed_progress) && is_numeric($p->computed_progress)){
          $progreso = intval(max(0, min(100, $p->computed_progress)));
        } else {
          // Calcular desde fechas como fallback
          $progreso = null;
          if (false) {
          }
          else {
            try{
              $now = \Carbon\Carbon::now();
              $start = null; $end = null;
              if (!empty($p->fecha_inicio) || !empty($p->fecha_inicio_estimado)){
                $start = \Carbon\Carbon::parse($p->fecha_inicio ?? $p->fecha_inicio_estimado);
              } elseif (!empty($p->created_at)){
                $start = \Carbon\Carbon::parse($p->created_at);
              }
              if (!empty($p->fecha_fin) || !empty($p->fecha_finalizacion)){
                $end = \Carbon\Carbon::parse($p->fecha_fin ?? $p->fecha_finalizacion);
              }
              if ($start && $end){
                if ($end->lessThanOrEqualTo($start)){
                  $progreso = $now->greaterThanOrEqualTo($end) ? 100 : 0;
                } else {
                  $total = max(1, $end->diffInDays($start));
                  $elapsed = max(0, min($total, $now->diffInDays($start)));
                  $progreso = intval(round(($elapsed / $total) * 100));
                }
              } elseif ($start && !$end){
                $progreso = 0;
              } elseif ($end && !$start){
                $progreso = $now->greaterThanOrEqualTo($end) ? 100 : 0;
              } else {
                $progreso = 0;
              }
            } catch (\Throwable $e) { $progreso = 0; }
          }
          $progreso = intval(max(0, min(100, $progreso)));
        }
          $materialsListString = '';
          if(isset($p->materiales) && $p->materiales->count()){
            $materialsListString = $p->materiales->map(function($m){
              $qty = intval($m->pivot->cantidad ?? 0);
              return ($m->nombre ?? ($m->NOMBRE ?? '')) . ($qty ? ' x'.$qty : '');
            })->implode(', ');
          }
          @endphp
          <tr class="cursor-pointer hover:bg-primary/10"
            data-name="{{ e($name) }}"
            data-contractor="{{ e($p->contratista ?? $p->contratista_nombre ?? '') }}"
            data-materials="{{ e($materialsListString) }}"
            data-estimated_time="{{ e($p->duracion_estimada ?? '') }}"
            data-material_costs="{{ e($p->costo_materiales ?? '') }}"
            data-labor_costs="{{ e($p->costo_mano_obra ?? '') }}"
            data-status="{{ e($estado) }}"
            data-computed-progress="{{ intval($p->computed_progress ?? $progreso ?? 0) }}">
                <td class="p-5 text-base text-white font-medium"><span class="hover:underline">{{ $name }}</span></td>
                @php
                  $estadoInfo = \App\Helpers\EstadoHelper::normalize($estado);
                  $estadoLabel = $estadoInfo['label'];
                  $estadoClass = $estadoInfo['class'];
                @endphp
                <td class="p-5 text-base">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $estadoClass }}">{{ $estadoLabel }}</span>
                </td>
                <td class="p-5 text-base text-white">
                  <div class="flex items-center gap-3">
                    <div class="w-32 bg-primary/20 rounded-full h-2.5">
                      <div class="bg-primary h-2.5 rounded-full" style="width: {{ intval($progreso) }}%"></div>
                    </div>
                    <span>{{ intval($progreso) }}%</span>
                  </div>
                </td>
                <td class="p-5 text-base text-white">{{ $inicio }}</td>
                <td class="p-5 text-base text-white">{{ $fin }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td class="p-5 text-base text-white" colspan="5">No hay proyectos registrados.</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </section>
  @include('dashboard.partials.project_modal', ['materialsList' => $materialsList ?? collect()])
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.19.2/xlsx.full.min.js"></script>
<script>
  // Expose initialization functions so they can be called after AJAX page loads
  function initAdminUI(){
    // Modal JS puro: attach listeners to rows and buttons
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
    const closeBtn = document.getElementById('closeReport');
    if(closeBtn){ closeBtn.addEventListener('click', function(){ document.getElementById('projectReportModal').style.display = "none"; }); }
    const modal = document.getElementById('projectReportModal');
    if(modal){ modal.addEventListener('click', function(e){ if(e.target === this){ this.style.display = "none"; } }); }

    // PDF export
    const btnPdf = document.getElementById('btnPdfReport');
    if(btnPdf){ btnPdf.addEventListener('click', function(){
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
  }

  // expose functions globally so layout's AJAX loader can call them
  window.initAdminUI = initAdminUI;

  document.addEventListener('DOMContentLoaded', function(){
    initAdminUI();
  });
</script>
<!-- scripts for materials export removed -->
@include('dashboard.partials.project_js')
@endsection