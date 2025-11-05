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
  <style>
  /* Implementación CSS-only (sin lógica JS) para márgenes estables entre
     sidebar y main. Usamos margin-left en main para desplazar todo el bloque
     principal y evitar superposición cuando el sidebar está fijo. Manteniendo
     transiciones para suavizar cambios. */
  /* Por defecto no desplazar todo el main — permitimos que las vistas opt-in
     (añadiendo la clase .with-sidebar) reciban el margen necesario. Esto evita
     que cambios globales afecten a otras páginas. */
  main { margin-left: 0; transition: margin-left 220ms ease; }

  /* Clase por vista: aplicar desplazamiento sólo cuando la vista lo requiera */
  .with-sidebar { transition: margin-left 220ms ease; }

  @media (min-width: 1280px) {
    .with-sidebar { margin-left: calc(18rem + 1.5rem) !important; }
  }

  @media (min-width: 768px) and (max-width: 1279px) {
    .with-sidebar { margin-left: calc(18rem + 1rem) !important; }
  }

  /* Colapsado: si la vista añade la clase .sidebar-collapsed, usar un margen menor */
  .sidebar-collapsed .with-sidebar { margin-left: calc(4.5rem + 1rem) !important; }

  @media (max-width: 767px) {
    .with-sidebar { margin-left: 0 !important; padding-left: 1rem !important; padding-right: 1rem !important; }
    .sidebar { position: fixed; z-index: 30; }
  }
  </style>
</head>
<body class="bg-[#2b2413] dark:bg-[#2b2413] font-display">
  <div class="flex min-h-screen">
    <x-sidebar />
    <main class="flex-1 p-6 lg:p-8 bg-transparent">
      @yield('content')
    </main>
  </div>
  @yield('scripts')
  {{-- Sidebar sizing handled by CSS-only rules; no JS measurement required anymore --}}
  <script>
    // Defined here so it's always available even when pages are loaded via AJAX.
      if (typeof window.initUsuariosModal !== 'function') {
      function initUsuariosModal(){
        try{
          const btn = document.getElementById('btnNuevoUsuario');
          const modal = document.getElementById('nuevoUsuarioModal');
          const close = document.getElementById('closeNuevoUsuario');
          const cancel = document.getElementById('cancelNuevoUsuario');
          if(btn && modal && !btn.dataset.listenerAttached){
            btn.addEventListener('click', function(){ modal.classList.remove('hidden'); modal.classList.add('flex'); setTimeout(()=>{ modal.querySelector('input[name=nombre]')?.focus(); }, 10); });
            btn.dataset.listenerAttached = '1';
          }
          if(close && modal && !close.dataset.listenerAttached){ close.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }); close.dataset.listenerAttached = '1'; }
          if(cancel && modal && !cancel.dataset.listenerAttached){ cancel.addEventListener('click', function(){ modal.classList.add('hidden'); modal.classList.remove('flex'); }); cancel.dataset.listenerAttached = '1'; }
          if(modal && !modal.dataset.clickGuard){ modal.addEventListener('click', function(e){ if(e.target === this){ this.classList.add('hidden'); this.classList.remove('flex'); } }); modal.dataset.clickGuard = '1'; }
        }catch(err){ /* initUsuariosModal error suppressed for cleaner logs */ }
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
        // Temporal: desactivar la navegación AJAX porque está causando fallos.
        // Devolver aquí permite que los enlaces funcionen como enlaces normales (carga completa de página).
        return;
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
          try{ urlObj = new URL(href, location.origin); } catch(err){ return; }
          if(urlObj.origin !== location.origin) return; 
          e.preventDefault();
          const fetchUrl = urlObj.pathname + urlObj.search + (urlObj.hash ? ('#' + urlObj.hash.replace(/^#/,'')) : '');
          
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
                if (s.src) { scriptEl.src = s.src; scriptEl.async = false; } else { scriptEl.textContent = s.textContent; }
                document.body.appendChild(scriptEl);
              });
            } catch(execErr){ /* suppressed script exec error */ }

            try {
                if (typeof window.initAdminUI === 'function') { try { window.initAdminUI(); } catch(e){} }

                if (typeof window.initUsuariosModal === 'function') { try { window.initUsuariosModal(); } catch(e){} }
                if (typeof window.initRoleChangeAjax === 'function') { try { window.initRoleChangeAjax(); } catch(e){} }
                if (typeof window.initReporteModal === 'function') { try { window.initReporteModal(); } catch(e){} }
                if (typeof window.afterAjaxLoad === 'function') { try { window.afterAjaxLoad(); } catch (e) {} }
              } catch(err){ /* suppressed initializer errors */ }
          } else {
            window.location.href = url;
          }
        }catch(err){
          // navigation failed silently; fallback to full page load
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