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
                        <!-- Más elementos si es necesario -->
                    </ul>
                </div>
            </li>
            <!-- Más elementos del menú si es necesario -->
        </ul>

        <ul class="menu-footer">
            <li class="sidebar-item" data-id="facturacion">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shop" viewBox="0 0 16 16" style="font-size: 24px;">
                    <path d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.37 2.37 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0M1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5M4 15h3v-5H4zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1zm3 0h-2v3h2z" />
                </svg>
                <span class="sidebar-text">Ventas</span>
            </li>
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
</script>