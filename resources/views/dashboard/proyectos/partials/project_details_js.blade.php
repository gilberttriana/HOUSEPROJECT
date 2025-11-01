<script>
(function(){
  function safeText(v){ return (v === null || v === undefined) ? '-' : String(v); }

  function renderMaterials(list){
    if(!list || !list.length) return '<div class="text-white/60">No hay materiales asignados</div>';
    const rows = list.map(function(m){
      const nombre = safeText(m.nombre ?? m.name ?? m.nombre_material ?? 'Material');
      const qty = safeText(m.pivot && m.pivot.cantidad ? m.pivot.cantidad : (m.cantidad ?? m.cantidad_solicitada ?? '1'));
      return `<div class="flex items-center justify-between py-1 px-2 bg-gray-800 rounded mb-1"><div class="truncate">${nombre}</div><div class="text-xs text-white/60">x ${qty}</div></div>`;
    });
    return rows.join('');
  }

  function showModal(data){
    try{
      const modal = document.getElementById('projectDetailsModal');
      if(!modal) return;
      document.getElementById('pd_name').textContent = data.nombre ?? data.name ?? 'Proyecto';
      const solicitante = (data.usuario && (data.usuario.nombre || data.usuario.nombre_usuario)) ? (data.usuario.nombre || data.usuario.nombre_usuario) : (data.solicitante ?? '-');
      document.getElementById('pd_solicitante').textContent = solicitante;
      document.getElementById('pd_descripcion').textContent = data.descripcion ?? data.descripcion_proyecto ?? '-';
      document.getElementById('pd_fecha_inicio').textContent = data.fecha_inicio ?? data.fecha_inicio_estimado ?? (data.fecha_inicio_estimado ? data.fecha_inicio_estimado : '-') ;
      document.getElementById('pd_fecha_fin').textContent = data.fecha_fin ?? data.fecha_finalizacion ?? data.fecha_finalizacion_estimado ?? '-';
      document.getElementById('pd_presupuesto').textContent = (data.presupuesto_est ?? data.presupuesto) ? (data.presupuesto_est ?? data.presupuesto) : '-';
      document.getElementById('pd_estado').textContent = data.estado ?? '-';
      document.getElementById('pd_progreso').textContent = (typeof data.progreso !== 'undefined' && data.progreso !== null) ? data.progreso + '%' : (data.progress ? data.progress + '%' : '-');

      // materiales: puede venir como array en data.materiales
      const matContainer = document.getElementById('pd_materiales');
      matContainer.innerHTML = renderMaterials(data.materiales || data.materiales || []);

      modal.style.display = 'flex';
    }catch(e){ console.error('Error mostrando modal detalles:', e); }
  }

  function hideModal(){
    const modal = document.getElementById('projectDetailsModal');
    if(modal) modal.style.display = 'none';
  }

  // Delegación: capturar clicks en botones .btn-view-details
  document.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('.btn-view-details');
    if(!btn) return;
    e.preventDefault();
    const raw = btn.getAttribute('data-project');
    if(!raw){ showModal({}); return; }
    try{
      const data = JSON.parse(raw);
      showModal(data);
    }catch(err){
      // si no se puede parsear, intentar pedir al servidor por id
      const id = (btn.getAttribute('data-id') || btn.dataset.id);
      if(!id) { showModal({}); return; }
      fetch('/proyectos/' + id + '?ajax=1')
        .then(r=>r.json())
        .then(json=> showModal(json))
        .catch(()=> showModal({}));
    }
  });

  // Cerrar modal
  document.getElementById('closeProjectDetails')?.addEventListener('click', hideModal);
  document.getElementById('pd_close_btn')?.addEventListener('click', hideModal);
  document.getElementById('projectDetailsModal')?.addEventListener('click', function(e){ if(e.target === this) hideModal(); });
})();
</script>