<!-- example.php -->
<?php
ob_start(); // Inicia el almacenamiento en búfer de salida
?>
<h2>Bienvenido a Mi Aplicación</h2>
<p>Este es un ejemplo de contenido principal. Puedes personalizarlo según tus necesidades.</p>
<?php
$content = ob_get_clean(); // Obtiene el contenido del búfer y limpia el búfer

include __DIR__ .'/../layout/layout.php'; // Incluye el layout
?>
