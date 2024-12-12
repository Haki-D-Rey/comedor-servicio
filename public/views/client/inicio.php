<?php
ob_start(); // Inicia el almacenamiento en búfer de salida
?>
<div class="d-flex flex-column align-items-center w-100 p-4">
    <div class="p-4 align-content-center d-flex flex-column align-items-center w-50">

        <h2 class="custom-heading text-center mb-1">Horarios de Servicio</h2>
        <hr class="hr-heading">
    </div>
    <div>
        <?php
        include __DIR__ . '/../components/reloj.php'; // Incluye el layout
        ?>
    </div>
    <div id="toggleButtonIdentificador" class="container-toogle" value="ITF-001">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layout-sidebar-inset-reverse" viewBox="0 0 16 16">
            <path d="M2 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z" />
            <path d="M13 4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z" />
        </svg>
        <span id="iconLabel" style="margin-left: 8px;">Lector de Tarjeta</span>
    </div>
    <div class="d-flex flex-row column-gap-5 justify-content-end w-100">
        <div class="d-flex flex-row w-25">
            <div id="carouselExampleDark" class="carousel carousel-dark slide" style="width: 450px; height: 200px;" data-bs-ride="true">
                <div class="carousel-indicators">
                    <!-- Los indicadores se generarán dinámicamente desde JavaScript -->
                </div>
                <div class="carousel-inner h-100 w-100" style="border: 2px solid green; border-radius: 10px;">
                    <!-- Los elementos del carrusel se generarán dinámicamente desde JavaScript -->
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
        <div class="d-flex flex-column w-50 row-gap-5">
            <div class="d-flex flex-row w-50 gap-4"> <!-- Mejorar gap y organización -->
                <!-- Dropdowns en una fila -->
                <div class="d-flex gap-3 dropdown">
                    <button class="dropdown-toggle custom-dropdown-btn-ventas" type="button" id="dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        Selecciona una opción
                    </button>
                    <ul id="dropdownList" class="dropdown-menu custom-dropdown-menu" aria-labelledby="dropdownMenu">
                    </ul>
                </div>

                <!-- Dropdown con estilos de Bootstrap y personalizados -->
                <div class="dropdown">
                    <select class="dropdown-toggle custom-dropdown-btn-ventas" id="dropdown_sistema" class="dropdown-menu custom-dropdown-menu" aria-labelledby="dropdown_sistema">
                        <option value="" selected disabled>Seleccione evento</option>
                    </select>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div id="content-input-search" class="content-search" style="display: none;">
                        <label for="inputSearch" class="form-label">Filtro de Búsqueda</label>
                        <input id="inputSearch" class="input-content" type="text" placeholder="Buscar..." oninput="buscarServicio(this.value)" style="height: 40px; width: 98%; border: 2px solid #077682;">
                        <div class="input-container">
                            <span id="loadingIcon" style="display: none;">
                                <!-- Ícono de carga -->
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                            <span id="successIcon" style="display: none; font-size: 22px; color: #077682;">
                                <!-- Ícono de éxito -->
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <ul id="resultList" class="list-group" style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-top: 10px; display: none;"></ul>
                    </div>
                    <div id="content-input-codigo" class="content-search">
                        <label for="inputCodigo" class="form-label">Lector de Tarjetas</label>
                        <input id="inputCodigo" class="input-content" type="password" placeholder="Escanea o ingresa código" style="height: 40px; width: 98%; border: 2px solid #077682;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column">
                        <label for="inputCantidad" class="form-label">Cantidad</label>
                        <input id="inputCantidad" class="input-content" type="number" placeholder="Ingrese número" style="height: 40px; width: 40%; border: 2px solid #077682;">
                    </div>
                </div>
            </div>

            <!-- Filtro de Búsqueda -->
            <div class="row g-3">
                <div class="col-md-6">
                </div>
            </div>
        </div>
    </div>

    <?php
    $content = ob_get_clean(); // Obtiene el contenido del búfer y limpia el búfer
    include __DIR__ . '/../layout/layout.php'; // Incluye el layout
    ?>

    <script>
        window.endpointListaServiciosProductosDetalles = "<?php echo $routeParser->urlFor('servicios_productos_detalles.list'); ?>";
        window.endpointListaSistemas = "<?php echo $routeParser->urlFor('sistemas.list_all'); ?>";
        window.endpointListaTipoServicios = "<?php echo $routeParser->urlFor('tipo_servicios.list_all'); ?>";
        window.endpointListaServicioProductos = "<?php echo $routeParser->urlFor('servicio_producto.list_all'); ?>";
        window.endpointListaZonasUsuario = "<?php echo $routeParser->urlFor('zona_usuarios.list_relational_all', ['id' => $user_id]) ?>";
        window.endpointEnviarFormulario = "<?php echo $routeParser->urlFor('control-estadisticos-servicios.create_form'); ?>";
        window.endpointListaZonaUsuariosServiciosValidar = "<?php echo $routeParser->urlFor('detalle-zona-servicio-horario.get_zona_usuario_detalles', ["id"  => $user_id]); ?>";
        window.endpointFacturacion = "<?php echo $routeParser->urlFor('ventas.create'); ?>";
        window.endpointFilterClientByName = "<?php echo $routeParser->urlFor('clientes.get_filter_client'); ?>"
        window.endpointClientRelationalIdentification = "<?php echo $routeParser->urlFor('clientes.get_client_relational_identification'); ?>"
        
    </script>
    <script type="module" src="/assets/js/Facturacion/ventas.js"></script>
</div>