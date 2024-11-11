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
    console.log(window.tokenAccess);
</script>
<script type="module" src="/assets/js/header.js"></script>
<script type="module" src="/assets/js/sidebar.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</html>