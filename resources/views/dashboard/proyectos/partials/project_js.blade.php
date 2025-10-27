<script>
  (function(){
    const btn = document.getElementById('btnAddProject');
    const modal = document.getElementById('addProjectModal');
    const close = document.getElementById('closeAddProject');
    const cancel = document.getElementById('cancelAddProject');
    if(btn && modal){
      btn.addEventListener('click', function(){ modal.style.display = 'flex'; });
    }
    if(close){ close.addEventListener('click', function(){ modal.style.display = 'none'; }); }
    if(cancel){ cancel.addEventListener('click', function(){ modal.style.display = 'none'; }); }
    if(modal){ modal.addEventListener('click', function(e){ if(e.target === this) this.style.display = 'none'; }); }
    // Habilitar/Deshabilitar inputs de cantidad según checkbox
    document.querySelectorAll('.material-checkbox').forEach(function(chk){
      chk.addEventListener('change', function(){
        const id = this.dataset.id;
        const qty = document.querySelector('.material-qty[data-id="'+id+'"]');
        if(qty){ qty.disabled = !this.checked; if(this.checked && (!qty.value || qty.value < 1)) qty.value = 1; }
      });
    });
  })();
</script>
