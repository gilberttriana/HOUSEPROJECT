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
