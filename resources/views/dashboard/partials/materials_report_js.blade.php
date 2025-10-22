<script>
  // Materials report modal handlers (extracted partial)
  (function(){
    function openMaterialsModal(data){
      document.getElementById('matName').textContent = data.nombre || '-';
      document.getElementById('matDesc').textContent = data.descripcion || '-';
      document.getElementById('matQty').textContent = data.cantidad ?? '-';
      document.getElementById('matFecha').textContent = data.fecha_actualizacion || '-';
      document.getElementById('materialsReportModal').classList.remove('hidden');
      document.getElementById('materialsReportModal').classList.add('flex');
    }

    function closeMaterialsModal(){
      const modal = document.getElementById('materialsReportModal');
      modal.classList.add('hidden'); modal.classList.remove('flex');
    }

    document.querySelectorAll('.material-row').forEach(function(row){
      row.addEventListener('click', function(){
        const data = { nombre: row.dataset.nombre, descripcion: row.dataset.descripcion, cantidad: parseInt(row.dataset.cantidad||0, 10), fecha_actualizacion: row.dataset.fecha || row.dataset.fecha_actualizacion || '' };
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
        const fecha = document.getElementById('matFecha').textContent;
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
        doc.text(`Actualizado: ${fecha}`, 14, 70);
        doc.save(`Reporte_Material_${nombre.replace(/\s+/g,'_')}.pdf`);
        closeMaterialsModal();
        return;
      }
      // all
      const rows = Array.from(document.querySelectorAll('.material-row')).map(r => ({ nombre: r.dataset.nombre, descripcion: r.dataset.descripcion, cantidad: r.dataset.cantidad, fecha_actualizacion: r.dataset.fecha || r.dataset.fecha_actualizacion || '' }));
      const doc = new window.jspdf.jsPDF();
      doc.setFillColor(16,34,52); doc.rect(0,0,210,25,'F');
      doc.setFontSize(16); doc.setTextColor(255,255,255); doc.text('Reporte de Inventario - Todos los Materiales', 14, 16);
      doc.setFontSize(11); doc.setTextColor(0,0,0);
      let y = 36;
      rows.forEach(r=>{ doc.text(`${r.nombre}`, 14, y); doc.text(`${r.cantidad}`, 110, y); doc.text(`${r.fecha_actualizacion}`, 150, y); doc.text(`${r.descripcion}`, 14, y+6); y += 14; if(y > 270){ doc.addPage(); y = 20; } });
      doc.save('Reporte_Materiales_Todos.pdf');
      closeMaterialsModal();
    });

  })();
</script>
