@extends('layouts.app-proveedor')

@section('content')
<div x-data="modalReporte" class="container mx-auto flex-grow p-4 sm:p-6 lg:p-8">
    <h1 class="text-4xl font-black text-white mb-8">Panel de Proveedor</h1>

    @php
        $user = Auth::user();
        $proveedor = $user ? $user->proveedor : null;
        if($proveedor){
                // Buscar materiales asociados al proveedor. A veces el id_proveedor fue guardado como el id de usuario,
                // por compatibilidad incluimos ambos valores (id_proveedor real o id_usuario del proveedor) para evitar
                // que materiales válidos desaparezcan de la vista.
                $materials = \App\Models\Material::where(function($q) use ($proveedor, $user){
                    $q->where('id_proveedor', $proveedor->id_proveedor);
                    // fallback: en algunos flujos previos se guardó el id_usuario en id_proveedor
                    $q->orWhere('id_proveedor', $user->id_usuario);
                })->orderByDesc(
                    (\Illuminate\Support\Facades\Schema::hasColumn('materiales','fecha_actualizacion') ? 'fecha_actualizacion' : (
                        \Illuminate\Support\Facades\Schema::hasColumn('materiales','updated_at') ? 'updated_at' : 'id_material'
                    ))
                )->get();

                // Proyectos que usan alguno de los materiales de este proveedor (mismo criterio de id_proveedor)
                $projects = \App\Models\Proyecto::whereHas('materiales', function($q) use ($proveedor, $user){
                    $q->where(function($qq) use ($proveedor, $user){
                        $qq->where('id_proveedor', $proveedor->id_proveedor)->orWhere('id_proveedor', $user->id_usuario);
                    });
                })->get();
        } else {
            $materials = collect();
            $projects = collect();
        }

        $countMaterials = $materials->count();
        $countProjectsActive = $projects->where('estado', 'activo')->count() ?: $projects->where('estado', 'Activo')->count();
        $countMaterialsInUse = $materials->filter(function($m){ return $m->proyectos()->count() > 0; })->count();
    @endphp

    <!-- Bloques resumen en fila y más pequeños -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-4">
        <div class="rounded-xl bg-card-blue p-4 shadow flex flex-col items-center justify-center text-white">
            <h2 class="text-base font-semibold mb-2">Materiales Publicados</h2>
            <span id="cntMaterials" class="text-2xl font-bold">{{ $countMaterials }}</span>
        </div>
        <div class="rounded-xl bg-card-blue p-4 shadow flex flex-col items-center justify-center text-white">
            <h2 class="text-base font-semibold mb-2">Proyectos con tus materiales</h2>
            <span id="cntProjects" class="text-2xl font-bold">{{ $projects->count() }}</span>
        </div>
        <div class="rounded-xl bg-card-blue p-4 shadow flex flex-col items-center justify-center text-white">
            <h2 class="text-base font-semibold mb-2">Materiales en Uso</h2>
            <span id="cntMaterialsInUse" class="text-2xl font-bold">{{ $countMaterialsInUse }}</span>
        </div>
    </div>

    <!-- Botón de Agregar Material -->
    <div class="mb-10 flex justify-center">
        <button id="btnNuevoMaterial" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold shadow hover:bg-primary/90" type="button">
            + Agregar Material
        </button>
    </div>

    <!-- Tabla Materiales -->
    <h3 class="text-2xl font-bold tracking-tight mb-4 text-white">Estado de Materiales</h3>
    <div class="overflow-x-auto rounded-xl bg-card-blue shadow mb-12">
        <table class="min-w-full divide-y divide-[#20304A] text-white">
            <thead class="bg-[#20304A] text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold">Material</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Estado</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Stock</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Última Actualización</th>
                </tr>
            </thead>
            <tbody id="materialsTbody" class="divide-y divide-[#20304A]">
                @forelse($materials as $mat)
                    @php
                        $estadoRaw = strtolower(trim((string)($mat->estado ?? '')));
                        // Normalizar y detectar estados comunes: agotado, desaprobado, aprobado, disponible, pendiente
                        if(strpos($estadoRaw,'agot') !== false) {
                            $badgeClass = 'bg-red-700/30 text-red-400'; $label = 'Agotado';
                        } elseif(strpos($estadoRaw,'desap') !== false || strpos($estadoRaw,'rechaz') !== false) {
                            $badgeClass = 'bg-red-700/30 text-red-400'; $label = 'Desaprobado';
                        } elseif(strpos($estadoRaw,'apro') !== false) {
                            $badgeClass = 'bg-green-700/30 text-green-400'; $label = 'Aprobado';
                        } elseif(strpos($estadoRaw,'disp') !== false || strpos($estadoRaw,'available') !== false) {
                            $badgeClass = 'bg-green-700/30 text-green-400'; $label = 'Disponible';
                        } elseif($estadoRaw === '' || strpos($estadoRaw,'pend') !== false || strpos($estadoRaw,'esper') !== false) {
                            $badgeClass = 'bg-yellow-700/30 text-yellow-400'; $label = 'Pendiente';
                        } else {
                            // Fallback: mostrar el texto tal cual capitalizado
                            $badgeClass = 'bg-yellow-700/30 text-yellow-400'; $label = ucfirst($mat->estado ?? 'Pendiente');
                        }
                        $stock = $mat->stock ?? ($mat->cantidad ?? '-');
                        $fecha = data_get($mat, 'updated_at') ?: data_get($mat, 'fecha_actualizacion') ?: data_get($mat, 'created_at') ?: '';
                    @endphp
                    <tr @click="abrir('material', {material: '{{ addslashes($mat->nombre) }}', estado: '{{ addslashes($label) }}', stock: '{{ addslashes($stock) }}', fecha: '{{ $fecha }}'})" class="cursor-pointer hover:bg-[#22304a]">
                        <td class="px-6 py-4">{{ $mat->nombre }}</td>
                        <td class="px-6 py-4"><span class="{{ $badgeClass }} px-3 py-1 rounded-full text-xs font-semibold">{{ $label }}</span></td>
                        <td class="px-6 py-4">{{ $stock }}</td>
                        <td class="px-6 py-4">{{ $fecha }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-white/80">No hay materiales publicados aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Tabla Proyectos -->
    <h3 class="text-2xl font-bold tracking-tight mb-4 text-white">Proyectos con tus Materiales</h3>
    <div class="overflow-x-auto rounded-xl bg-card-blue shadow">
        <table class="min-w-full divide-y divide-[#20304A] text-white">
            <thead class="bg-[#20304A] text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold">Proyecto</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Ubicación</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Materiales</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Estado</th>
                </tr>
            </thead>
            <tbody id="projectsTbody" class="divide-y divide-[#20304A]">
                @forelse($projects as $p)
                    @php
                        $projEstado = $p->estado ?? 'En Curso';
                        $projMaterials = $p->materiales->map(function($m){ return $m->nombre ?? $m->NOMBRE ?? ''; })->implode(', ');
                        $ubicacion = $p->ubicacion ?? $p->direccion ?? '-';
                        // badge color
                        $pe = strtolower($projEstado);
                        if(strpos($pe,'complet') !== false) { $pClass = 'bg-green-700/30 text-green-400'; }
                        elseif(strpos($pe,'plan') !== false) { $pClass = 'bg-blue-700/30 text-blue-400'; }
                        else { $pClass = 'bg-yellow-700/30 text-yellow-400'; }
                    @endphp
                    <tr @click="abrir('proyecto', {proyecto: '{{ addslashes($p->nombre) }}', ubicacion: '{{ addslashes($ubicacion) }}', materiales: '{{ addslashes($projMaterials) }}', estado: '{{ addslashes($projEstado) }}'})" class="cursor-pointer hover:bg-[#22304a]">
                        <td class="px-6 py-4">{{ $p->nombre }}</td>
                        <td class="px-6 py-4">{{ $ubicacion }}</td>
                        <td class="px-6 py-4">{{ $projMaterials }}</td>
                        <td class="px-6 py-4"><span class="{{ $pClass }} px-3 py-1 rounded-full text-xs font-semibold">{{ $projEstado }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-white/80">No se encontraron proyectos que usen tus materiales.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal para modificar/crear reportes -->
    <div x-show="open" x-transition class="fixed inset-0 z-40 flex items-center justify-center bg-black/40" style="display: none;">
        <div class="bg-[#21293a] dark:bg-[#182534] rounded-xl shadow-xl w-full max-w-lg p-8 relative flex flex-col items-center">
            <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-primary text-2xl">&times;</button>
            <h2 class="text-2xl font-bold text-primary mb-6 text-center" x-text="tipo === 'material' ? 'Reporte de Material' : 'Reporte de Proyecto'"></h2>
            <form class="w-full flex flex-col items-center">
                <template x-if="tipo === 'material'">
                    <div class="w-full flex flex-col items-center">
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Material</label>
                            <input id="inputMaterial" type="text" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.material" placeholder="Nombre del material">
                        </div>
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Estado</label>
                            <input id="inputEstado" type="text" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.estado" placeholder="Estado">
                        </div>
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Stock</label>
                            <input id="inputStock" type="text" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.stock" placeholder="Stock">
                        </div>
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Última Actualización</label>
                            <input id="inputFecha" type="date" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.fecha">
                        </div>
                    </div>
                </template>
                <template x-if="tipo === 'proyecto'">
                    <div class="w-full flex flex-col items-center">
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Proyecto</label>
                            <input id="inputProyecto" type="text" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.proyecto" placeholder="Nombre del proyecto">
                        </div>
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Ubicación</label>
                            <input id="inputUbicacion" type="text" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.ubicacion" placeholder="Ubicación">
                        </div>
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Materiales</label>
                            <input id="inputMateriales" type="text" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.materiales" placeholder="Materiales">
                        </div>
                        <div class="mb-4 w-3/4 text-center">
                            <label class="block font-bold text-primary mb-2">Estado</label>
                            <input id="inputEstadoProyecto" type="text" class="w-full rounded-lg p-2 bg-[#182534] text-white border border-primary text-center" x-model="reporte.estado" placeholder="Estado">
                        </div>
                    </div>
                </template>
                <div class="flex justify-center gap-4 mt-8 w-full">
                    <button type="button" @click="open = false" class="bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-700">Cancelar</button>
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary/80">Guardar</button>
                    <button type="button" id="btnExportarPdf" class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 shadow-lg">Exportar PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Nuevo Material -->
<div id="nuevoMaterialModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div class="bg-[#21293a] rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-white">Agregar Material</h3>
            <button id="closeNuevoMaterial" class="text-white text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('materiales.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            {{-- id_proveedor: usar el id_usuario del usuario autenticado --}}
            <input type="hidden" name="id_proveedor" value="{{ Auth::user()->id_usuario ?? '' }}">

            <div>
                <label class="block text-sm text-white mb-1">Nombre</label>
                <input name="nombre" required type="text" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="Nombre del material">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Precio</label>
                <input name="precio" required type="number" step="0.01" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Stock</label>
                <input name="stock" type="number" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="Cantidad disponible">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Imagen</label>
                <input name="imagen" type="file" accept="image/*" class="w-full text-white">
            </div>

            <div>
                <label class="block text-sm text-white mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full rounded p-2 bg-[#182534] text-white border border-primary" placeholder="Descripción breve"></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="cancelNuevoMaterial" class="bg-gray-600 text-white px-4 py-2 rounded">Cancelar</button>
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // Modal nuevo material show/hide
    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('btnNuevoMaterial');
        const modal = document.getElementById('nuevoMaterialModal');
        const close = document.getElementById('closeNuevoMaterial');
        const cancel = document.getElementById('cancelNuevoMaterial');
        if(btn && modal){
            btn.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); modal.style.display = 'flex'; modal.querySelector('input[name=nombre]')?.focus(); });
        }
        if(close){ close.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); modal.style.display = 'none'; }); }
        if(cancel){ cancel.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); modal.style.display = 'none'; }); }
        if(modal){ modal.addEventListener('click', function(e){ if(e.target === modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); modal.style.display = 'none'; } }); }
    });
