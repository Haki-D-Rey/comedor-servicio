<?php
ob_start();
?>
<link rel="stylesheet" href="./../../../assets/css/reportes.css">
<!-- Contenedor Principal -->
<div class="container">
  <div class="title-dashboard">
    <h3>SECCION DE REPORTERIA SURE</h3>
  </div>
  <div class="report-item">
    <div>
      <div class="report-title">Reporte de Ventas</div>
      <div class="report-date">reporte generado en excel</div>
    </div>
    <div class="actions">
      <button class="icon-button">
        📥
      </button>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal" id="reportModal">
  <div class="modal-content">
    <h2>Generar Reporte</h2>
    <form id="reportForm">
      <label for="startDate">Fecha Inicio:</label>
      <input type="date" id="startDate" name="startDate" required>

      <label for="endDate">Fecha Final:</label>
      <input type="date" id="endDate" name="endDate" required>

      <label for="reportType">Tipo de Reporte:</label>
      <select id="reportType" name="reportType">
        <option value="" disabled selected>Seleccione una opcion</option>
        <option value="consolidated">Consolidado</option>
        <option value="details">Detallado</option>
      </select>

      <div class="modal-buttons">
        <button type="button" class="cancel-button">Cancelar</button>
        <button type="submit" class="ok-button">OK</button>
      </div>
    </form>
  </div>
</div>

<!-- Script JavaScript -->
<script type="module" src="./../../../assets/js/Dashboard/reportes.js"></script>
<script>
  window.endpointGetReportesVentas = "<?php echo $routeParser->urlFor('ventas.get_report'); ?>"
  window.endpointListaZonasUsuario = "<?php echo $routeParser->urlFor('zona_usuarios.list_relational_all', ['id' => $user_id]) ?>";
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layout/layout.php';
?>