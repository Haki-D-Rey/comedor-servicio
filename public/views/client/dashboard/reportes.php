<?php
ob_start();
?>
<style>
  /* General Styles */
  body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
  }

  .container {
    width: 90%;
    max-width: 800px;
    margin: 50px auto;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
  }

  .header {
    text-align: center;
    margin-bottom: 20px;
  }

  .report-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    background-color: #fafafa;
    border-radius: 4px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  }

  .report-title {
    font-size: 18px;
    font-weight: bold;
  }

  .report-date {
    font-size: 14px;
    color: #777;
  }

  .actions {
    display: flex;
    gap: 10px;
  }

  .icon-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 20px;
    color: #007bff;
  }

  .icon-button:hover {
    color: #0056b3;
  }

  /* Modal Styles */
  .modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    justify-content: center;
    align-items: center;
  }

  .modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  }

  .modal-content h2 {
    margin-top: 0;
  }

  .modal-content label {
    display: block;
    margin-top: 10px;
  }

  .modal-content input,
  .modal-content select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  .modal-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
  }

  .modal-buttons button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .ok-button {
    background-color: #28a745;
    color: white;
  }

  .ok-button:hover {
    background-color: #218838;
  }

  .cancel-button {
    background-color: #dc3545;
    color: white;
  }

  .cancel-button:hover {
    background-color: #c82333;
  }

  @media (max-width: 768px) {
    .report-item {
      flex-direction: column;
      align-items: flex-start;
    }

    .actions {
      margin-top: 10px;
    }
  }
</style>

<!-- Contenedor Principal -->
<div class="container">
  <div class="header">
    <h1>Reporte</h1>
  </div>
  <div class="report-item">
    <div>
      <div class="report-title">Reporte de Ventas - Octubre</div>
      <div class="report-date">Fecha: 10/10/2024</div>
    </div>
    <div class="actions">
      <button class="icon-button" onclick="openModal()">
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
      <select id="reportType" name="reportType" required>
        <option value="consolidated">Consolidado</option>
        <option value="details">Detallado</option>
      </select>

      <div class="modal-buttons">
        <button type="button" class="cancel-button" onclick="closeModal()">Cancelar</button>
        <button type="submit" class="ok-button">OK</button>
      </div>
    </form>
  </div>
</div>

<!-- Script JavaScript -->
<script>
  // Abrir el modal
  function openModal() {
    document.getElementById('reportModal').style.display = 'flex';
  }

  // Cerrar el modal
  function closeModal() {
    document.getElementById('reportModal').style.display = 'none';
  }

  // Capturar el formulario de envío
  document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const reportType = document.getElementById('reportType').value;

    alert(`Generando reporte:\n\nFecha Inicio: ${startDate}\nFecha Final: ${endDate}\nTipo: ${reportType}`);

    // Cierra el modal después de enviar
    closeModal();
  });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layout/layout.php';
?>