<?php
ob_start(); ?>
<div class="d-flex flex-column align-self-center col-12 col-xl-6 p-2 align-items-center h-100 justify-content-center">
    <div class="d-flex flex-column col-12 col-xl-9 justify-content-center">
        <div id="myForm" class="col g-3 w-100 d-flex justify-content-center">
            <div class="login-container">
                <div class="p-2 align-content-center d-flex flex-column align-items-center w-100">
                    <h2 class="custom-heading text-center mb-1">Incio de Sesion</h2>
                    <hr class="hr-heading" style="width: 200px">
                </div>
                <div>
                    <?php
                    $width = 108;
                    $height = 108;
                    include __DIR__ . '/../../components/reloj.php'; ?>
                </div>
                <form id="loginForm" class="col g-3 w-100 d-flex flex-column justify-content-center" action="/auth/login" method="POST">
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                        </span>
                        <input type="text" class="form-control" placeholder="Nombre de Usuario" name="username" aria-label="username" aria-describedby="basic-addon1" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-key-fill" viewBox="0 0 16 16">
                                <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2M2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                            </svg>
                        </span>
                        <input type="password" class="form-control" placeholder="Contraseña" aria-label="password" name="password" required>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-success text-center justify-content-center">Ingresar</button>
                    </div>
                </form>
                <div class="form-footer">
                    <p>¿Olvidaste tu contraseña? <a href="#">Recuperar aquí</a></p>
                    <p>¿No tienes cuenta? <a href="#">Registrarse</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layout/layout.php'; ?>