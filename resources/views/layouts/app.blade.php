<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8"/>
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Fonts & Icons -->
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
            primary: "#1173d4",
            "background-light": "#101922",
            "background-dark": "#101922",
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
<body class="bg-background-light dark:bg-background-dark font-display">
  <div class="flex min-h-screen">
    <x-sidebar />
    <main class="flex-1 p-8 lg:p-10 bg-gray-50 dark:bg-gray-900/50">
      @yield('content')
    </main>
  </div>
  @yield('scripts')
  <script>
    // AJAX navigation for sidebar links: intercept clicks, fetch content and replace <main>
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

              try {
                if (typeof window.initAdminCharts === 'function') { try { window.initAdminCharts(); } catch(e){ console.error('initAdminCharts failed', e); } }
                if (typeof window.initAdminUI === 'function') { try { window.initAdminUI(); } catch(e){ console.error('initAdminUI failed', e); } }

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
    })();
  </script>
</body>
</html>