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
                })->orderByDesc('id_material')->get();

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

    @include('dashboard.proveedor._nuevo_material_modal')
    @include('dashboard.proveedor._scripts')

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
@endsection
