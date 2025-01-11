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
    <link rel="stylesheet" href="./../../assets/css/dashboard.css">
    <!--Bootstrap-->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/docs.css" rel="stylesheet">
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <!--Typographia-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="/assets/js/sweetalert2.all.min.js"></script>
    <link href="/assets/css/sweetalert2.min.css" rel="stylesheet">
    <!-- DataTableJS -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css" />
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script> -->
    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.css" rel="stylesheet">
    <!-- <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css" rel="stylesheet"> -->
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.2.0/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.2.0/b-html5-3.2.0/b-print-3.2.0/r-3.0.3/datatables.min.js"></script>
    <!-- Incluir jQuery (si no lo tienes ya) -->
</head>

<body>
    <section id="layout" class="layout <?php echo $isLoggedIn ? '' : 'no-sidebar'; ?>">
        <header>
            <?php include 'header.php'; ?>
        </header>

        <?php if ($isLoggedIn): ?>
            <?php include 'sidebar.php'; ?>
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
</script>
<script type="module" src="/assets/js/header.js"></script>
<script type="module" src="/assets/js/sidebar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

</html>