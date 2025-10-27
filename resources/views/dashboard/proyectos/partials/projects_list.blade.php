<div class="overflow-x-auto bg-card-blue border border-accent-gold/6 rounded-xl p-4 mb-6">
  <table class="w-full text-left">
    <thead class="bg-accent-gold/6">
      <tr>
        <th class="p-3 text-sm font-semibold text-white">Nombre</th>
        <th class="p-3 text-sm font-semibold text-white">Estado</th>
        <th class="p-3 text-sm font-semibold text-white">Progreso</th>
        <th class="p-3 text-sm font-semibold text-white">Inicio</th>
        <th class="p-3 text-sm font-semibold text-white">Fin</th>
        <th class="p-3 text-sm font-semibold text-white">Total Materiales</th>
        <th class="p-3 text-sm font-semibold text-white">Costo Materiales</th>
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
    // Preferir el valor persistido en DB (`progreso`) para que la UI refleje la verdad de la BD.
    if (isset($p->progreso) && is_numeric($p->progreso)){
      $progreso = intval(max(0, min(100, $p->progreso)));
    } elseif (isset($p->progress) && is_numeric($p->progress)){
      $progreso = intval(max(0, min(100, $p->progress)));
    } elseif (isset($p->computed_progress) && is_numeric($p->computed_progress)){
      $progreso = intval(max(0, min(100, $p->computed_progress)));
    } else {
      // Calcular a partir de fechas como fallback
      $progreso = null;
      if (false) {
      }
      else {
        try {
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
        } catch (\Throwable $e){ $progreso = 0; }
      }
      $progreso = intval(max(0, min(100, $progreso)));
    }
              // materiales con cantidades: nombre x cantidad
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
              <td class="p-5 text-base text-white font-medium">{{ $name }}</td>
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
              @php
                $totalQty = 0;
                $totalCost = 0;
                if(isset($p->materiales) && $p->materiales->count()){
                    foreach($p->materiales as $m){
                        $qty = intval($m->pivot->cantidad ?? 0);
                        $price = floatval($m->precio ?? 0);
                        $totalQty += $qty;
                        $totalCost += $qty * $price;
                    }
                }
              @endphp
              <td class="p-5 text-base text-white">{{ $totalQty }}</td>
              <td class="p-5 text-base text-white">${{ number_format($totalCost,2) }}</td>
            </tr>
          @endforeach
      @else
        <tr>
          <td class="p-5 text-base text-white" colspan="7">No hay proyectos registrados.</td>
        </tr>
      @endif
    </tbody>
  </table>
</div>
