<script>
  // Project modal controls
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
  })();
</script>
