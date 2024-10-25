<!-- layout.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Aplicación</title>
    <link rel="stylesheet" href="./../../assets/css/layout.css">
    <link rel="stylesheet" href="./../../assets/css/header.css">
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
    <section class="layout">
        <header>
            <?php include 'header.php'; ?>
        </header>
        <div class="sidebar nocollapsed" id="sidebar">
            <div class="content-flex">
                <div>
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </div>
                <div>
                    <i class="fas fa-cube"></i>
                    <span>Producto</span>
                </div>
                <div>
                    <i class="fas fa-th"></i>
                    <span>Categoria</span>
                </div>
                <div>
                    <i class="fas fa-truck"></i>
                    <span>Proveedor</span>
                </div>
                <div>
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </div>
                <div>
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </div>
                <div>
                    <i class="fas fa-cube"></i>
                    <span>Producto</span>
                </div>
                <div>
                    <i class="fas fa-th"></i>
                    <span>Categoria</span>
                </div>
                <div>
                    <i class="fas fa-truck"></i>
                    <span>Proveedor</span>
                </div>
                <div>
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </div>
                <div>
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </div>
                <div>
                    <i class="fas fa-cube"></i>
                    <span>Producto</span>
                </div>
                <div>
                    <i class="fas fa-th"></i>
                    <span>Categoria</span>
                </div>
                <div>
                    <i class="fas fa-truck"></i>
                    <span>Proveedor</span>
                </div>
                <div>
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </div>
                <div>
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </div>
                <div>
                    <i class="fas fa-cube"></i>
                    <span>Producto</span>
                </div>
                <div>
                    <i class="fas fa-th"></i>
                    <span>Categoria</span>
                </div>
                <div>
                    <i class="fas fa-truck"></i>
                    <span>Proveedor</span>
                </div>
                <div>
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </div>
                <div>
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </div>
                <div>
                    <i class="fas fa-cube"></i>
                    <span>Producto</span>
                </div>
                <div>
                    <i class="fas fa-th"></i>
                    <span>Categoria</span>
                </div>
                <div>
                    <i class="fas fa-truck"></i>
                    <span>Proveedor</span>
                </div>
                <div>
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </div>
            </div>
        </div>

        <main class="nocollapsed-child-no-sidebar">
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
    window.tokenAccess = <?php
                            session_start();
                            echo json_encode($_SESSION['jwt_token'] ?? ""); // Usa json_encode para manejar comillas y espacios
                            ?>;
    console.log(window.tokenAccess);
</script>
<script type="module" src="/assets/js/header.js"></script>

</html>