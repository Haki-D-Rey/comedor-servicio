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
    <div class="d-flex flex-row column-gap-5 justify-content-end w-100">
        <div class="d-flex flex-row w-25">

            <!-- <?php
                    date_default_timezone_set('America/Guatemala');

                    // Definir los horarios de los servicios de alimentación
                    $mealServices = [
                        'Fuera de Servicio' => [],
                        'Desayuno' => ['start' => '05:00', 'end' => '07:00'],
                        'Almuerzo' => ['start' => '11:00', 'end' => '14:00'],
                        'Cena' => ['start' => '16:30', 'end' => '20:00'],
                        'Refracción' => ['start' => '22:00', 'end' => '23:30'],
                    ];

                    // Obtener la hora actual
                    $currentTime = new DateTime();

                    // Función para verificar si la hora actual está dentro de un rango de tiempo
                    function isCurrentMealActive($currentTime, $startTime, $endTime)
                    {
                        if (!$startTime || !$endTime) {
                            return true;
                        }
                        $start = DateTime::createFromFormat('H:i', $startTime);
                        $end = DateTime::createFromFormat('H:i', $endTime);

                        if ($start <= $end) {
                            return ($currentTime >= $start && $currentTime <= $end);
                        } else {
                            // En caso de que el rango cruce la medianoche
                            return ($currentTime >= $start || $currentTime <= $end);
                        }
                    }

                    // Encontrar el índice del servicio activo
                    $activeIndex = 'Fuera de Servicio';  // Por defecto
                    foreach ($mealServices as $index => $times) {
                        if ($index !== 'Fuera de Servicio' && isCurrentMealActive($currentTime, $times['start'], $times['end'])) {
                            $activeIndex = $index;
                            break;
                        }
                    }
                    ?> -->
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
                    <div class="content-search">
                        <label for="inputCodigo" class="form-label">Lector de Tarjetas</label>
                        <input id="inputCodigo" class="input-content" type="text" placeholder="Escanea o ingresa código" style="height: 40px; width: 98%; border: 2px solid #077682;">
                    </div>
                </div>
                <div class="col-md-6 mt-auto mr-0">
                    <input id="inputCantidad"  class="input-content" type="number" placeholder="Ingrese número" style="height: 40px; width: 40%; border: 2px solid #077682;">
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
    </script>
    <script type="module" src="/assets/js/Facturacion/ventas.js"></script>

</div>