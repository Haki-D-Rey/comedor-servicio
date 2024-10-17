<?php
ob_start();
?>
<div class="d-flex flex-column w-100 p-4 align-items-center">
    <div class="p-4 align-content-center d-flex flex-column align-items-center col-12 col-sm-9">
        <h2 class="custom-heading text-center mb-1">Formulario Control Estadisticos Servicios Alimentacion</h2>
        <hr class="hr-heading">
    </div>
    <div>
        <?php
        $width = 128;
        $height = 128;
        include __DIR__ . '/../../components/reloj.php'; ?>
    </div>

    <form id="myForm" class="d-flex flex-column col-12 col-md-9 col-lg-6 row-gap-3">
        <!-- Aquí van los campos del formulario -->
        <div for="component-filter" class="d-flex flex-column flex-sm-row column-gap-3 row-gap-3 align-items-sm-initial align-items-center">
            <div class="col-sm-6 col-9">
                <div class="d-flex flex-column row-gap-3">
                    <div>
                        <label for="inputDate" class="form-label">Fecha</label>
                        <input type="date" id="inputDate" name="inputDate" class="form-control" required>
                        <div class="invalid-feedback">
                            Debe seleccionar una fecha.
                        </div>

                    </div>

                    <div>
                        <label for="select_zona_usuario" class="form-label">Listado de Estaciones de Trabajo</label>
                        <select id="select_zona_usuario" class="form-select" aria-label="Default">
                            <option value="" selected disabled>Seleccione una zona</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="col-sm-6 col-9">
                <label for="inputSelect" class="form-label">Seleccione el Servicio</label>
                <select id="inputSelect" name="selectItems[]" class="form-select" multiple>
                    <!-- <option value="SIS-0001">Servicios Sistema SISERVI</option>
                    <option value="SIS-0002">Servicios Sistema DIETA</option> -->
                </select>
            </div>
        </div>

        <div id="itemsContainer" class="row row-cols-1 row-cols-md-4 g-3 col-12">
            <!-- Dynamic content will be inserted here -->
        </div>
        <div class="col-12">
            <input type="hidden" id="serviceCounts" name="serviceCounts">
            <button type="submit" class="btn btn-primary w-25">Enviar</button>
        </div>
    </form>

    <?php
    $content = ob_get_clean();
    include __DIR__ . '/../../layout/layout.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        window.endpointListaServiciosProductosDetalles = "<?php echo $routeParser->urlFor('servicios_productos_detalles.list'); ?>";
        window.endpointListaSistemas = "<?php echo $routeParser->urlFor('sistemas.list_all'); ?>";
        window.endpointListaTipoServicios = "<?php echo $routeParser->urlFor('tipo_servicios.list_all'); ?>";
        window.endpointListaServicioProductos = "<?php echo $routeParser->urlFor('servicio_producto.list_all'); ?>";
        window.endpointListaZonasUsuario = "<?php echo $routeParser->urlFor('zona_usuarios.list_relational_all', ['id' => $user_id]) ?>";
        window.endpointEnviarFormulario =  "<?php echo $routeParser->urlFor('control-estadisticos-servicios.create_form'); ?>";
        window.endpointListaZonaUsuariosServiciosValidar =  "<?php echo $routeParser->urlFor('detalle-zona-servicio-horario.get_zona_usuario_detalles', ["id"  => $user_id]); ?>";   
    </script>
    <script type="module" src="/assets/js/inscripcion_control.js"></script>
</div>