<section class="sidebar nocollapsed" id="sidebar">
    <div class="menu-list">
        <ul class="menu">
            <li class="sidebar-item" data-id="inicio">
                <i class="fas fa-home"></i>
                <span class="sidebar-text">Inicio</span>
            </li>
            <li class="sidebar-item" data-id="formularios">
                <i class="fas fa-cube"></i>
                <span class="sidebar-text">Formularios</span>
                <i class="fas fa-chevron-down arrow" onclick="toggleSubMenu(this)"></i>
                <div class="container-submenu-list">
                    <ul class="submenu">
                        <li class="sidebar-item" data-id="formulario-control-estadisticos">
                            <i class="fas fa-cube"></i>
                            <span class="sidebar-text">Control Estadisticos de Servicios</span>
                        </li>
                        <!-- <li class="sidebar-item">
                            <i class="fas fa-cube"></i>
                            <span class="sidebar-text">Producto 2</span>
                        </li> -->
                    </ul>
                </div>
            </li>
            <!-- Agrega más elementos según sea necesario -->
        </ul>
    </div>
</section>


<script>
    function toggleSubMenu(element) {
        const submenu = element.nextElementSibling; // Obtiene el siguiente elemento (submenu)
        if (submenu.style.display === "block") {
            submenu.style.display = "none"; // Oculta el submenu
        } else {
            submenu.style.display = "block"; // Muestra el submenu
        }
    }

    document.querySelectorAll('.menu .sidebar-item, .submenu .sidebar-item').forEach(item => {
        item.addEventListener('click', function(event) {
            event.stopPropagation();

            const parentMenu = this.closest('.menu, .submenu');
            const hasActiveParentChild = parentMenu.querySelector('.sidebar-item.active');
            if (hasActiveParentChild) {

                if (hasActiveParentChild !== this) {
                    hasActiveParentChild.classList.remove('active');
                } else {
                    return;
                }
            }
            document.querySelectorAll('.menu .sidebar-item.active, .submenu .sidebar-item.active').forEach(el => {
                if (el !== this) el.classList.remove('active');
            });

            this.classList.add('active');

            if (parentMenu && !parentMenu.classList.contains('active')) {
                parentMenu.classList.add('active');
            }
            const grandParentMenu = parentMenu.closest('.sidebar-item');

            if (grandParentMenu) {
                grandParentMenu.classList.add('active');
            }

            sidebar.classList.add("nocollapsed");
            sidebar.classList.remove("collapsed");
        });
    });
</script>