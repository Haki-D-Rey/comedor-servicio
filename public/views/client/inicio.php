<!-- example.php -->
<?php
ob_start(); // Inicia el almacenamiento en búfer de salida
?>
<h2>Bienvenido a Mi Aplicación</h2>
<p>Este es un ejemplo de contenido principal. Puedes personalizarlo según tus necesidades.</p>
<?php
date_default_timezone_set('America/Guatemala');

// Definir los horarios de los servicios de alimentación
$mealServices = [
    'Desayuno' => ['start' => '05:00', 'end' => '07:00'],
    'Almuerzo' => ['start' => '11:00', 'end' => '14:00'],
    'Cena' => ['start' => '17:30', 'end' => '20:00'],
    'Refracción' => ['start' => '22:00', 'end' => '23:30']
];

// Obtener la hora actual
$currentTime = new DateTime();

// Función para verificar si la hora actual está dentro de un rango de tiempo
function isCurrentMealActive($currentTime, $startTime, $endTime)
{
    $start = DateTime::createFromFormat('H:i', $startTime);
    $end = DateTime::createFromFormat('H:i', $endTime);

    if ($start <= $end) {
        return ($currentTime >= $start && $currentTime <= $end);
    } else {
        // En caso de que el rango cruce la medianoche
        return ($currentTime >= $start || $currentTime <= $end);
    }
}
?>
<div class="content-schedule">
    <?php foreach ($mealServices as $service => $times): ?>
        <?php
        $isActive = isCurrentMealActive($currentTime, $times['start'], $times['end']);
        ?>
        <div class="card <?= $isActive ? 'active' : 'inactive' ?>">
            <h3><?= $service ?></h3>
            <p>Horario: <?= $times['start'] ?> - <?= $times['end'] ?></p>
            <?php if ($isActive): ?>
                <p><strong>¡Servicio actualmente activo!</strong></p>
            <?php endif; ?>
            <?php if (!$isActive): ?>
                <p><strong>¡Fuera de Servicio!</strong></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="contenedor-filtro">
    <input class="input-content" type="text" name="cod" id="cod">
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.content-schedule').classList.add('loaded');
});
</script>

<?php
$content = ob_get_clean(); // Obtiene el contenido del búfer y limpia el búfer

include __DIR__ . '/../layout/layout.php'; // Incluye el layout
?>