<!-- footer.php -->
</main>

<footer>
    <div class="container-footer">
        <p>&copy; 2024 Servicio Comedor Clientes - Hospital Militar Escuela Dr. Alejandro Davila Bolaños</p>
        <div class="container-div2">
            <a class="container-footer-div-social" href="https://www.facebook.com" target="_blank">
                <span>Facebook</span>
                <img src="./../../assets/img/logo-facebook.svg" alt="logo-facebook" width="24" height="24" />
            </a>
            <a class="container-footer-div-social" href="https://www.instagram.com" target="_blank">
                <span>Instagram</span>
                <img src="./../../assets/img/logo-instagran.svg" alt="logo-instagram" width="24" height="24" />
            </a>
            <a class="container-footer-div-social" href="https://www.youtube.com/@HMTVNicaragua" target="_blank">
                <span>Youtube</span>
                <img src="./../../assets/img/logo-youtube.svg" alt="logo-youtube" width="24" height="24" />
            </a>
        </div>
    </div>
</footer>
</div>
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