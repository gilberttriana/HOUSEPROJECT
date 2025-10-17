<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8"/>
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            // Gold accent used for highlights and progress bars
            primary: "#D6A857",
            // Brand palette: warm brown background and deep card blue
            'brand-brown': '#2b2413',
            'card-blue': '#10212a',
            'accent-gold': '#D6A857'
          },
          fontFamily: {
            display: ["Inter", "Noto Sans"],
          },
          borderRadius: {
            DEFAULT: "0.25rem",
            lg: "0.5rem",
            xl: "0.75rem",
            full: "9999px"
          },
        },
      },
    }
  </script>
</head>
<body class="bg-[#2b2413] dark:bg-[#2b2413] font-display">
  <div class="flex min-h-screen">
    <x-sidebar />
    <main class="flex-1 p-8 lg:p-10 bg-transparent ml-72">
      @yield('content')
    </main>
  </div>
  @yield('scripts')
  <script>
    // Defined here so it's always available even when pages are loaded via AJAX.
    if (typeof window.initUsuariosModal !== 'function') {
      function initUsuariosModal(){
        try{
          const btn = document.getElementById('btnNuevoUsuario');
          const modal = document.getElementById('nuevoUsuarioModal');
          const close = document.getElementById('closeNuevoUsuario');
          const cancel = document.getElementById('cancelNuevoUsuario');
          if(btn && modal){
           
            const cloned = btn.cloneNode(true);
            btn.parentNode.replaceChild(cloned, btn);
            cloned.addEventListener('click', function(){
              modal.classList.remove('hidden'); modal.classList.add('flex');
              setTimeout(()=>{ modal.querySelector('input[name=nombre]')?.focus(); }, 10);
            });
          }
          if(close && modal){
            const clonedClose = close.cloneNode(true);
            close.parentNode.replaceChild(clonedClose, close);
            clonedClose.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); });
          }
          if(cancel && modal){
            const clonedCancel = cancel.cloneNode(true);
            cancel.parentNode.replaceChild(clonedCancel, cancel);
            clonedCancel.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); });
          }
          if(modal){
            // prevent multiple identical listeners: remove if exists by setting a flag
            if(!modal._listenerAdded){
              modal.addEventListener('click', function(e){ if(e.target === this){ this.classList.add('hidden'); this.classList.remove('flex'); } });
              modal._listenerAdded = true;
            }
          }
        }catch(err){ console.error('initUsuariosModal error', err); }
      }
      window.initUsuariosModal = initUsuariosModal;
      document.addEventListener('DOMContentLoaded', function(){ window.initUsuariosModal(); });
    }
    // Ensure a global no-op for report modal initializer so AJAX-loaded pages can rely on it
    if (typeof window.initReporteModal !== 'function') {
      function initReporteModal(){ /* no-op fallback; views can override */ }
      window.initReporteModal = initReporteModal;
      document.addEventListener('DOMContentLoaded', function(){ try{ window.initReporteModal(); } catch(e){ /* ignore */ } });
    }
    // Ensure a global no-op for role-change AJAX initializer
    if (typeof window.initRoleChangeAjax !== 'function'){
      function initRoleChangeAjax(){ /* no-op fallback; views can override */ }
      window.initRoleChangeAjax = initRoleChangeAjax;
      document.addEventListener('DOMContentLoaded', function(){ try{ window.initRoleChangeAjax(); } catch(e){} });
    }
    // AJAX navigation 
    (function(){
      function initAjaxNav(){
        const sidebar = document.getElementById('sidebarNav');
        if(!sidebar) return;
        sidebar.addEventListener('click', function(e){
          const a = e.target.closest('a');
          if(!a || !sidebar.contains(a)) return;
          const href = a.getAttribute('href');
          
          if(!href || href === '#') return;
            if(e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
          if(a.target && a.target.toLowerCase() !== '_self') return;
  
          let urlObj;
          try{ urlObj = new URL(href, location.origin); } catch(err){ console.debug('AJAXNav: invalid URL', href); return; }
          if(urlObj.origin !== location.origin) return; 
          e.preventDefault();
          const fetchUrl = urlObj.pathname + urlObj.search + (urlObj.hash ? ('#' + urlObj.hash.replace(/^#/,'')) : '');
          console.debug('AJAXNav: fetching', fetchUrl);
          fetchPage(fetchUrl, true);
        });
        
        window.addEventListener('popstate', function(e){
          const url = location.pathname + location.search;
          fetchPage(url, false);
        });
      }

      async function fetchPage(url, push){
        try{
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          if(!res.ok) throw new Error('Network error');
          const text = await res.text();
          const parser = new DOMParser();
          const doc = parser.parseFromString(text, 'text/html');
          const newMain = doc.querySelector('main');
          const curMain = document.querySelector('main');
          if(newMain && curMain){
            curMain.innerHTML = newMain.innerHTML;
            const newTitle = doc.querySelector('title');
            if(newTitle) document.title = newTitle.textContent;
            if(push) history.pushState({}, '', url);

            // Execute any scripts contained in the fetched main so view-level initializers register
            try {
              const inlineScripts = Array.from(newMain.querySelectorAll('script'));
              inlineScripts.forEach(s => {
                const scriptEl = document.createElement('script');
                if (s.src) {
                  scriptEl.src = s.src;
                  // ensure external scripts preserve order
                  scriptEl.async = false;
                } else {
                  scriptEl.textContent = s.textContent;
                }
                document.body.appendChild(scriptEl);
                // remove the script tag from the parsed node to avoid double-execution if needed
              });
            } catch(execErr){ console.error('Error executing fetched scripts', execErr); }

            try {
                if (typeof window.initAdminCharts === 'function') { try { window.initAdminCharts(); } catch(e){ console.error('initAdminCharts failed', e); } }
                if (typeof window.initAdminUI === 'function') { try { window.initAdminUI(); } catch(e){ console.error('initAdminUI failed', e); } }

                if (typeof window.initUsuariosModal === 'function') { try { window.initUsuariosModal(); } catch(e){ console.error('initUsuariosModal failed', e); } }
                if (typeof window.initRoleChangeAjax === 'function') { try { window.initRoleChangeAjax(); } catch(e){ console.error('initRoleChangeAjax failed', e); } }
                if (typeof window.initReporteModal === 'function') { try { window.initReporteModal(); } catch(e){ console.error('initReporteModal failed', e); } }
                if (typeof window.afterAjaxLoad === 'function') { try { window.afterAjaxLoad(); } catch (e) { console.error('afterAjaxLoad hook failed', e); } }
              } catch(err){ console.error('Error running page initializers', err); }
          } else {
            window.location.href = url;
          }
        }catch(err){
          console.error('AJAX navigation failed', err);
          window.location.href = url; 
        }
      }

      if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', initAjaxNav);
      } else {
        initAjaxNav();
      }
      // Global delegated handlers to ensure report modal opens/closes even if view binding missed
      document.addEventListener('click', function(e){
        try{
          const openBtn = e.target.closest('#btnAbrirReporte');
          if(openBtn){
            const modal = document.getElementById('reporteModal');
            if(modal){ modal.classList.remove('hidden'); modal.classList.add('flex'); setTimeout(()=>{ modal.querySelector('select[name=role]')?.focus(); }, 10); }
            return;
          }
          const closeBtn = e.target.closest('#closeReporteModal');
          if(closeBtn){ const modal = e.target.closest('#reporteModal') || document.getElementById('reporteModal'); if(modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); } return; }
          const cancelBtn = e.target.closest('#cancelReporte');
          if(cancelBtn){ const modal = e.target.closest('#reporteModal') || document.getElementById('reporteModal'); if(modal){ modal.classList.add('hidden'); modal.classList.remove('flex'); } return; }
        }catch(err){ console.error('Global report modal handler error', err); }
      });
    })();
  </script>
</body>
</html>