</script>
<script>
    // AJAX submit: enviar el formulario por fetch para no redirigir
    document.addEventListener('DOMContentLoaded', function(){
        const form = document.querySelector('#nuevoMaterialModal form');
        if(!form) return;

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const modal = document.getElementById('nuevoMaterialModal');
            const submitBtn = form.querySelector('button[type=submit]');
            if(submitBtn) submitBtn.disabled = true;

            const fd = new FormData(form);
            try{
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if(res.status === 422){
                    const json = await res.json();
                    const first = Object.values(json.errors)[0][0] || 'Error de validación';
                    alert(first);
                    if(submitBtn) submitBtn.disabled = false;
                    return;
                }

                const data = await res.json();
                if(!data || !data.ok){
                    alert(data && data.error ? data.error : 'Error al guardar');
                    if(submitBtn) submitBtn.disabled = false;
                    return;
                }

                // Cerrar modal
                modal.classList.add('hidden'); modal.classList.remove('flex'); modal.style.display = 'none';

                // Actualizar contador y tabla
                const mat = data.material;
                // Si el servidor devolvió contadores actualizados, aplicarlos
                if(data.counters){
                    const c = data.counters;
                    const elMat = document.getElementById('cntMaterials'); if(elMat) elMat.textContent = c.materials;
                    const elProj = document.getElementById('cntProjects'); if(elProj) elProj.textContent = c.projects;
                    const elMatUse = document.getElementById('cntMaterialsInUse'); if(elMatUse) elMatUse.textContent = c.materials_in_use;
                } else {
                    // fallback: incrementar contador de materiales
                    const cntMaterialsEl = document.getElementById('cntMaterials');
                    if(cntMaterialsEl) cntMaterialsEl.textContent = parseInt(cntMaterialsEl.textContent || '0') + 1;
                }

                // Añadir fila al inicio de la tabla de materiales
                const tbody = document.getElementById('materialsTbody');
                if(tbody){
                    const tr = document.createElement('tr');
                    tr.className = 'cursor-pointer hover:bg-[#22304a]';
                    const nombre = document.createElement('td'); nombre.className = 'px-6 py-4'; nombre.textContent = mat.nombre || '';
                    const estadoTd = document.createElement('td'); estadoTd.className = 'px-6 py-4';
                    const badge = document.createElement('span'); badge.className = 'bg-yellow-700/30 text-yellow-400 px-3 py-1 rounded-full text-xs font-semibold'; badge.textContent = mat.estado || 'pendiente';
                    estadoTd.appendChild(badge);
                    const stockTd = document.createElement('td'); stockTd.className = 'px-6 py-4'; stockTd.textContent = mat.stock ?? mat.cantidad ?? '-';
                    const fechaTd = document.createElement('td'); fechaTd.className = 'px-6 py-4'; fechaTd.textContent = mat.updated_at ?? new Date().toISOString().slice(0,10);
                    tr.appendChild(nombre); tr.appendChild(estadoTd); tr.appendChild(stockTd); tr.appendChild(fechaTd);
                    tbody.insertBefore(tr, tbody.firstChild);
                }

                if(submitBtn) submitBtn.disabled = false;

            }catch(err){
                console.error(err);
                alert('Error al subir el material');
                const submitBtn = form.querySelector('button[type=submit]'); if(submitBtn) submitBtn.disabled = false;
            }
        });
    });
