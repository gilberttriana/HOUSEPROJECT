@extends('layouts.app')

@section('content')
<div class="with-sidebar">
  <div class="max-w-5xl mx-auto px-4 md:px-6 lg:px-8">
  <div class="flex items-center justify-between mb-4">
  <h1 class="text-xl md:text-2xl font-bold text-[#F8F4EA] leading-tight">Dashboard Administrador</h1>
    <div class="flex items-center gap-4">
      <div class="text-right mr-4">
        <div class="text-sm text-[#F0EAD6]">{{ auth()->user()->nombre ?? 'Administrador' }}</div>
        <div class="text-xs text-[#DAD2BC]">{{ ucfirst(auth()->user()->rol ?? 'administrador') }}</div>
      </div>
  <div class="w-10 h-10 rounded-full bg-cover bg-center" style="background-image: url('{{ auth()->user()->foto ?? 'https://i.pravatar.cc/100' }}')"></div>
    </div>
  </div>

  
  @php
    $totalProyectos = isset($proyectos) ? $proyectos->count() : \App\Models\Proyecto::count();
    $activo = 0; $cancelado = 0; $espera = 0;
    if (isset($proyectos) && $proyectos instanceof \Illuminate\Support\Collection){
      foreach($proyectos as $pp){
        $s = strtolower(trim($pp->estado ?? ''));
        if ($s === ''){ 
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
    
      $activo = \App\Models\Proyecto::whereRaw("lower(estado) like ?", ['%activo%'])->count();
      $cancelado = \App\Models\Proyecto::whereRaw("lower(estado) like ?", ['%cancel%'])->count();
      $espera = \App\Models\Proyecto::whereRaw("lower(estado) like ?", ['%esper%'])->count();
    }
  @endphp
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-4">
    <div class="flex items-center justify-between bg-card-blue border border-accent-gold/6 rounded-lg p-2 text-[#F8F4EA]">
      <div>
        <h3 class="text-xs font-medium">Activos</h3>
        <p class="text-lg font-bold mt-1">{{ $activo }}</p>
      </div>
      <div class="hidden md:block opacity-60"></div>
    </div>
    <div class="flex items-center justify-between bg-card-blue border border-accent-gold/6 rounded-lg p-2 text-[#F8F4EA]">
      <div>
        <h3 class="text-xs font-medium">Cancelados</h3>
        <p class="text-lg font-bold mt-1">{{ $cancelado }}</p>
      </div>
      <div class="hidden md:block opacity-60"></div>
    </div>
    <div class="flex items-center justify-between bg-card-blue border border-accent-gold/6 rounded-lg p-2 text-[#F8F4EA]">
      <div>
        <h3 class="text-xs font-medium">En espera</h3>
        <p class="text-lg font-bold mt-1">{{ $espera }}</p>
      </div>
      <div class="hidden md:block opacity-60"></div>
    </div>
  </div>

  <!-- Inventario de Materiales -->
  <section class="mt-8">
    <h2 class="text-xl font-bold text-[#F8F4EA] mb-4">Inventario de Materiales</h2>
    <div class="overflow-x-auto bg-card-blue border border-accent-gold/6 rounded-lg p-2">
      <table class="w-full text-left" id="materialsTable">
        <thead class="bg-accent-gold/6">
          <tr>
            <th class="px-2 py-1 text-xs font-semibold text-white">Material</th>
            <th class="px-2 py-1 text-xs font-semibold text-white">Proveedor</th>
            <th class="px-2 py-1 text-xs font-semibold text-white">Descripción</th>
            <th class="px-2 py-1 text-xs font-semibold text-white">Cantidad</th>
            <th class="px-2 py-1 text-xs font-semibold text-white">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-accent-gold/10 text-xs">
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
            @php
              $provLabel = data_get($mat, 'proveedor_label') ?? data_get($mat, 'proveedor') ?? data_get($mat, 'PROVEEDOR') ?? null;
            @endphp
            <tr class="material-row cursor-pointer hover:bg-primary/10 align-middle" data-id="{{ data_get($mat,'id_material') ?? data_get($mat,'id') ?? '' }}" data-nombre="{{ e($nombre) }}" data-descripcion="{{ e($desc) }}" data-cantidad="{{ $qty }}" data-estado="{{ e($estRaw) }}" data-fecha="{{ $fecha }}" data-proveedor="{{ e($provLabel) }}">
              @php
                // resolver imagen primaria (soporta arrays/JSON o ruta simple)
                $imgUrl = null;
                $rawImg = data_get($mat, 'imagen') ?? data_get($mat, 'IMAGEN') ?? null;
                if (empty($rawImg) && data_get($mat,'primary_image_url')) { $imgUrl = data_get($mat,'primary_image_url'); }
                else if (!empty($rawImg)) {
                  try{
                    $decoded = json_decode($rawImg, true);
                    if (is_array($decoded) && count($decoded)) { $imgUrl = \Illuminate\Support\Facades\Storage::url($decoded[0]); }
                    elseif (is_string($rawImg)) { if (preg_match('#^https?://#i', $rawImg)) $imgUrl = $rawImg; else $imgUrl = \Illuminate\Support\Facades\Storage::url($rawImg); }
                  }catch(\Throwable $e){ $imgUrl = null; }
                }
              @endphp
              <td class="px-2 py-1 text-white font-medium flex items-center gap-3">
                @if($imgUrl)
                  <img src="{{ $imgUrl }}" alt="{{ e($nombre) }}" class="w-8 h-8 rounded object-cover" />
                @else
                  <div class="w-8 h-8 rounded bg-gray-700 flex items-center justify-center text-2xs text-white">No</div>
                @endif
                <div class="truncate max-w-xs" title="{{ $nombre }}">{{ $nombre }}</div>
              </td>
              <td class="px-2 py-1 text-white/90 truncate max-w-[10rem]" title="{{ $provLabel ?? '—' }}">{{ $provLabel ?? '—' }}</td>
              <td class="px-2 py-1 text-white/80 truncate max-w-sm" title="{{ $desc }}">{{ $desc }}</td>
              <td class="px-2 py-1 text-white">{{ $qty }}</td>
              <td class="px-3 py-2">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">{{ $label }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>

  <section>
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-bold text-[#F8F4EA] mb-4">Proyectos</h2>
    </div>
    <div class="overflow-x-auto bg-card-blue border border-accent-gold/6 rounded-lg p-2">
      <table class="w-full text-left" id="projectsTable">
        <thead class="bg-accent-gold/6">
          <tr>
            <th class="p-2 text-sm font-semibold text-white">Nombre del Proyecto</th>
            <th class="p-2 text-sm font-semibold text-white">Estado</th>
            <th class="p-2 text-sm font-semibold text-white">Progreso</th>
            <th class="p-2 text-sm font-semibold text-white">Fecha de Inicio</th>
            <th class="p-2 text-sm font-semibold text-white">Fecha de Finalización</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-accent-gold/10 text-sm">
          @if(isset($proyectos) && $proyectos->count())
            @foreach($proyectos as $p)
              @php
                $name = $p->nombre ?? $p->nombre_proyecto ?? 'Proyecto';
                $estado = $p->estado ?? $p->status ?? 'En Curso';
                $inicio = $p->fecha_inicio ?? $p->fecha_inicio_estimado ?? ($p->created_at ?? '');
                $fin = $p->fecha_fin ?? $p->fecha_finalizacion ?? '';

                if (isset($p->progreso) && is_numeric($p->progreso)){
                  $progreso = intval(max(0, min(100, $p->progreso)));
                } elseif (isset($p->progress) && is_numeric($p->progress)){
                  $progreso = intval(max(0, min(100, $p->progress)));
                } elseif (isset($p->computed_progress) && is_numeric($p->computed_progress)){
                  $progreso = intval(max(0, min(100, $p->computed_progress)));
                } else {
                  $progreso = null;
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
                  <td class="p-2 text-sm text-white font-medium"><span class="hover:underline truncate max-w-xs" title="{{ $name }}">{{ $name }}</span></td>
                  @php
                    $estadoInfo = \App\Helpers\EstadoHelper::normalize($estado);
                    $estadoLabel = $estadoInfo['label'];
                    $estadoClass = $estadoInfo['class'];
                  @endphp
                  <td class="p-2 text-sm">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $estadoClass }}">{{ $estadoLabel }}</span>
                  </td>
                  <td class="p-2 text-sm text-white">
                    <div class="flex items-center gap-2">
                      <div class="w-20 bg-primary/20 rounded-full h-2.5">
                        <div class="bg-primary h-2.5 rounded-full" style="width: {{ intval($progreso) }}%"></div>
                      </div>
                      <span class="text-xs">{{ intval($progreso) }}%</span>
                    </div>
                  </td>
                  <td class="p-2 text-sm text-white truncate max-w-[8rem]" title="{{ $inicio }}">{{ $inicio }}</td>
                  <td class="p-2 text-sm text-white truncate max-w-[8rem]" title="{{ $fin }}">{{ $fin }}</td>
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
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.19.2/xlsx.full.min.js"></script>
<script>
  function initAdminUI(){
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

  window.initAdminUI = initAdminUI;

  document.addEventListener('DOMContentLoaded', function(){
    initAdminUI();
  });
</script>
@include('dashboard.partials.project_js')
@endsection