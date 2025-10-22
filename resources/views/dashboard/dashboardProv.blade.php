@extends('layouts.app-proveedor')

@section('content')
<div x-data="modalReporte" class="container mx-auto flex-grow p-4 sm:p-6 lg:p-8">
    <h1 class="text-4xl font-black text-white mb-8">Panel de Proveedor</h1>

    <!-- Bloques resumen en fila y más pequeños -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-4">
        <div class="rounded-xl bg-card-blue p-4 shadow flex flex-col items-center justify-center text-white">
            <h2 class="text-base font-semibold mb-2">Materiales Publicados</h2>
            <span class="text-2xl font-bold">125</span>
        </div>
        <div class="rounded-xl bg-card-blue p-4 shadow flex flex-col items-center justify-center text-white">
            <h2 class="text-base font-semibold mb-2">Proyectos Activos</h2>
            <span class="text-2xl font-bold">32</span>
        </div>
        <div class="rounded-xl bg-card-blue p-4 shadow flex flex-col items-center justify-center text-white">
            <h2 class="text-base font-semibold mb-2">Materiales en Uso</h2>
            <span class="text-2xl font-bold">87</span>
        </div>
    </div>

    <!-- Botón de Agregar Material -->
    <div class="mb-10 flex justify-center">
        <button class="bg-primary text-white px-6 py-3 rounded-lg font-semibold shadow opacity-50 cursor-not-allowed" disabled>
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
            <tbody class="divide-y divide-[#20304A]">
                <tr @click="abrir('material', {material: 'Concreto Reforzado', estado: 'Disponible', stock: '500 m³', fecha: '2024-07-26'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Concreto Reforzado</td>
                    <td class="px-6 py-4"><span class="bg-green-700/30 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">Disponible</span></td>
                    <td class="px-6 py-4">500 m³</td>
                    <td class="px-6 py-4">2024-07-26</td>
                </tr>
                <tr @click="abrir('material', {material: 'Acero Estructural', estado: 'Agotado', stock: '0 kg', fecha: '2024-07-20'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Acero Estructural</td>
                    <td class="px-6 py-4"><span class="bg-red-700/30 text-red-400 px-3 py-1 rounded-full text-xs font-semibold">Agotado</span></td>
                    <td class="px-6 py-4">0 kg</td>
                    <td class="px-6 py-4">2024-07-20</td>
                </tr>
                <tr @click="abrir('material', {material: 'Ladrillo Cerámico', estado: 'Disponible', stock: '10,000 unidades', fecha: '2024-07-25'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Ladrillo Cerámico</td>
                    <td class="px-6 py-4"><span class="bg-green-700/30 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">Disponible</span></td>
                    <td class="px-6 py-4">10,000 unidades</td>
                    <td class="px-6 py-4">2024-07-25</td>
                </tr>
                <tr @click="abrir('material', {material: 'Madera de Construcción', estado: 'Disponible', stock: '2,000 m³', fecha: '2024-07-24'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Madera de Construcción</td>
                    <td class="px-6 py-4"><span class="bg-green-700/30 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">Disponible</span></td>
                    <td class="px-6 py-4">2,000 m³</td>
                    <td class="px-6 py-4">2024-07-24</td>
                </tr>
                <tr @click="abrir('material', {material: 'Aislamiento Térmico', estado: 'Disponible', stock: '1,500 m²', fecha: '2024-07-23'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Aislamiento Térmico</td>
                    <td class="px-6 py-4"><span class="bg-green-700/30 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">Disponible</span></td>
                    <td class="px-6 py-4">1,500 m²</td>
                    <td class="px-6 py-4">2024-07-23</td>
                </tr>
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
            <tbody class="divide-y divide-[#20304A]">
                <tr @click="abrir('proyecto', {proyecto: 'Residencial Las Palmas', ubicacion: 'Ciudad del Sol, México', materiales: 'Concreto, Acero', estado: 'En Construcción'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Residencial Las Palmas</td>
                    <td class="px-6 py-4">Ciudad del Sol, México</td>
                    <td class="px-6 py-4">Concreto, Acero</td>
                    <td class="px-6 py-4"><span class="bg-yellow-700/30 text-yellow-400 px-3 py-1 rounded-full text-xs font-semibold">En Construcción</span></td>
                </tr>
                <tr @click="abrir('proyecto', {proyecto: 'Edificio Corporativo Alpha', ubicacion: 'Distrito Financiero, México', materiales: 'Acero, Vidrio', estado: 'Planificación'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Edificio Corporativo Alpha</td>
                    <td class="px-6 py-4">Distrito Financiero, México</td>
                    <td class="px-6 py-4">Acero, Vidrio</td>
                    <td class="px-6 py-4"><span class="bg-blue-700/30 text-blue-400 px-3 py-1 rounded-full text-xs font-semibold">Planificación</span></td>
                </tr>
                <tr @click="abrir('proyecto', {proyecto: 'Centro Comercial Omega', ubicacion: 'Zona Industrial, México', materiales: 'Concreto, Ladrillo', estado: 'Completado'})" class="cursor-pointer hover:bg-[#22304a]">
                    <td class="px-6 py-4">Centro Comercial Omega</td>
                    <td class="px-6 py-4">Zona Industrial, México</td>
                    <td class="px-6 py-4">Concreto, Ladrillo</td>
                    <td class="px-6 py-4"><span class="bg-green-700/30 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">Completado</span></td>
                </tr>
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

<script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