</script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('modalReporte', () => ({
            open: false,
            tipo: '',
            reporte: {},
            abrir(tipo, datos) {
                this.tipo = tipo;
                this.reporte = datos;
                this.open = true;
                setTimeout(() => {
                    if (tipo === 'material') {
                        document.getElementById('inputMaterial').value = datos.material;
                        document.getElementById('inputEstado').value = datos.estado;
                        document.getElementById('inputStock').value = datos.stock;
                        document.getElementById('inputFecha').value = datos.fecha;
                    } else {
                        document.getElementById('inputProyecto').value = datos.proyecto;
                        document.getElementById('inputUbicacion').value = datos.ubicacion;
                        document.getElementById('inputMateriales').value = datos.materiales;
                        document.getElementById('inputEstadoProyecto').value = datos.estado;
                    }
                }, 100);
            }
        }))
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Enhance PDF export with a decorated layout
        const currentUserName = "{{ Auth::user()->nombre ?? 'Usuario' }} {{ Auth::user()->apellido ?? '' }}";
        document.getElementById('btnExportarPdf').addEventListener('click', function() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();

            // Detecta si es material o proyecto
            const tipoModal = document.querySelector('[x-show="open"] h2').textContent.includes('Material') ? 'material' : 'proyecto';

            // Header band
            doc.setFillColor(212,175,55); // dorado
            doc.rect(0, 0, pageWidth, 56, 'F');
            doc.setFontSize(18);
            doc.setTextColor(15, 15, 15);
            const title = tipoModal === 'material' ? 'Reporte de Material' : 'Reporte de Proyecto';
            doc.text(title, 40, 38);
            // Generated timestamp on right
            const genDate = new Date();
            const genStr = genDate.toLocaleString();
            doc.setFontSize(10);
            doc.setTextColor(20,20,20);
            doc.text(`Generado: ${genStr}`, pageWidth - 40, 38, { align: 'right' });

            // Prepare rows
            const padLeft = 40;
            const labelWidth = 140;
            const startY = 90;
            let rows = [];
            if (tipoModal === 'material') {
                rows = [
                    ['Material', document.getElementById('inputMaterial').value || '-'],
                    ['Estado', document.getElementById('inputEstado').value || '-'],
                    ['Stock', document.getElementById('inputStock').value || '-'],
                    ['Última actualización', document.getElementById('inputFecha').value || '-']
                ];
            } else {
                rows = [
                    ['Proyecto', document.getElementById('inputProyecto').value || '-'],
                    ['Ubicación', document.getElementById('inputUbicacion').value || '-'],
                    ['Materiales', document.getElementById('inputMateriales').value || '-'],
                    ['Estado', document.getElementById('inputEstadoProyecto').value || '-']
                ];
            }

            // Draw a simple table-like layout
            let y = startY;
            const rowH = 24;
            for (let i = 0; i < rows.length; i++) {
                const label = rows[i][0];
                const value = rows[i][1];

                // label cell background
                doc.setFillColor(245,245,245);
                doc.rect(padLeft, y - 16, labelWidth, rowH, 'F');
                // value cell background
                doc.setFillColor(255,255,255);
                doc.rect(padLeft + labelWidth, y - 16, pageWidth - padLeft - labelWidth - 40, rowH, 'F');

                // label text
                doc.setFontSize(11);
                doc.setTextColor(30,30,30);
                doc.text(label, padLeft + 8, y);

                // value text (wrap if needed)
                doc.setFontSize(11);
                doc.setTextColor(40,40,40);
                const split = doc.splitTextToSize(value, pageWidth - padLeft - labelWidth - 60);
                doc.text(split, padLeft + labelWidth + 8, y);

                y += Math.max(rowH, split.length * 12);
                y += 6;
                // check page break
                if (y > pageHeight - 80) {
                    doc.addPage();
                    y = 60;
                }
            }

            // Footer: author & page
            const footerY = pageHeight - 40;
            doc.setFontSize(9);
            doc.setTextColor(120,120,120);
            doc.text(`Impreso por: ${currentUserName}`, 40, footerY);
            const totalPages = doc.getNumberOfPages();
            for (let i = 1; i <= totalPages; i++) {
                doc.setPage(i);
                doc.text(`Página ${i} / ${totalPages}`, pageWidth - 40, footerY, { align: 'right' });
            }

            // Save
            const safeName = (tipoModal === 'material' ? (document.getElementById('inputMaterial').value || 'material') : (document.getElementById('inputProyecto').value || 'proyecto')).replace(/[^a-z0-9-_]/gi,'_');
            doc.save(`${title.replace(/\s+/g,'_')}_${safeName}_${genDate.toISOString().replace(/[:.]/g,'')}.pdf`);
        });
    });
</script>
@endsection
