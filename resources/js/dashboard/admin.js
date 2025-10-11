document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');
    const navItems = document.querySelectorAll('.nav-item');

    // Estado inicial: sidebar colapsada
    sidebar.classList.add('collapsed');

    // Toggle sidebar con botón
    toggleBtn.addEventListener('click', function(event) {
        event.stopPropagation();
        sidebar.classList.toggle('collapsed');

        // Cierra todos los submenús al colapsar
        if (sidebar.classList.contains('collapsed')) {
            document.querySelectorAll('.has-submenu.active').forEach(item => {
                item.classList.remove('active');
                item.querySelector('.submenu').style.height = '0px';
            });
        }
    });

    // Cierra la sidebar si se hace clic fuera
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            sidebar.classList.add('collapsed');
            document.querySelectorAll('.has-submenu.active').forEach(item => {
                item.classList.remove('active');
                item.querySelector('.submenu').style.height = '0px';
            });
        }
    });

    // Submenús desplegables
    const hasSubmenuItems = document.querySelectorAll('.has-submenu > a');

    hasSubmenuItems.forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            const parentLi = this.closest('.has-submenu');
            const submenu = parentLi.querySelector('.submenu');

            // Si sidebar está colapsada, primero expándelo
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');

                // Esperar el fin de la animación antes de abrir submenú
                setTimeout(() => {
                    toggleSubmenu(parentLi, submenu);
                }, 300); // 300ms = duración de tu transition en CSS
            } else {
                toggleSubmenu(parentLi, submenu);
            }
        });
    });

    function toggleSubmenu(parentLi, submenu) {
        // Cerrar otros submenús abiertos
        document.querySelectorAll('.has-submenu.active').forEach(openItem => {
            if (openItem !== parentLi) {
                openItem.classList.remove('active');
                openItem.querySelector('.submenu').style.height = '0px';
            }
        });

        // Alternar submenú actual con altura dinámica
        if (parentLi.classList.contains('active')) {
            submenu.style.height = '0px';
        } else {
            submenu.style.height = submenu.scrollHeight + "px";
        }

        parentLi.classList.toggle('active');
    }

    // Permite expandir sidebar al hacer clic en un nav-item colapsado
    navItems.forEach(item => {
        item.addEventListener('click', function(event) {
            if (sidebar.classList.contains('collapsed')) {
                event.preventDefault(); 
                sidebar.classList.remove('collapsed');
            }
        });
    });

    // Abrir sidebar al hacer clic en el borde izquierdo
    document.addEventListener('click', function(event) {
        if (sidebar.classList.contains('collapsed')) {
            if (event.clientX <= 20) {
                sidebar.classList.remove('collapsed');
            }
        }
    });
});
