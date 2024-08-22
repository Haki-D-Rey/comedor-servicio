<?php
ob_start(); // Inicia el almacenamiento en búfer de salida
?>
<div class="d-flex flex-column w-100 p-4">
    <div class="p-2 align-content-center">

        <h2 class="text-center mb-4">Horarios de Servicio</h2>
    </div>
    <div class="d-flex flex-row g-5">
        <div class="d-flex flex-row w-25">

            <?php
            date_default_timezone_set('America/Guatemala');

            // Definir los horarios de los servicios de alimentación
            $mealServices = [
                'Fuera de Servicio' => [],
                'Desayuno' => ['start' => '05:00', 'end' => '07:00'],
                'Almuerzo' => ['start' => '12:00', 'end' => '14:00'],
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
            ?>
            <div id="carouselExampleDark" class="carousel carousel-dark slide" style="width: 450px; height: 200px">
                <div class="carousel-indicators">
                    <?php foreach ($mealServices as $index => $service): ?>
                        <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="<?= array_search($index, array_keys($mealServices)) ?>" class="<?= $index == $activeIndex ? 'active' : '' ?>" aria-label="Slide <?= array_search($index, array_keys($mealServices)) + 1 ?>" <?= $index == $activeIndex ? 'aria-current="true"' : '' ?>></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner h-100 w-100">
                    <?php foreach ($mealServices as $index => $times): ?>
                        <?php
                        $start = empty($times['start']) ? null : $times['start'];
                        $end = empty($times['end']) ? null : $times['end'];
                        $service = $index;
                        $isActive = isCurrentMealActive($currentTime, $start, $end);
                        $isActiveClass = $index == $activeIndex ? 'active' : '';
                        ?>
                        <div class="carousel-item <?= $isActiveClass ?> h-100 w-100" data-bs-interval="10000">
                            <div class="card h-100 w-100 <?= $isActive ? (($service == 'Fuera de Servicio') ? 'bg-danger' : 'bg-success') . ' text-white' : 'bg-transparent text-dark' ?>">
                                <div class="card-body text-center">
                                    <h3><?= $service ?></h3>
                                    <?php if ($index !== 'Fuera de Servicio'): ?>
                                        <p>Horario: <?= $times['start'] ?> - <?= $times['end'] ?></p>
                                        <?php if ($isActive): ?>
                                            <p><strong>¡Servicio actualmente activo!</strong></p>
                                        <?php else: ?>
                                            <p><strong>¡Fuera de Servicio!</strong></p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p><strong>Sin servicio de alimentación disponible</strong></p>
                                        <p><strong>Por favor, vuelva más tarde.</strong></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const carousel = new bootstrap.Carousel(document.querySelector('#carouselExampleDark'), {
                        interval: 2000,
                        ride: 'carousel'
                    });
                    carousel.to(<?= array_search($activeIndex, array_keys($mealServices)) ?>);
                });
            </script>
        </div>
        <div class="w-50">
            <div class="content-search">
                <label for="ingresa-codigo">Pasa la Tarjeta por el Lector</label>
                <input class="input-content" type="text">
            </div>

        </div>
    </div>

    <?php
    $content = ob_get_clean(); // Obtiene el contenido del búfer y limpia el búfer

    include __DIR__ . '/../layout/layout.php'; // Incluye el layout
    ?>
</div>