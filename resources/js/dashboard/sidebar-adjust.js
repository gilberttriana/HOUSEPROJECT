// Ajusta dinámicamente el margen izquierdo del <main> según el ancho real del sidebar
function adjustMainToSidebar(){
  try{
    const sidebar = document.getElementById('sidebar');
    const main = document.querySelector('main');
    const toggleBtn = document.getElementById('toggle-btn');
    if(!sidebar || !main) return;

    const computeAndApply = () => {
      const rect = sidebar.getBoundingClientRect();
  // gap base entre sidebar y main
  const baseGap = 16;
  // offsets extra según tamaño de pantalla para empujar ligeramente hacia la derecha
      const w = window.innerWidth || document.documentElement.clientWidth;
      let extra = 0;
  if (w >= 1280) { extra = 24; } // pantallas grandes: pequeño offset adicional
  else if (w >= 768) { extra = 16; } // pantallas medianas: offset ligero
      else { extra = 0; } // móviles no empujamos

      // si el sidebar está colapsado usar valor estimado pequeño
      let sideWidth = Math.ceil(rect.width || 0);
      if(sidebar.classList.contains('collapsed')){
        sideWidth = Math.max(56, Math.min(sideWidth || 70, 90));
      }

      // en pantallas pequeñas mantenemos margen 0 (sidebar overlay)
      if (w < 768) {
        // on small screens sidebar overlays; main uses full width
        document.documentElement.style.setProperty('--sidebar-offset', '0px');
        document.documentElement.style.setProperty('--sidebar-gap', '0px');
      } else {
        // Separar el ancho del sidebar y el gap para evitar errores de redondeo al hacer zoom
  const safety = 32; // px extra para prevenir solapamientos por redondeo/scrollbar (aumentado)
        const gap = baseGap + extra + safety;
        // sidebar-offset almacena SOLO el ancho del sidebar en px
        document.documentElement.style.setProperty('--sidebar-offset', sideWidth + 'px');
        // sidebar-gap almacena la separación adicional (baseGap + extra + safety)
        document.documentElement.style.setProperty('--sidebar-gap', gap + 'px');
      }
      // transición suave
      if(!main.style.transition) main.style.transition = 'margin-left 200ms ease';
    };

    // Inicial
    computeAndApply();

    // Actualizar aria-expanded en el toggleBtn si existe
    if(toggleBtn){
      toggleBtn.setAttribute('aria-expanded', sidebar.classList.contains('collapsed') ? 'false' : 'true');
    }

    // Recalcular al redimensionar ventana
    window.addEventListener('resize', () => { computeAndApply(); });

    // Observador de cambios en clases del sidebar (ej. .collapsed toggled por otro script)
    const mo = new MutationObserver(muts => {
      let changed = false;
      for(const m of muts){ if(m.attributeName === 'class') { changed = true; break; } }
      if(changed){
        computeAndApply();
        if(toggleBtn) toggleBtn.setAttribute('aria-expanded', sidebar.classList.contains('collapsed') ? 'false' : 'true');
      }
    });
    mo.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

    // También recalcular periódicamente un par de veces al inicio para cubrir animaciones
    setTimeout(computeAndApply, 120);
    setTimeout(computeAndApply, 400);
  }catch(err){ console.warn('sidebar-adjust error', err); }
}

if(document.readyState === 'loading'){
  document.addEventListener('DOMContentLoaded', adjustMainToSidebar);
} else { adjustMainToSidebar(); }

// Export for testability (not required)
export default adjustMainToSidebar;
