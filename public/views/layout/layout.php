<!-- layout.php -->
<?php session_start();
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Aplicación</title>
    <link rel="stylesheet" href="./../../assets/css/layout.css">
    <link rel="stylesheet" href="./../../assets/css/header.css">
    <link rel="stylesheet" href="./../../assets/css/sidebar.css">
    <!--Bootstrap-->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/docs.css" rel="stylesheet">
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <!--Typographia-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="/assets/js/sweetalert2.all.min.js"></script>
    <link href="/assets/css/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <section id="layout" class="layout <?php echo $isLoggedIn ? '' : 'no-sidebar'; ?>">
        <header>
            <?php include 'header.php'; ?>
        </header>

        <?php if ($isLoggedIn): ?>
            <section class="sidebar nocollapsed" id="sidebar">
                <div class="menu-list">
                    <ul class="menu">
                        <li class="sidebar-item">
                            <i class="fas fa-home"></i>
                            <span class="sidebar-text">Inicio</span>
                        </li>
                        <li class="sidebar-item">
                            <i class="fas fa-cube"></i>
                            <span class="sidebar-text">Producto</span>
                            <i class="fas fa-chevron-down arrow" onclick="toggleSubMenu(this)"></i>
                            <div class="container-submenu-list">
                                <ul class="submenu">
                                    <li class="sidebar-item">
                                        <i class="fas fa-cube"></i>
                                        <span class="sidebar-text">Producto 1</span>
                                    </li>
                                    <li class="sidebar-item">
                                        <i class="fas fa-cube"></i>
                                        <span class="sidebar-text">Producto 2</span>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="sidebar-item">
                            <i class="fas fa-th"></i>
                            <span class="sidebar-text">Categoria</span>
                            <i class="fas fa-chevron-down arrow" onclick="toggleSubMenu(this)"></i>
                            <div class="container-submenu-list">
                                <ul class="submenu">
                                    <li class="sidebar-item">
                                        <i class="fas fa-th"></i>
                                        <span class="sidebar-text">Categoria 1</span>
                                    </li>
                                    <li class="sidebar-item">
                                        <i class="fas fa-th"></i>
                                        <span class="sidebar-text">Categoria 2</span>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="sidebar-item">
                            <i class="fas fa-truck"></i>
                            <span class="sidebar-text">Proveedor</span>
                        </li>
                        <li class="sidebar-item">
                            <i class="fas fa-users"></i>
                            <span class="sidebar-text">Usuarios</span>
                        </li>
                        <!-- Agrega más elementos según sea necesario -->
                    </ul>
                </div>
            </section>
        <?php endif; ?>

        <main class="<?php echo $isLoggedIn ? 'nocollapsed-child-no-sidebar' : 'full-width'; ?>"> <!-- Cambiado según estado de sesión -->
            <div class="content-conditional-footer">
                <div class="content">
                    <?php echo $content; ?>
                </div>
            </div>
            <div class="footer">
                <?php include 'footer.php'; ?>
            </div>
        </main>
    </section>
</body>

<script>
    var sidebar = document.querySelector(".sidebar");
    window.tokenAccess = <?php
                            session_start();
                            echo json_encode($_SESSION['jwt_token'] ?? ""); // Usa json_encode para manejar comillas y espacios
                            ?>;
    console.log(window.tokenAccess);

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
<script type="module" src="/assets/js/header.js"></script>

</html>