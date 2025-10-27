<script>
  // Materials report modal handlers (extracted partial)
  (function(){
    function openMaterialsModal(data){
      document.getElementById('matName').textContent = data.nombre || '-';
      document.getElementById('matDesc').textContent = data.descripcion || '-';
      document.getElementById('matQty').textContent = data.cantidad ?? '-';
      document.getElementById('matEstado').textContent = data.estado || '-';
      document.getElementById('materialsReportModal').classList.remove('hidden');
      document.getElementById('materialsReportModal').classList.add('flex');
    }

    function closeMaterialsModal(){
      const modal = document.getElementById('materialsReportModal');
      modal.classList.add('hidden'); modal.classList.remove('flex');
    }

    document.querySelectorAll('.material-row').forEach(function(row){
      row.addEventListener('click', function(){
          // normalizar estado para mostrar etiqueta legible
          function estadoLabel(raw){
            const s = (raw||'').toString().toLowerCase();
            // chequear 'desap' y 'rechaz' primero porque contienen la subcadena 'apro'
            if(s.indexOf('desap') !== -1 || s.indexOf('rechaz') !== -1 || s === '2' || s === '-1') return 'Desaprobado';
            if(s === '1' || s === 'true' || s.indexOf('apro') !== -1) return 'Aprobado';
            if(s === '0' || s === 'false' || s === '' || s.indexOf('esper') !== -1 || s.indexOf('pend') !== -1) return 'En espera';
            return raw;
          }
        const data = { nombre: row.dataset.nombre, descripcion: row.dataset.descripcion, cantidad: parseInt(row.dataset.cantidad||0, 10), estado: estadoLabel(row.dataset.estado || '') };
        openMaterialsModal(data);
      });
    });
    const closeMaterialsBtn = document.getElementById('closeMaterialsReport'); if(closeMaterialsBtn) closeMaterialsBtn.addEventListener('click', closeMaterialsModal);
    const materialsModalEl = document.getElementById('materialsReportModal'); if(materialsModalEl) materialsModalEl.addEventListener('click', function(e){ if(e.target === this) closeMaterialsModal(); });

    document.getElementById('btnMaterialsPdf').addEventListener('click', function(){
      const scope = document.querySelector('input[name=materialsScope]:checked').value;
      if(scope === 'single'){
        const nombre = document.getElementById('matName').textContent;
        const desc = document.getElementById('matDesc').textContent;
        const qty = document.getElementById('matQty').textContent;
  const estado = document.getElementById('matEstado').textContent;
        const doc = new window.jspdf.jsPDF();
        // header
        doc.setFillColor(16,34,52);
        doc.rect(0,0,210,25,'F');
        doc.setFontSize(16); doc.setTextColor(255,255,255); doc.text('Reporte de Material', 14, 16);
        // body
        doc.setFontSize(12); doc.setTextColor(0,0,0);
        doc.text(`Nombre: ${nombre}`, 14, 40);
        doc.text(`Descripción: ${desc}`, 14, 50);
        doc.text(`Cantidad: ${qty}`, 14, 60);
  doc.text(`Estado: ${estado}`, 14, 70);
        doc.save(`Reporte_Material_${nombre.replace(/\s+/g,'_')}.pdf`);
        closeMaterialsModal();
        return;
      }
      // all
  const rows = Array.from(document.querySelectorAll('.material-row')).map(r => ({ nombre: r.dataset.nombre, descripcion: r.dataset.descripcion, cantidad: r.dataset.cantidad, estado: estadoLabel(r.dataset.estado || '') }));
      const doc = new window.jspdf.jsPDF();
      doc.setFillColor(16,34,52); doc.rect(0,0,210,25,'F');
      doc.setFontSize(16); doc.setTextColor(255,255,255); doc.text('Reporte de Inventario - Todos los Materiales', 14, 16);
      doc.setFontSize(11); doc.setTextColor(0,0,0);
      let y = 36;
  rows.forEach(r=>{ doc.text(`${r.nombre}`, 14, y); doc.text(`${r.cantidad}`, 90, y); doc.text(`${r.estado}`, 130, y); doc.text(`${r.descripcion}`, 14, y+6); y += 14; if(y > 270){ doc.addPage(); y = 20; } });
      doc.save('Reporte_Materiales_Todos.pdf');
      closeMaterialsModal();
    });

  })();
</script>
